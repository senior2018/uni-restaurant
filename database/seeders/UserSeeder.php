<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 1 admin (fixed credentials)
    User::create([
        'name' => 'Admin User',
        'email' => 'admin@university.com',
        'password' => Hash::make('password'),
        'phone' => '+255700000000',
        'permanent_location' => 'Administration Block',
        'role' => 'admin'
    ]);

    // Create 3 staff members (fixed credentials)
    foreach (range(1, 3) as $i) {
        User::create([
            'name' => "Staff Member $i",
            'email' => "staff$i@university.com",
            'password' => Hash::make('password'),
            'phone' => '+25570000000' . $i,
            'permanent_location' => 'Staff Quarters',
            'role' => 'staff'
        ]);
    }

    // Create 50 customers (randomized)
    User::factory()
        ->count(50)
        ->create([
            'role' => 'customer',
            'password' => Hash::make('password') // Uniform password
        ]);
    }
}
