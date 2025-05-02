<?php

namespace Database\Seeders;

use App\Models\Meal;
use Illuminate\Database\Seeder;

class MealSeeder extends Seeder
{
    public function run()
    {
        // Create exactly 20 meals (matches dishes array length)
        Meal::factory()
            ->count(20)
            ->create();
    }
}
