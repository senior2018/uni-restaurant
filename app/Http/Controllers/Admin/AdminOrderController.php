<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Notifications\OrderAssignedNotification;
use App\Notifications\OrderCancellationDecisionNotification;
use App\Notifications\OrderStatusChangedNotification;

class AdminOrderController extends Controller
{
    // List all orders with filters, search, and pagination
    public function index(Request $request)
    {
        $query = Order::with(['user', 'staff', 'items.meal']);

        // Filters
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('cancellation_requested')) {
            $query->where('cancellation_requested', (bool)$request->cancellation_requested);
        }
        // Support filtering for unassigned staff
        if ($request->filled('staff_id')) {
            if ($request->staff_id === 'unassigned') {
                $query->whereNull('staff_id');
            } else {
                $query->where('staff_id', $request->staff_id);
            }
        }
        if ($request->filled('customer_id')) {
            $query->where('user_id', $request->customer_id);
        }
        // Search by order ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', $search)
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%$search%");
                  })
                  ->orWhereHas('staff', function($sq) use ($search) {
                      $sq->where('name', 'like', "%$search%");
                  });
            });
        }
        $sort = $request->input('sort', 'desc');
        if ($sort === 'asc') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderByDesc('created_at');
        }
        $orders = $query->paginate(15)->withQueryString();

        // Attach similar_orders for all unassigned, pending orders in the page
        $orders->getCollection()->transform(function($order) use ($orders) {
            if ($order->status !== 'pending' || $order->staff_id !== null) {
                $order->similar_orders = [];
                return $order;
            }
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
                    if ($other->status !== 'pending' || $other->staff_id !== null) return false;
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

        // Filter by has_similar if requested
        if ($request->filled('has_similar') && $request->has_similar) {
            $orders->setCollection($orders->getCollection()->filter(function($order) {
                return $order->similar_orders && count($order->similar_orders) > 0;
            })->values());
        }

        $staff = User::where('role', 'staff')->get(['id', 'name']);
        $customers = User::where('role', 'customer')->get(['id', 'name']);
        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'staff' => $staff,
            'customers' => $customers,
            'filters' => [
                'status' => $request->status ?? 'all',
                'staff_id' => $request->staff_id ?? '',
                'customer_id' => $request->customer_id ?? '',
                'search' => $request->search ?? '',
            ],
        ]);
    }

    // Assign or reassign staff to an order
    public function assignStaff(Request $request, $orderId)
    {
        $request->validate([
            'staff_id' => 'required|exists:users,id',
        ]);
        $order = Order::findOrFail($orderId);
        $order->staff_id = $request->staff_id;
        $order->save();
        // Notify staff
        $staff = \App\Models\User::find($request->staff_id);
        if ($staff) {
            $staff->notify(new OrderAssignedNotification($order, $request->user()->name));
        }
        // Check if staff has more than 5 pending orders (after assignment)
        $pendingCount = Order::where('staff_id', $request->staff_id)
            ->where('status', 'pending')
            ->count();
        $warning = null;
        if ($pendingCount > 5) {
            $warning = 'Warning: This staff now has more than 5 pending orders.';
        }
        return redirect()->back()
            ->with('success', 'Staff assigned to order successfully.')
            ->with('warning', $warning);
    }

    // Show order details (optional, for details page)
    public function show($orderId)
    {
        $order = Order::with(['user', 'staff', 'items.meal'])->findOrFail($orderId);
        return Inertia::render('Admin/Orders/Show', [
            'order' => $order
        ]);
    }

    // Approve cancellation request (admin)
    public function approveCancellation(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('cancellation_requested', true)
            ->firstOrFail();
        $order->status = 'cancelled';
        $order->cancellation_requested = false;
        $order->cancelled_by = 'admin';
        $order->save();
        // Notify customer, staff, and admin
        if ($order->user) {
            $order->user->notify(new OrderCancellationDecisionNotification($order, 'accepted', 'admin', $request->user()->name));
        }
        if ($order->staff) {
            $order->staff->notify(new OrderCancellationDecisionNotification($order, 'accepted', 'admin', $request->user()->name));
        }
        foreach (\App\Models\User::where('role', 'admin')->get() as $admin) {
            $admin->notify(new OrderCancellationDecisionNotification($order, 'accepted', 'admin', $request->user()->name));
        }
        return redirect()->back()->with('success', 'Order cancelled.');
    }

    // Reject cancellation request (admin)
    public function rejectCancellation(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('cancellation_requested', true)
            ->firstOrFail();
        $order->cancellation_requested = false;
        $order->cancellation_reason = null;
        $order->save();
        // Notify customer, staff, and admin
        if ($order->user) {
            $order->user->notify(new OrderCancellationDecisionNotification($order, 'rejected', 'admin', $request->user()->name));
        }
        if ($order->staff) {
            $order->staff->notify(new OrderCancellationDecisionNotification($order, 'rejected', 'admin', $request->user()->name));
        }
        foreach (\App\Models\User::where('role', 'admin')->get() as $admin) {
            $admin->notify(new OrderCancellationDecisionNotification($order, 'rejected', 'admin', $request->user()->name));
        }
        return redirect()->back()->with('success', 'Cancellation request rejected.');
    }

    public function markCancellationSeen(Request $request)
    {
        \App\Models\Order::where('cancellation_requested', true)
            ->where('cancellation_request_seen', false)
            ->update(['cancellation_request_seen' => true]);
        return response()->json(['success' => true]);
    }

    public function pendingCancellations(Request $request)
    {
        $orders = Order::with(['user', 'staff', 'items.meal'])
            ->where('status', 'preparing')
            ->where('cancellation_requested', true)
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();
        $staff = User::where('role', 'staff')->get(['id', 'name']);
        $customers = User::where('role', 'customer')->get(['id', 'name']);
        return Inertia::render('Admin/PendingCancellations', [
            'orders' => $orders,
            'staff' => $staff,
            'customers' => $customers,
        ]);
    }

    // Admin can update the status of any order
    public function updateStatus(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,delivered,cancelled',
        ]);
        $oldStatus = $order->status;
        $order->status = $validated['status'];
        $order->save();
        // Notify customer
        if ($order->user) {
            $order->user->notify(new OrderStatusChangedNotification($order, $oldStatus, $validated['status']));
        }
        return redirect()->back()->with('success', 'Order status updated by admin.');
    }
}
