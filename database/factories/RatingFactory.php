<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rating>
 */
class RatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $customerIds = \App\Models\User::where('role', 'customer')->pluck('id');

        return [
            'user_id' => $this->faker->randomElement($customerIds),
            'order_id' => \App\Models\Order::inRandomOrder()->first()->id,
            'meal_id' => \App\Models\Meal::inRandomOrder()->first()->id,
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->optional(0.7)->sentence(),
        ];
    }
}
