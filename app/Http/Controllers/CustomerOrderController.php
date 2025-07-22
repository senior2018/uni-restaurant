<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Notifications\NewOrderPlacedNotification;
use App\Models\User;

class CustomerOrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|integer|exists:meals,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.price' => 'required|numeric|min:0',
            'delivery_location' => 'required|string|max:255',
            'payment_method' => 'required|in:cash,mobile_money',
        ]);

        $user = $request->user();

        DB::beginTransaction();
        try {
            $total = collect($validated['cart'])->sum(function ($item) {
                return $item['quantity'] * (float)($item['price'] ?? 0);
            });
            // Auto-assign staff with <5 pending and <5 preparing orders, prefer similarity
            $orderMealIds = collect($validated['cart'])->pluck('id')->unique()->toArray();
            $staffCandidates = \App\Models\User::where('role', 'staff')
                ->with(['orders' => function($q) {
                    $q->whereIn('status', ['pending', 'preparing']);
                }])
                ->get()
                ->filter(function($staff) {
                    $pending = $staff->orders->where('status', 'pending')->count();
                    $preparing = $staff->orders->where('status', 'preparing')->count();
                    return $pending < 5 && $preparing < 5;
                });
            $staffToAssign = null;
            $maxSimilarity = -1;
            foreach ($staffCandidates as $staff) {
                $staffMealIds = $staff->orders->flatMap(function($order) {
                    return $order->items->pluck('meal_id');
                })->unique()->toArray();
                $similarity = count(array_intersect($orderMealIds, $staffMealIds));
                if ($similarity > $maxSimilarity) {
                    $maxSimilarity = $similarity;
                    $staffToAssign = $staff;
                }
            }
            $orderData = [
                'user_id' => $user->id,
                'delivery_location' => $validated['delivery_location'],
                'payment_method' => $validated['payment_method'],
                'status' => 'pending',
                'total_price' => $total,
            ];
            if ($staffToAssign) {
                $orderData['staff_id'] = $staffToAssign->id;
            }
            $order = Order::create($orderData);

            foreach ($validated['cart'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'meal_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'] ?? 0,
                ]);
            }

            DB::commit();
            // Notify all staff and admins
            $staffAndAdmins = User::whereIn('role', ['staff', 'admin'])->get();
            foreach ($staffAndAdmins as $recipient) {
                $recipient->notify(new NewOrderPlacedNotification($order, $user->name));
            }
            // Redirect to My Orders with success flash message
            return redirect()->route('customer.orders')->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            Log::error('Order placement failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('checkout')->with('error', 'Order failed. Please try again.');
        }
    }

    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->with(['items.meal'])
            ->orderByDesc('created_at')
            ->get();

        return inertia('Customer/MyOrders', [
            'orders' => $orders,
            'user' => $request->user(),
        ]);
    }

    public function dashboard(Request $request)
    {
        $orders = $request->user()->orders()->orderByDesc('created_at')->get();
        return inertia('Customer/Dashboard', [
            'user' => $request->user(),
            'orders' => $orders,
        ]);
    }

    public function cancel(Request $request, $orderId)
    {
        $order = $request->user()->orders()->where('id', $orderId)->firstOrFail();
        if ($order->status === 'pending') {
            $order->status = 'cancelled';
            $order->cancellation_reason = $request->input('reason');
            $order->cancelled_by = 'customer';
            $order->save();
            return redirect()->back()->with('success', 'Order cancelled successfully.');
        }
        if ($order->status === 'preparing') {
            if ($order->cancellation_requested) {
                return redirect()->back()->with('error', 'Cancellation already requested for this order.');
            }
            $request->validate(['reason' => 'required|string|min:5']);
            $order->cancellation_requested = true;
            $order->cancellation_reason = $request->input('reason');
            $order->save();
            return redirect()->back()->with('success', 'Cancellation request submitted. Staff or admin will review it.');
        }
        return redirect()->back()->with('error', 'Only pending orders can be cancelled directly. For preparing orders, you may request cancellation.');
    }

    public function cancelRequest(Request $request, $orderId)
    {
        $order = $request->user()->orders()->where('id', $orderId)->firstOrFail();
        if ($order->cancellation_requested && $order->status === 'preparing') {
            $order->cancellation_requested = false;
            $order->cancellation_reason = null;
            $order->save();
            return redirect()->back()->with('success', 'Cancellation request withdrawn.');
        }
        return redirect()->back()->with('error', 'No pending cancellation request to withdraw.');
    }

    public function update(Request $request, $orderId)
    {
        $order = $request->user()->orders()->where('id', $orderId)->with('items')->firstOrFail();
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending orders can be edited.');
        }
        $validated = $request->validate([
            'delivery_location' => 'required|string|max:255',
            'payment_method' => 'required|in:cash,mobile_money',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:order_items,id',
            'items.*.meal_id' => 'required|integer|exists:meals,id',
            'items.*.quantity' => 'required|integer|min:0',
            'items.*.price' => 'required|numeric|min:0',
        ]);
        // Update or remove items
        $newItems = collect($validated['items']);
        $total = 0;
        foreach ($order->items as $item) {
            $new = $newItems->firstWhere('id', $item->id);
            if ($new) {
                if ($new['quantity'] > 0) {
                    $item->quantity = $new['quantity'];
                    $item->price = $new['price'];
                    $item->save();
                    $total += $item->quantity * $item->price;
                } else {
                    $item->delete();
                }
            }
        }
        // If all items removed, cancel the order
        if ($order->items()->count() === 0) {
            $order->status = 'cancelled';
            $order->save();
            return redirect()->back()->with('success', 'Order cancelled because all items were removed.');
        }
        $order->delivery_location = $validated['delivery_location'];
        $order->payment_method = $validated['payment_method'];
        $order->total_price = $total;
        $order->save();
        return redirect()->back()->with('success', 'Order updated successfully.');
    }
}
