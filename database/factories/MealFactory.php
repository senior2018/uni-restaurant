<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MealFactory extends Factory
{
    // Common Tanzanian university restaurant dishes
    protected $dishes = [
        'Ugali Maharage', 'Wali Maharage', 'Chips Mayai', 'Ndizi Kaanga',
        'Samaki wa Kupaka', 'Mchuzi wa Kuku', 'Chapati', 'Supu ya Ndizi',
        'Mchemsho', 'Kisamvu na Nyama', 'Matoke', 'Mkate wa Sinia',
        'Pilau', 'Beef Stew', 'Vegetable Curry', 'Rice and Beans',
        'Grilled Chicken', 'Fish Curry', 'Mandazi', 'Katogo'
    ];

    protected $currentDishIndex = 0;

    public function definition(): array
    {
        // Get the next dish name in sequence
        $dish = $this->dishes[$this->currentDishIndex % count($this->dishes)];
        $this->currentDishIndex++;

        return [
            'name' => $dish,
            'price' => $this->faker->randomFloat(2, 1500, 10000), // 1,500 to 10,000 TZS
            'category_id' => $this->faker->numberBetween(1, 6), // Match category count
            'description' => $this->faker->sentence(),
            'image_url' => $this->faker->imageUrl(640, 480, 'food', true, 'university meal'),
            'is_available' => $this->faker->boolean(90)
        ];
    }
}
