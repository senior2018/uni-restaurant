<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MealCategory;
use App\Models\Meal;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Rating;
use App\Models\Alert;
use App\Models\SupportTicket;
use App\Models\OtpVerification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DeploymentSeeder extends Seeder
{
    /**
     * Run the database seeds for deployment.
     * This seeder creates a comprehensive dataset for a restaurant management system.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting deployment seeding...');

        // Create meal categories first
        $this->createMealCategories();

        // Create meals
        $this->createMeals();

        // Create users (admin, staff, customers)
        $this->createUsers();

        // Create orders and order items
        $this->createOrders();

        // Create ratings
        $this->createRatings();

        // Create alerts
        $this->createAlerts();

        // Create support tickets
        $this->createSupportTickets();

        // Create OTP verifications
        $this->createOtpVerifications();

        $this->command->info('✓ Deployment seeding completed successfully!');
        $this->command->info('Database populated with:');
        $this->command->info('   - ' . MealCategory::count() . ' meal categories');
        $this->command->info('   - ' . Meal::count() . ' meals');
        $this->command->info('   - ' . User::count() . ' users');
        $this->command->info('   - ' . Order::count() . ' orders');
        $this->command->info('   - ' . OrderItem::count() . ' order items');
        $this->command->info('   - ' . Rating::count() . ' ratings');
        $this->command->info('   - ' . Alert::count() . ' alerts');
        $this->command->info('   - ' . SupportTicket::count() . ' support tickets');
    }

    private function createMealCategories(): void
    {
        $this->command->info('Creating meal categories...');

        $categories = [
            ['name' => 'Starches'],
            ['name' => 'Proteins'],
            ['name' => 'Vegetables'],
            ['name' => 'Breakfast'],
            ['name' => 'Beverages'],
            ['name' => 'Specials'],
        ];

        foreach ($categories as $category) {
            MealCategory::create($category);
        }
    }

    private function createMeals(): void
    {
        $this->command->info('Creating meals...');

        $starches = MealCategory::where('name', 'Starches')->first();
        $proteins = MealCategory::where('name', 'Proteins')->first();
        $vegetables = MealCategory::where('name', 'Vegetables')->first();
        $breakfast = MealCategory::where('name', 'Breakfast')->first();
        $beverages = MealCategory::where('name', 'Beverages')->first();
        $specials = MealCategory::where('name', 'Specials')->first();

        $meals = [
            // Starches Category (3 meals: 2 available, 1 unavailable)
            [
                'name' => 'Ugali Maharage',
                'description' => 'Traditional maize meal with beans',
                'price' => 2500.00,
                'category_id' => $starches->id,
                'is_available' => true,
                'image_url' => 'meals/3pFOnrP6yZtfRYNRW2xaq2CyGlBb2VG8hJCTQeQ1.jpg',
            ],
            [
                'name' => 'Wali Maharage',
                'description' => 'Rice with beans',
                'price' => 3000.00,
                'category_id' => $starches->id,
                'is_available' => true,
                'image_url' => 'meals/4hp1mbOjr1R7WHzdUVhehmRqM07rFkDedzmYrH2v.jpg',
            ],
            [
                'name' => 'Pilau',
                'description' => 'Spiced rice with meat',
                'price' => 4000.00,
                'category_id' => $starches->id,
                'is_available' => false,
                'image_url' => 'meals/6D8WqWTUBQEYqYPi8zKlgzdXDBIqP14ZoiYhdnHf.jpg',
            ],

            // Proteins Category (3 meals: 2 available, 1 unavailable)
            [
                'name' => 'Samaki wa Kupaka',
                'description' => 'Grilled fish with coconut sauce',
                'price' => 5000.00,
                'category_id' => $proteins->id,
                'is_available' => true,
                'image_url' => 'meals/880QOj6lOFASW6PRZrTWGmvvlYMgvAkhpI5Vb7ca.jpg',
            ],
            [
                'name' => 'Mchuzi wa Kuku',
                'description' => 'Chicken curry',
                'price' => 4500.00,
                'category_id' => $proteins->id,
                'is_available' => true,
                'image_url' => 'meals/9EAPEXWMDYIlwHx9e83WQzLAmnJcYh2OLxpvftxn.jpg',
            ],
            [
                'name' => 'Beef Stew',
                'description' => 'Tender beef in rich gravy',
                'price' => 6000.00,
                'category_id' => $proteins->id,
                'is_available' => false,
                'image_url' => 'meals/AccHXktiLI0Go5O6phTktiE1If51pFs5wFCyiRve.jpg',
            ],

            // Vegetables Category (3 meals: 2 available, 1 unavailable)
            [
                'name' => 'Vegetable Curry',
                'description' => 'Mixed vegetables in curry sauce',
                'price' => 2000.00,
                'category_id' => $vegetables->id,
                'is_available' => true,
                'image_url' => 'meals/aZ8zkdt1ZgskFpQswxAnirHpP622J5Eh5avcjqvb.jpg',
            ],
            [
                'name' => 'Kisamvu na Nyama',
                'description' => 'Cassava leaves with meat',
                'price' => 3500.00,
                'category_id' => $vegetables->id,
                'is_available' => true,
                'image_url' => 'meals/C4oiMkZXiWuN1lxy2afr8bCmgwqXcyMBt53U6ruG.jpg',
            ],
            [
                'name' => 'Mchemsho',
                'description' => 'Mixed vegetable stew',
                'price' => 2500.00,
                'category_id' => $vegetables->id,
                'is_available' => false,
                'image_url' => 'meals/HaJyQwGUauQM57x4AZBY9b1GYvTWNRquneD5ayTO.jpg',
            ],

            // Breakfast Category (3 meals: 2 available, 1 unavailable)
            [
                'name' => 'Chips Mayai',
                'description' => 'French fries with eggs',
                'price' => 3000.00,
                'category_id' => $breakfast->id,
                'is_available' => true,
                'image_url' => 'meals/JcEMzhEgPNIjtlLqZUOmqwdTByP6LLwFJtH6DcEg.jpg',
            ],
            [
                'name' => 'Chapati',
                'description' => 'Flatbread with tea',
                'price' => 1500.00,
                'category_id' => $breakfast->id,
                'is_available' => true,
                'image_url' => 'meals/pWmHRcofYaigWvhmXNi5kBzp2JPboMIZ4v8od1qd.jpg',
            ],
            [
                'name' => 'Mandazi',
                'description' => 'Sweet fried bread',
                'price' => 1000.00,
                'category_id' => $breakfast->id,
                'is_available' => false,
                'image_url' => 'meals/QIAQVDvXmW4OpYiXABW0GtHXhsD0Ixh9716KsmQv.jpg',
            ],

            // Beverages Category (3 meals: 2 available, 1 unavailable)
            [
                'name' => 'Chai',
                'description' => 'Traditional spiced tea',
                'price' => 500.00,
                'category_id' => $beverages->id,
                'is_available' => true,
                'image_url' => 'meals/RmaoNtYUvd7KgwRnd92fZB5gfLlQE54l7h4aAI2p.jpg',
            ],
            [
                'name' => 'Fresh Juice',
                'description' => 'Fresh fruit juice',
                'price' => 2000.00,
                'category_id' => $beverages->id,
                'is_available' => true,
                'image_url' => 'meals/srDtrvg8AuWnHADFAMvYtVrqjJ01sbuHxSHWzeCr.jpg',
            ],
            [
                'name' => 'Coffee',
                'description' => 'Freshly brewed coffee',
                'price' => 1000.00,
                'category_id' => $beverages->id,
                'is_available' => false,
                'image_url' => 'meals/Ti28y0TRE7PcFIptykV6RHWOFPcuMRxm8nG6zVjg.jpg',
            ],

            // Specials Category (3 meals: 2 available, 1 unavailable)
            [
                'name' => 'Katogo',
                'description' => 'Traditional mixed dish',
                'price' => 4000.00,
                'category_id' => $specials->id,
                'is_available' => true,
                'image_url' => 'meals/W4Rdk8O7pVtotk4BdECTnN6iabPUaoh69ou30jFD.jpg',
            ],
            [
                'name' => 'Matoke',
                'description' => 'Steamed green bananas',
                'price' => 3000.00,
                'category_id' => $specials->id,
                'is_available' => true,
                'image_url' => 'meals/XGeUXmfeU0upk80dXW1d4AZH1Q6F5BCaVim618cc.jpg',
            ],
            [
                'name' => 'Supu ya Ndizi',
                'description' => 'Banana soup',
                'price' => 2500.00,
                'category_id' => $specials->id,
                'is_available' => false,
                'image_url' => 'meals/Z74RmsNRQ87iItvJeeFudR8sI8GuAYykDwUfATIr.jpg',
            ],
        ];

        foreach ($meals as $meal) {
            Meal::create($meal);
        }
    }

    private function createUsers(): void
    {
        $this->command->info('Creating users...');

        // Super Admin
        User::firstOrCreate(
            ['email' => 'superadmin@ourrestaurant.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
                'phone' => '+1234567890',
                'permanent_location' => '123 Admin Street, Admin City',
            ]
        );

        // Admin
        User::firstOrCreate(
            ['email' => 'admin@ourrestaurant.com'],
            [
                'name' => 'Restaurant Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'phone' => '+1234567891',
                'permanent_location' => '456 Admin Avenue, Admin City',
            ]
        );

        // Staff members
        $staffNames = ['John Smith', 'Sarah Johnson', 'Mike Wilson', 'Emily Davis', 'David Brown'];
        foreach ($staffNames as $index => $name) {
            $email = strtolower(str_replace(' ', '.', $name)) . '@ourrestaurant.com';
            User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'role' => 'staff',
                    'email_verified_at' => now(),
                    'phone' => '+123456789' . (2 + $index),
                    'permanent_location' => (100 + $index) . ' Staff Street, Staff City',
                ]
            );
        }

        // Customers
        $customerNames = [
            'Alice Johnson', 'Bob Smith', 'Carol Williams', 'David Jones', 'Eva Brown',
            'Frank Davis', 'Grace Miller', 'Henry Wilson', 'Ivy Moore', 'Jack Taylor',
            'Kate Anderson', 'Liam Thomas', 'Mia Jackson', 'Noah White', 'Olivia Harris',
            'Paul Martin', 'Quinn Thompson', 'Rachel Garcia', 'Sam Martinez', 'Tina Robinson'
        ];

        foreach ($customerNames as $index => $name) {
            $email = strtolower(str_replace(' ', '.', $name)) . '@example.com';
            User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'role' => 'customer',
                    'email_verified_at' => now(),
                    'phone' => '+198765432' . str_pad($index, 1, '0', STR_PAD_LEFT),
                    'permanent_location' => (200 + $index) . ' Customer Street, Customer City',
                ]
            );
        }
    }

    private function createOrders(): void
    {
        $this->command->info('Creating orders...');

        $customers = User::where('role', 'customer')->get();
        $staff = User::where('role', 'staff')->get();
        $meals = Meal::all();
        $statuses = ['pending', 'preparing', 'delivered', 'cancelled'];

        // Create 50 orders
        for ($i = 0; $i < 50; $i++) {
            $customer = $customers->random();
            $assignedStaff = $staff->random();
            $status = $statuses[array_rand($statuses)];
            $createdAt = now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            $order = Order::create([
                'user_id' => $customer->id,
                'staff_id' => $assignedStaff->id,
                'status' => $status,
                'total_price' => 0, // Will be calculated
                'delivery_location' => $customer->permanent_location,
                'staff_notes' => rand(0, 1) ? 'Please deliver to the back door' : null,
                'payment_method' => ['cash', 'mobile_money', 'card'][array_rand(['cash', 'mobile_money', 'card'])],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Create 1-4 order items per order
            $itemCount = rand(1, 4);
            $totalAmount = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $meal = $meals->random();
                $quantity = rand(1, 3);
                $subtotal = $meal->price * $quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'meal_id' => $meal->id,
                    'quantity' => $quantity,
                    'price' => $meal->price,
                ]);

                $totalAmount += $subtotal;
            }

            // Update order total
            $order->update(['total_price' => $totalAmount]);
        }
    }

    private function createRatings(): void
    {
        $this->command->info('Creating ratings...');

        $orders = Order::where('status', 'delivered')->get();
        $meals = Meal::all();

        foreach ($orders as $order) {
            // 70% chance to create a rating
            if (rand(1, 100) <= 70) {
                $meal = $meals->random();
                $rating = rand(3, 5); // Mostly positive ratings
                $comment = $this->getRandomComment($rating);

                Rating::create([
                    'user_id' => $order->user_id,
                    'meal_id' => $meal->id,
                    'order_id' => $order->id,
                    'rating' => $rating,
                    'comment' => $comment,
                    'created_at' => $order->created_at->addHours(rand(1, 24)),
                ]);
            }
        }
    }

    private function createAlerts(): void
    {
        $this->command->info('Creating alerts...');

        $orders = Order::all();
        $alertReasons = [
            'Food quality issue',
            'Late delivery',
            'Missing items',
            'Wrong order',
            'Payment issue',
            'Customer complaint',
        ];

        // Create 15 alerts
        for ($i = 0; $i < 15; $i++) {
            $order = $orders->random();
            $reason = $alertReasons[array_rand($alertReasons)];
            $resolved = rand(0, 1);

            Alert::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'reason' => $reason,
                'resolved' => $resolved,
                'created_at' => $order->created_at->addHours(rand(1, 48)),
            ]);
        }
    }

    private function createSupportTickets(): void
    {
        $this->command->info('Creating support tickets...');

        $customers = User::where('role', 'customer')->get();
        $staff = User::where('role', 'staff')->get();
        $subjects = [
            'Account Issues',
            'Order Problems',
            'Payment Questions',
            'Technical Support',
            'General Inquiry',
        ];

        // Create 20 support tickets
        for ($i = 0; $i < 20; $i++) {
            $customer = $customers->random();
            $subject = $subjects[array_rand($subjects)];
            $status = ['open', 'closed'][array_rand(['open', 'closed'])];

            SupportTicket::create([
                'name' => $customer->name,
                'email' => $customer->email,
                'subject' => $subject,
                'message' => 'Customer inquiry about ' . strtolower($subject),
                'status' => $status,
                'user_id' => $customer->id,
                'is_registered' => true,
                'created_at' => now()->subDays(rand(0, 30)),
            ]);
        }
    }

    private function createOtpVerifications(): void
    {
        $this->command->info('Creating OTP verifications...');

        $users = User::all();

        // Create some OTP verifications for testing
        foreach ($users->take(10) as $user) {
            OtpVerification::create([
                'user_id' => $user->id,
                'otp' => rand(100000, 999999),
                'otp_type' => 'email',
                'recipient' => $user->email,
                'expires_at' => now()->addMinutes(15),
                'verified_at' => rand(0, 1) ? now() : null,
                'used' => rand(0, 1),
            ]);
        }
    }

    private function getRandomComment(int $rating): string
    {
        $comments = [
            5 => [
                'Excellent food and service!',
                'Amazing experience, will definitely come back!',
                'Perfect meal, highly recommended!',
                'Outstanding quality and taste!',
                'Best restaurant in town!',
            ],
            4 => [
                'Very good food, minor issues with timing.',
                'Great meal overall, would recommend.',
                'Good quality food and service.',
                'Nice experience, will visit again.',
                'Solid meal with good flavors.',
            ],
            3 => [
                'Average experience, room for improvement.',
                'Decent food but service could be better.',
                'Okay meal, nothing special.',
                'Fair quality, expected more.',
                'Standard restaurant experience.',
            ],
        ];

        $ratingComments = $comments[$rating] ?? $comments[3];
        return $ratingComments[array_rand($ratingComments)];
    }
}
