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

        return [
            'user_id' => $this->faker->randomElement($customerIds), // Create a new user for the order
            'staff_id' => $this->faker->optional(0.5)->randomElement($staffIds->isEmpty() ? [null] : $staffIds), // 50% assigned, 50% unassigned
            'total_price' => 0, // Set to 0 initially, will be updated after creating order items
            'status'=> $this->faker->randomElement(['pending', 'preparing', 'delivered', 'cancelled']),
            'payment_method' => $this->faker->randomElement(['cash', 'mobile_money', 'card']),
            'delivery_location' => $this-> faker->address(),
            'staff_notes' => $this->faker->optional(0.3)->sentence(),
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
