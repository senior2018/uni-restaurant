<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $customerIds = \App\Models\User::where('role', 'customer')->pluck('id');
        $staffIds = \App\Models\User::where('role', 'staff')->pluck('id');

        $staffId = $this->faker->optional(0.5)->randomElement($staffIds->isEmpty() ? [null] : $staffIds);
        $status = $staffId ? $this->faker->randomElement(['pending', 'preparing', 'delivered', 'cancelled']) : $this->faker->randomElement(['pending', 'cancelled']);
        $isCancelled = $status === 'cancelled';
        $cancelledBy = $isCancelled ? $this->faker->randomElement(['customer', 'staff', 'admin']) : null;
        $cancellationReason = $isCancelled ? $this->faker->sentence() : null;
        $cancellationRequested = false;
        $cancellationRequestSeen = true;
        if ($isCancelled) {
            $cancellationRequested = false;
            $cancellationRequestSeen = true;
        } else if ($this->faker->boolean(10)) { // 10% chance to have a pending request
            $cancellationRequested = true;
            $cancellationRequestSeen = false;
        }
        return [
            'user_id' => $this->faker->randomElement($customerIds),
            'staff_id' => $staffId,
            'total_price' => 0,
            'status'=> $status,
            'payment_method' => $this->faker->randomElement(['cash', 'mobile_money', 'card']),
            'delivery_location' => $this->faker->address(),
            'staff_notes' => $this->faker->optional(0.3)->sentence(),
            'cancellation_requested' => $cancellationRequested,
            'cancellation_reason' => $cancellationReason,
            'cancelled_by' => $cancelledBy,
            'cancellation_request_seen' => $cancellationRequestSeen,
        ];
    }

    //Configure order with items after creation
    public function configure(): static
    {
        return $this->afterCreating(function (\App\Models\Order $order) {
            // Create 1 to 5 order items for the order
            \App\Models\OrderItem::factory()
                ->count($this->faker->numberBetween(1, 5))
                ->for($order) // Associate the order with the order items
                ->create();

            $total = $order->items->sum(function ($item) {
                return $item->meal->price * $item->quantity;
            });

            $order->update(['total_price' => $total]); // Update the order's total price
        });
    }
}
