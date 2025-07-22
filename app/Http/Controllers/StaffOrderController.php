<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\OrderStatusChangedNotification;
use App\Notifications\OrderCancellationDecisionNotification;

class StaffOrderController extends Controller
{
    // Staff claims an unassigned order
    public function claim(Request $request, $orderId)
    {
        $user = $request->user();
        // Enforce pending limit
        $pendingCount = Order::where('staff_id', $user->id)->where('status', 'pending')->count();
        if ($pendingCount >= 5) {
            return response()->json(['success' => false, 'message' => 'You cannot claim more orders. You already have 5 pending orders.'], 422);
        }
        $order = Order::where('id', $orderId)
            ->whereNull('staff_id')
            ->where('status', '!=', 'cancelled')
            ->firstOrFail();
        $order->staff_id = $user->id;
        $order->save();
        return response()->json(['success' => true, 'message' => 'Order claimed successfully.']);
    }

    // Staff updates the status of their assigned order
    public function updateStatus(Request $request, $orderId)
    {
        $user = $request->user();
        $order = Order::where('id', $orderId)
            ->where('staff_id', $user->id)
            ->where('status', '!=', 'cancelled')
            ->firstOrFail();

        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,delivered,cancelled',
        ]);

        // Prevent preparing/delivered without staff assignment
        if (in_array($validated['status'], ['preparing', 'delivered']) && !$order->staff_id) {
            return response()->json(['success' => false, 'message' => 'Order must be assigned to a staff before changing to preparing or delivered.'], 422);
        }
        // Enforce preparing limit
        if ($validated['status'] === 'preparing') {
            $preparingCount = Order::where('staff_id', $user->id)->where('status', 'preparing')->count();
            if ($preparingCount >= 5) {
                return response()->json(['success' => false, 'message' => 'You cannot have more than 5 preparing orders at the same time.'], 422);
            }
        }

        $oldStatus = $order->status;
        $order->status = $validated['status'];
        $order->save();
        // Notify customer
        if ($order->user) {
            $order->user->notify(new OrderStatusChangedNotification($order, $oldStatus, $validated['status']));
        }
        return response()->json(['success' => true, 'message' => 'Order status updated.', 'status' => $order->status]);
    }

    // Show all orders assigned to the current staff
    public function myOrders(Request $request)
    {
        $user = $request->user();
        $query = \App\Models\Order::with(['user', 'items.meal'])
            ->where('staff_id', $user->id)
            ->where('status', '!=', 'cancelled');

        // Filters
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        // Search by order ID or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', $search)
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%$search%");
                  });
            });
        }
        $orders = $query->orderByDesc('created_at')->paginate(10)->withQueryString();
        return inertia('Staff/MyOrders', [
            'user' => $user,
            'orders' => $orders,
            'filters' => [
                'status' => $request->status ?? 'all',
                'from_date' => $request->from_date ?? null,
                'to_date' => $request->to_date ?? null,
                'search' => $request->search ?? '',
            ],
        ]);
    }

    // Show all unassigned, pending orders
    public function unassignedOrders(Request $request)
    {
        $user = $request->user();
        $query = \App\Models\Order::with(['user', 'items.meal'])
            ->whereNull('staff_id')
            ->where('status', 'pending');
        // Filters
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        // Search by order ID or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', $search)
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%$search%");
                  });
            });
        }
        $orders = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

        // --- Suggested for you logic ---
        $suggestedOrders = [];
        if ($user) {
            // Get meal IDs from staff's current pending+preparing orders
            $myMealIds = \App\Models\Order::where('staff_id', $user->id)
                ->whereIn('status', ['pending', 'preparing'])
                ->with('items')
                ->get()
                ->flatMap(function($order) {
                    return $order->items->pluck('meal_id');
                })->unique()->toArray();

            // Get all unassigned pending orders (not paginated, for suggestion)
            $allUnassigned = \App\Models\Order::with(['user', 'items.meal'])
                ->whereNull('staff_id')
                ->where('status', 'pending')
                ->get();

            // Score each order by meal overlap and collect similar meals
            $scored = $allUnassigned->map(function($order) use ($myMealIds) {
                $orderMealIds = $order->items->pluck('meal_id')->unique()->toArray();
                $similarIds = array_intersect($myMealIds, $orderMealIds);
                $similarity = count($similarIds);
                $order->similarity_score = $similarity;
                // Attach similar meal objects (id, name)
                $order->similar_meals = $order->items->filter(function($item) use ($similarIds) {
                    return in_array($item->meal_id, $similarIds);
                })->map(function($item) {
                    return [
                        'id' => $item->meal->id,
                        'name' => $item->meal->name
                    ];
                })->unique('id')->values()->all();
                return $order;
            });
            // Only suggest those with at least 1 overlap, sort by similarity desc, limit 5
            $suggestedOrders = $scored->filter(function($order) {
                return $order->similarity_score > 0;
            })->sortByDesc('similarity_score')->take(5)->values()->all();
        }

        // For each order in the paginated list, find similar orders (>=50% meal overlap, not itself)
        $orders->getCollection()->transform(function($order) use ($orders) {
            $orderMealIds = $order->items->pluck('meal_id')->unique()->toArray();
            $orderMealCount = count($orderMealIds);
            if ($orderMealCount === 0) {
                $order->similar_orders = [];
                return $order;
            }
            $minOverlap = (int) ceil($orderMealCount / 2); // at least half, rounded up
            $similar = collect($orders->getCollection())
                ->filter(function($other) use ($order, $orderMealIds, $minOverlap) {
                    if ($other->id === $order->id) return false;
                    $otherMealIds = $other->items->pluck('meal_id')->unique()->toArray();
                    $overlap = count(array_intersect($orderMealIds, $otherMealIds));
                    return $overlap >= $minOverlap;
                })
                ->map(function($other) use ($orderMealIds) {
                    $otherMealIds = $other->items->pluck('meal_id')->unique()->toArray();
                    $similarity_score = count(array_intersect($orderMealIds, $otherMealIds));
                    return [
                        'id' => $other->id,
                        'created_at' => $other->created_at,
                        'user' => $other->user ? ['id' => $other->user->id, 'name' => $other->user->name] : null,
                        'meals' => $other->items->map(function($item) {
                            return [
                                'id' => $item->meal->id,
                                'name' => $item->meal->name,
                                'quantity' => $item->quantity,
                                'price' => $item->price
                            ];
                        })->values()->all(),
                        'total_price' => $other->total_price,
                        'delivery_location' => $other->delivery_location,
                        'payment_method' => $other->payment_method,
                        'similarity_score' => $similarity_score,
                    ];
                })
                ->sortByDesc('similarity_score')
                ->values()->all();
            $order->similar_orders = $similar;
            return $order;
        });

        return inertia('Staff/UnassignedOrders', [
            'user' => $user,
            'orders' => $orders,
            'filters' => [
                'from_date' => $request->from_date ?? null,
                'to_date' => $request->to_date ?? null,
                'search' => $request->search ?? '',
            ],
            'suggestedOrders' => $suggestedOrders,
        ]);
    }

    // Approve cancellation request
    public function approveCancellation(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('cancellation_requested', true)
            ->where('staff_id', $request->user()->id)
            ->firstOrFail();
        $order->status = 'cancelled';
        $order->cancellation_requested = false;
        $order->cancelled_by = 'staff';
        $order->save();
        // Notify customer, staff, and admin
        if ($order->user) {
            $order->user->notify(new OrderCancellationDecisionNotification($order, 'accepted', 'staff', $request->user()->name));
        }
        if ($order->staff) {
            $order->staff->notify(new OrderCancellationDecisionNotification($order, 'accepted', 'staff', $request->user()->name));
        }
        foreach (\App\Models\User::where('role', 'admin')->get() as $admin) {
            $admin->notify(new OrderCancellationDecisionNotification($order, 'accepted', 'staff', $request->user()->name));
        }
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Order cancelled.']);
        }
        return redirect()->back()->with('success', 'Order cancelled.');
    }

    // Reject cancellation request
    public function rejectCancellation(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('cancellation_requested', true)
            ->where('staff_id', $request->user()->id)
            ->firstOrFail();
        $order->cancellation_requested = false;
        $order->cancellation_reason = null;
        $order->save();
        // Notify customer, staff, and admin
        if ($order->user) {
            $order->user->notify(new OrderCancellationDecisionNotification($order, 'rejected', 'staff', $request->user()->name));
        }
        if ($order->staff) {
            $order->staff->notify(new OrderCancellationDecisionNotification($order, 'rejected', 'staff', $request->user()->name));
        }
        foreach (\App\Models\User::where('role', 'admin')->get() as $admin) {
            $admin->notify(new OrderCancellationDecisionNotification($order, 'rejected', 'staff', $request->user()->name));
        }
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Cancellation request rejected.']);
        }
        return redirect()->back()->with('success', 'Cancellation request rejected.');
    }

    public function markCancellationSeen(Request $request)
    {
        $user = $request->user();
        \App\Models\Order::where('staff_id', $user->id)
            ->where('cancellation_requested', true)
            ->where('cancellation_request_seen', false)
            ->update(['cancellation_request_seen' => true]);
        return response()->json(['success' => true]);
    }

    public function pendingCancellations(Request $request)
    {
        $user = $request->user();
        $orders = \App\Models\Order::where('staff_id', $user->id)
            ->where('status', 'preparing')
            ->where('cancellation_requested', true)
            ->orderByDesc('created_at')
            ->get();
        // Mark all as seen
        \App\Models\Order::where('staff_id', $user->id)
            ->where('status', 'preparing')
            ->where('cancellation_requested', true)
            ->where('cancellation_request_seen', false)
            ->update(['cancellation_request_seen' => true]);
        $unseenCancellationCount = \App\Models\Order::where('staff_id', $user->id)
            ->where('status', 'preparing')
            ->where('cancellation_requested', true)
            ->where('cancellation_request_seen', false)
            ->count();
        return inertia('Staff/PendingCancellations', [
            'orders' => $orders,
            'unseenCancellationCount' => $unseenCancellationCount,
        ]);
    }
}
