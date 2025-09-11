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

        $this->command->info('✅ Deployment seeding completed successfully!');
        $this->command->info('📊 Database populated with:');
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
            [
                'name' => 'Appetizers',
                'description' => 'Start your meal with our delicious appetizers',
                'is_active' => true,
            ],
            [
                'name' => 'Main Courses',
                'description' => 'Hearty main dishes to satisfy your hunger',
                'is_active' => true,
            ],
            [
                'name' => 'Desserts',
                'description' => 'Sweet treats to end your meal perfectly',
                'is_active' => true,
            ],
            [
                'name' => 'Beverages',
                'description' => 'Refreshing drinks and beverages',
                'is_active' => true,
            ],
            [
                'name' => 'Salads',
                'description' => 'Fresh and healthy salad options',
                'is_active' => true,
            ],
            [
                'name' => 'Soups',
                'description' => 'Warm and comforting soup varieties',
                'is_active' => true,
            ],
            [
                'name' => 'Pasta',
                'description' => 'Italian-inspired pasta dishes',
                'is_active' => true,
            ],
            [
                'name' => 'Pizza',
                'description' => 'Authentic wood-fired pizzas',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            MealCategory::create($category);
        }
    }

    private function createMeals(): void
    {
        $this->command->info('Creating meals...');

        $appetizers = MealCategory::where('name', 'Appetizers')->first();
        $mainCourses = MealCategory::where('name', 'Main Courses')->first();
        $desserts = MealCategory::where('name', 'Desserts')->first();
        $beverages = MealCategory::where('name', 'Beverages')->first();
        $salads = MealCategory::where('name', 'Salads')->first();
        $soups = MealCategory::where('name', 'Soups')->first();
        $pasta = MealCategory::where('name', 'Pasta')->first();
        $pizza = MealCategory::where('name', 'Pizza')->first();

        $meals = [
            // Appetizers
            [
                'name' => 'Buffalo Wings',
                'description' => 'Crispy chicken wings tossed in spicy buffalo sauce',
                'price' => 12.99,
                'category_id' => $appetizers->id,
                'is_available' => true,
                'preparation_time' => 15,
            ],
            [
                'name' => 'Mozzarella Sticks',
                'description' => 'Golden fried mozzarella cheese sticks with marinara sauce',
                'price' => 9.99,
                'category_id' => $appetizers->id,
                'is_available' => true,
                'preparation_time' => 10,
            ],
            [
                'name' => 'Garlic Bread',
                'description' => 'Fresh baked bread with garlic butter and herbs',
                'price' => 6.99,
                'category_id' => $appetizers->id,
                'is_available' => true,
                'preparation_time' => 8,
            ],

            // Main Courses
            [
                'name' => 'Grilled Salmon',
                'description' => 'Fresh Atlantic salmon grilled to perfection with lemon herb butter',
                'price' => 24.99,
                'category_id' => $mainCourses->id,
                'is_available' => true,
                'preparation_time' => 20,
            ],
            [
                'name' => 'Beef Steak',
                'description' => 'Premium ribeye steak cooked to your preference',
                'price' => 32.99,
                'category_id' => $mainCourses->id,
                'is_available' => true,
                'preparation_time' => 25,
            ],
            [
                'name' => 'Chicken Parmesan',
                'description' => 'Breaded chicken breast with marinara sauce and melted mozzarella',
                'price' => 18.99,
                'category_id' => $mainCourses->id,
                'is_available' => true,
                'preparation_time' => 22,
            ],
            [
                'name' => 'Fish and Chips',
                'description' => 'Beer-battered cod with crispy fries and tartar sauce',
                'price' => 16.99,
                'category_id' => $mainCourses->id,
                'is_available' => true,
                'preparation_time' => 18,
            ],

            // Pasta
            [
                'name' => 'Spaghetti Carbonara',
                'description' => 'Classic Italian pasta with eggs, cheese, and pancetta',
                'price' => 17.99,
                'category_id' => $pasta->id,
                'is_available' => true,
                'preparation_time' => 15,
            ],
            [
                'name' => 'Fettuccine Alfredo',
                'description' => 'Creamy fettuccine pasta with parmesan cheese sauce',
                'price' => 15.99,
                'category_id' => $pasta->id,
                'is_available' => true,
                'preparation_time' => 12,
            ],
            [
                'name' => 'Penne Arrabbiata',
                'description' => 'Spicy penne pasta with tomato and chili sauce',
                'price' => 14.99,
                'category_id' => $pasta->id,
                'is_available' => true,
                'preparation_time' => 14,
            ],

            // Pizza
            [
                'name' => 'Margherita Pizza',
                'description' => 'Classic pizza with tomato sauce, mozzarella, and basil',
                'price' => 16.99,
                'category_id' => $pizza->id,
                'is_available' => true,
                'preparation_time' => 20,
            ],
            [
                'name' => 'Pepperoni Pizza',
                'description' => 'Traditional pizza topped with pepperoni and mozzarella',
                'price' => 18.99,
                'category_id' => $pizza->id,
                'is_available' => true,
                'preparation_time' => 20,
            ],
            [
                'name' => 'Supreme Pizza',
                'description' => 'Loaded pizza with pepperoni, sausage, peppers, and onions',
                'price' => 22.99,
                'category_id' => $pizza->id,
                'is_available' => true,
                'preparation_time' => 25,
            ],

            // Salads
            [
                'name' => 'Caesar Salad',
                'description' => 'Fresh romaine lettuce with caesar dressing and croutons',
                'price' => 11.99,
                'category_id' => $salads->id,
                'is_available' => true,
                'preparation_time' => 8,
            ],
            [
                'name' => 'Greek Salad',
                'description' => 'Mixed greens with feta cheese, olives, and Greek dressing',
                'price' => 13.99,
                'category_id' => $salads->id,
                'is_available' => true,
                'preparation_time' => 10,
            ],

            // Soups
            [
                'name' => 'Tomato Basil Soup',
                'description' => 'Creamy tomato soup with fresh basil',
                'price' => 8.99,
                'category_id' => $soups->id,
                'is_available' => true,
                'preparation_time' => 12,
            ],
            [
                'name' => 'Chicken Noodle Soup',
                'description' => 'Classic comfort soup with chicken and egg noodles',
                'price' => 9.99,
                'category_id' => $soups->id,
                'is_available' => true,
                'preparation_time' => 15,
            ],

            // Desserts
            [
                'name' => 'Chocolate Cake',
                'description' => 'Rich chocolate cake with chocolate ganache',
                'price' => 7.99,
                'category_id' => $desserts->id,
                'is_available' => true,
                'preparation_time' => 5,
            ],
            [
                'name' => 'Tiramisu',
                'description' => 'Classic Italian dessert with coffee and mascarpone',
                'price' => 8.99,
                'category_id' => $desserts->id,
                'is_available' => true,
                'preparation_time' => 5,
            ],
            [
                'name' => 'Ice Cream Sundae',
                'description' => 'Vanilla ice cream with chocolate sauce and whipped cream',
                'price' => 6.99,
                'category_id' => $desserts->id,
                'is_available' => true,
                'preparation_time' => 3,
            ],

            // Beverages
            [
                'name' => 'Fresh Orange Juice',
                'description' => 'Freshly squeezed orange juice',
                'price' => 4.99,
                'category_id' => $beverages->id,
                'is_available' => true,
                'preparation_time' => 2,
            ],
            [
                'name' => 'Iced Tea',
                'description' => 'Refreshing iced tea with lemon',
                'price' => 3.99,
                'category_id' => $beverages->id,
                'is_available' => true,
                'preparation_time' => 2,
            ],
            [
                'name' => 'Coffee',
                'description' => 'Freshly brewed coffee',
                'price' => 2.99,
                'category_id' => $beverages->id,
                'is_available' => true,
                'preparation_time' => 3,
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
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@ourrestaurant.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'email_verified_at' => now(),
            'phone' => '+1234567890',
            'address' => '123 Admin Street, Admin City',
        ]);

        // Admin
        User::create([
            'name' => 'Restaurant Admin',
            'email' => 'admin@ourrestaurant.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'phone' => '+1234567891',
            'address' => '456 Admin Avenue, Admin City',
        ]);

        // Staff members
        $staffNames = ['John Smith', 'Sarah Johnson', 'Mike Wilson', 'Emily Davis', 'David Brown'];
        foreach ($staffNames as $index => $name) {
            User::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@ourrestaurant.com',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'email_verified_at' => now(),
                'phone' => '+123456789' . (2 + $index),
                'address' => (100 + $index) . ' Staff Street, Staff City',
            ]);
        }

        // Customers
        $customerNames = [
            'Alice Johnson', 'Bob Smith', 'Carol Williams', 'David Jones', 'Eva Brown',
            'Frank Davis', 'Grace Miller', 'Henry Wilson', 'Ivy Moore', 'Jack Taylor',
            'Kate Anderson', 'Liam Thomas', 'Mia Jackson', 'Noah White', 'Olivia Harris',
            'Paul Martin', 'Quinn Thompson', 'Rachel Garcia', 'Sam Martinez', 'Tina Robinson'
        ];

        foreach ($customerNames as $index => $name) {
            User::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'email_verified_at' => now(),
                'phone' => '+198765432' . str_pad($index, 1, '0', STR_PAD_LEFT),
                'address' => (200 + $index) . ' Customer Street, Customer City',
            ]);
        }
    }

    private function createOrders(): void
    {
        $this->command->info('Creating orders...');

        $customers = User::where('role', 'customer')->get();
        $staff = User::where('role', 'staff')->get();
        $meals = Meal::all();
        $statuses = ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'];

        // Create 50 orders
        for ($i = 0; $i < 50; $i++) {
            $customer = $customers->random();
            $assignedStaff = $staff->random();
            $status = $statuses[array_rand($statuses)];
            $createdAt = now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            $order = Order::create([
                'user_id' => $customer->id,
                'assigned_staff_id' => $assignedStaff->id,
                'status' => $status,
                'total_amount' => 0, // Will be calculated
                'delivery_address' => $customer->address,
                'notes' => rand(0, 1) ? 'Please deliver to the back door' : null,
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
                    'subtotal' => $subtotal,
                ]);

                $totalAmount += $subtotal;
            }

            // Update order total
            $order->update(['total_amount' => $totalAmount]);
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
            $assignedStaff = $staff->random();
            $subject = $subjects[array_rand($subjects)];
            $status = ['open', 'in_progress', 'resolved'][array_rand(['open', 'in_progress', 'resolved'])];

            SupportTicket::create([
                'user_id' => $customer->id,
                'assigned_staff_id' => $assignedStaff->id,
                'subject' => $subject,
                'description' => 'Customer inquiry about ' . strtolower($subject),
                'status' => $status,
                'priority' => ['low', 'medium', 'high'][array_rand(['low', 'medium', 'high'])],
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
                'type' => 'email_verification',
                'expires_at' => now()->addMinutes(15),
                'verified_at' => rand(0, 1) ? now() : null,
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
