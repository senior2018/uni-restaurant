<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        // Create a new meal
        $meal = \App\Models\Meal::inRandomOrder() -> first() ?? \App\Models\Meal::factory()->create(); // Get a random meal or create one if none exist

        return [
            'order_id' => \App\Models\Order::factory(), // Create a new order for the order item
            'meal_id' => $meal->id, // Create a new meal for the order item
            'quantity' => $this->faker->numberBetween(1, 3), // Random quantity between 1 and 5
            'price' => $this->faker->randomFloat(2, 5, 50), // Random price between 5 and 50
        ];
    }
}
