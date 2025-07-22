<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

class DatabaseNotificationFactory extends Factory
{
    protected $model = \Illuminate\Notifications\DatabaseNotification::class;

    public function definition(): array
    {
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        $types = ['alert', 'alert_response', 'rating', 'rating_response'];
        $type = $this->faker->randomElement($types);
        $data = match ($type) {
            'alert' => [
                'type' => 'alert',
                'alert_id' => $this->faker->uuid(),
                'order_id' => $this->faker->numberBetween(1, 20),
                'user_id' => $user->id,
                'reason' => $this->faker->sentence(),
                'created_at' => now(),
            ],
            'alert_response' => [
                'type' => 'alert_response',
                'alert_id' => $this->faker->uuid(),
                'order_id' => $this->faker->numberBetween(1, 20),
                'staff_id' => $user->id,
                'staff_response' => $this->faker->sentence(),
                'responded_at' => now(),
            ],
            'rating' => [
                'type' => 'rating',
                'rating_id' => $this->faker->uuid(),
                'order_id' => $this->faker->numberBetween(1, 20),
                'meal_id' => $this->faker->numberBetween(1, 20),
                'user_id' => $user->id,
                'rating' => $this->faker->numberBetween(1, 5),
                'comment' => $this->faker->sentence(),
                'created_at' => now(),
            ],
            'rating_response' => [
                'type' => 'rating_response',
                'rating_id' => $this->faker->uuid(),
                'order_id' => $this->faker->numberBetween(1, 20),
                'meal_id' => $this->faker->numberBetween(1, 20),
                'staff_id' => $user->id,
                'response_comment' => $this->faker->sentence(),
                'responded_at' => now(),
            ],
        };
        return [
            'id' => Str::uuid(),
            'type' => 'App\\Notifications\\GenericNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => $data,
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
