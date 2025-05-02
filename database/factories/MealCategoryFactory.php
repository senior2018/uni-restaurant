<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MealCategoryFactory extends Factory
{
    // Common Tanzanian university meal categories
    protected $categories = [
        'Starches', 'Proteins', 'Vegetables',
        'Breakfast', 'Beverages', 'Specials'
    ];

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement($this->categories),
        ];
    }
}
