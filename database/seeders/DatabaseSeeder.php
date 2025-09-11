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
        // Check if we're in production/deployment mode
        if (app()->environment('production') || config('app.env') === 'production') {
            $this->call([
                DeploymentSeeder::class,
            ]);
        } else {
            // Development seeding - use DeploymentSeeder for consistency
            $this->call([
                DeploymentSeeder::class,
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
}
