<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Alert>
 */
class AlertFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $customerIds = \App\Models\User::where('role', 'customer')->pluck('id');
        $staffIds = User::where('role', 'staff')->pluck('id')->toArray();

        return [
            'user_id' => $this->faker->randomElement($customerIds),
            'order_id' => \App\Models\Order::inRandomOrder()->first()->id,
            'reason' => $this->faker->sentence(),
            'resolved' => $resolved = $this->faker->boolean(30),
            'staff_id' => $resolved ? $this->faker->randomElement($staffIds) : null,
            'staff_response' => $resolved ? $this->faker->sentence() : null,
            'responded_at' => $resolved ? now()->subDays(rand(0, 7)) : null,
        ];
    }
}
