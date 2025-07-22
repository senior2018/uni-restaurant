<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Alert;
use App\Models\Order;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Notifications\Notification;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            MealCategorySeeder::class,
            MealSeeder::class,
            UserSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
            RatingSeeder::class,
            AlertSeeder::class,
            OtpVerificationsSeeder::class,
            NotificationSeeder::class,
        ]);

        // Seed notifications for testing
        $user = User::first();
        $order = Order::first();
        if ($user && $order) {
            $alert = Alert::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'reason' => 'Test alert seeded',
                'resolved' => false,
            ]);
            $user->notify(new \App\Notifications\NewAlertNotification($alert));
        }
    }
}
