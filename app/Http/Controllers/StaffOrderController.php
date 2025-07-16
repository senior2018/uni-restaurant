<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffOrderController extends Controller
{
    // Staff claims an unassigned order
    public function claim(Request $request, $orderId)
    {
        $user = $request->user();
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

        $order->status = $validated['status'];
        $order->save();

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
        return inertia('Staff/UnassignedOrders', [
            'user' => $request->user(),
            'orders' => $orders,
            'filters' => [
                'from_date' => $request->from_date ?? null,
                'to_date' => $request->to_date ?? null,
                'search' => $request->search ?? '',
            ],
        ]);
    }

    // Approve cancellation request
    public function approveCancellation(Request $request, $orderId)
    {
        $user = $request->user();
        $order = Order::where('id', $orderId)
            ->where('staff_id', $user->id)
            ->where('cancellation_requested', true)
            ->firstOrFail();
        $order->status = 'cancelled';
        $order->cancellation_requested = false;
        $order->cancelled_by = 'staff';
        $order->save();
        return response()->json(['success' => true, 'message' => 'Order cancelled.']);
    }

    // Reject cancellation request
    public function rejectCancellation(Request $request, $orderId)
    {
        $user = $request->user();
        $order = Order::where('id', $orderId)
            ->where('staff_id', $user->id)
            ->where('cancellation_requested', true)
            ->firstOrFail();
        $order->cancellation_requested = false;
        $order->cancellation_reason = null;
        $order->save();
        return response()->json(['success' => true, 'message' => 'Cancellation request rejected.']);
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
            ->where('cancellation_requested', true)
            ->orderByDesc('created_at')
            ->get();
        // Mark all as seen
        \App\Models\Order::where('staff_id', $user->id)
            ->where('cancellation_requested', true)
            ->where('cancellation_request_seen', false)
            ->update(['cancellation_request_seen' => true]);
        $unseenCancellationCount = \App\Models\Order::where('staff_id', $user->id)
            ->where('cancellation_requested', true)
            ->where('cancellation_request_seen', false)
            ->count();
        return inertia('Staff/PendingCancellations', [
            'orders' => $orders,
            'unseenCancellationCount' => $unseenCancellationCount,
        ]);
    }
}
