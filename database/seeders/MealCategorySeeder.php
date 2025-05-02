<?php

namespace Database\Seeders;

use App\Models\MealCategory;
use Illuminate\Database\Seeder;

class MealCategorySeeder extends Seeder
{
    public function run()
    {
        // Create exactly 6 categories (matches array length)
        foreach (['Starches', 'Proteins', 'Vegetables', 'Breakfast', 'Beverages', 'Specials'] as $category) {
            MealCategory::create(['name' => $category]);
        }
    }
}
