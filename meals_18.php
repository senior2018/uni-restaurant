        $meals = [
            // Starches Category (3 meals: 2 available, 1 unavailable)
            [
                'name' => 'Ugali Maharage',
                'description' => 'Traditional maize meal with beans',
                'price' => 2500.00,
                'category_id' => $starches->id,
                'is_available' => true,
            ],
            [
                'name' => 'Wali Maharage',
                'description' => 'Rice with beans',
                'price' => 3000.00,
                'category_id' => $starches->id,
                'is_available' => true,
            ],
            [
                'name' => 'Pilau',
                'description' => 'Spiced rice with meat',
                'price' => 4000.00,
                'category_id' => $starches->id,
                'is_available' => false,
            ],

            // Proteins Category (3 meals: 2 available, 1 unavailable)
            [
                'name' => 'Samaki wa Kupaka',
                'description' => 'Grilled fish with coconut sauce',
                'price' => 5000.00,
                'category_id' => $proteins->id,
                'is_available' => true,
            ],
            [
                'name' => 'Mchuzi wa Kuku',
                'description' => 'Chicken curry',
                'price' => 4500.00,
                'category_id' => $proteins->id,
                'is_available' => true,
            ],
            [
                'name' => 'Beef Stew',
                'description' => 'Tender beef in rich gravy',
                'price' => 6000.00,
                'category_id' => $proteins->id,
                'is_available' => false,
            ],

            // Vegetables Category (3 meals: 2 available, 1 unavailable)
            [
                'name' => 'Vegetable Curry',
                'description' => 'Mixed vegetables in curry sauce',
                'price' => 2000.00,
                'category_id' => $vegetables->id,
                'is_available' => true,
            ],
            [
                'name' => 'Kisamvu na Nyama',
                'description' => 'Cassava leaves with meat',
                'price' => 3500.00,
                'category_id' => $vegetables->id,
                'is_available' => true,
            ],
            [
                'name' => 'Mchemsho',
                'description' => 'Mixed vegetable stew',
                'price' => 2500.00,
                'category_id' => $vegetables->id,
                'is_available' => false,
            ],

            // Breakfast Category (3 meals: 2 available, 1 unavailable)
            [
                'name' => 'Chips Mayai',
                'description' => 'French fries with eggs',
                'price' => 3000.00,
                'category_id' => $breakfast->id,
                'is_available' => true,
            ],
            [
                'name' => 'Chapati',
                'description' => 'Flatbread with tea',
                'price' => 1500.00,
                'category_id' => $breakfast->id,
                'is_available' => true,
            ],
            [
                'name' => 'Mandazi',
                'description' => 'Sweet fried bread',
                'price' => 1000.00,
                'category_id' => $breakfast->id,
                'is_available' => false,
            ],

            // Beverages Category (3 meals: 2 available, 1 unavailable)
            [
                'name' => 'Chai',
                'description' => 'Traditional spiced tea',
                'price' => 500.00,
                'category_id' => $beverages->id,
                'is_available' => true,
            ],
            [
                'name' => 'Fresh Juice',
                'description' => 'Fresh fruit juice',
                'price' => 2000.00,
                'category_id' => $beverages->id,
                'is_available' => true,
            ],
            [
                'name' => 'Coffee',
                'description' => 'Freshly brewed coffee',
                'price' => 1000.00,
                'category_id' => $beverages->id,
                'is_available' => false,
            ],

            // Specials Category (3 meals: 2 available, 1 unavailable)
            [
                'name' => 'Katogo',
                'description' => 'Traditional mixed dish',
                'price' => 4000.00,
                'category_id' => $specials->id,
                'is_available' => true,
            ],
            [
                'name' => 'Matoke',
                'description' => 'Steamed green bananas',
                'price' => 3000.00,
                'category_id' => $specials->id,
                'is_available' => true,
            ],
            [
                'name' => 'Supu ya Ndizi',
                'description' => 'Banana soup',
                'price' => 2500.00,
                'category_id' => $specials->id,
                'is_available' => false,
            ],
        ];
