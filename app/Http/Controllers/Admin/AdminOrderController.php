<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
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
        $orders = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
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
        return redirect()->back()->with('success', 'Staff assigned to order successfully.');
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
        return redirect()->back()->with('success', 'Cancellation request rejected.');
    }

    public function markCancellationSeen(Request $request)
    {
        \App\Models\Order::where('cancellation_requested', true)
            ->where('cancellation_request_seen', false)
            ->update(['cancellation_request_seen' => true]);
        return response()->json(['success' => true]);
    }
}
