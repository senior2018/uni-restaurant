<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::take(10)->get();
        foreach ($users as $user) {
            // Alert notification
            DatabaseNotification::create([
                'id' => Str::uuid(),
                'type' => 'App\\Notifications\\NewAlertNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'type' => 'alert',
                    'alert_id' => Str::uuid(),
                    'order_id' => 1,
                    'user_id' => $user->id,
                    'reason' => 'Seeded alert for testing',
                    'created_at' => now(),
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            // Rating notification
            DatabaseNotification::create([
                'id' => Str::uuid(),
                'type' => 'App\\Notifications\\NewRatingNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'type' => 'rating',
                    'rating_id' => Str::uuid(),
                    'order_id' => 1,
                    'meal_id' => 1,
                    'user_id' => $user->id,
                    'rating' => rand(1, 5),
                    'comment' => 'Seeded rating for testing',
                    'created_at' => now(),
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
