<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CustomerOrderController extends Controller
{
    public function store(Request $request)
    {
        Log::error('Test log entry from CustomerOrderController@store');
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
            Log::error('Cart data for order: ' . json_encode($validated['cart']));
            $total = collect($validated['cart'])->sum(function ($item) {
                return $item['quantity'] * (float)($item['price'] ?? 0);
            });
            Log::error('Calculated total: ' . $total);
            $order = Order::create([
                'user_id' => $user->id,
                'delivery_location' => $validated['delivery_location'],
                'payment_method' => $validated['payment_method'],
                'status' => 'pending',
                'total_price' => $total,
            ]);

            foreach ($validated['cart'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'meal_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'] ?? 0,
                ]);
            }

            DB::commit();
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
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending orders can be cancelled.');
        }
        $order->status = 'cancelled';
        $order->save();
        return redirect()->back()->with('success', 'Order cancelled successfully.');
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
