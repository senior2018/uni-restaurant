<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RestaurantConfig;

class RestaurantConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default restaurant configuration
        RestaurantConfig::create([
            'restaurant_name' => 'Our Restaurant',
            'restaurant_slug' => 'our-restaurant',
            'description' => 'Welcome to Our Restaurant - Your favorite dining destination serving delicious meals with exceptional service.',
            'email' => 'info@ourrestaurant.com',
            'phone' => '+1 (555) 123-4567',
            'address' => '123 Main Street',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'US',
            'business_hours' => [
                'monday' => ['open' => '09:00', 'close' => '22:00', 'closed' => false],
                'tuesday' => ['open' => '09:00', 'close' => '22:00', 'closed' => false],
                'wednesday' => ['open' => '09:00', 'close' => '22:00', 'closed' => false],
                'thursday' => ['open' => '09:00', 'close' => '22:00', 'closed' => false],
                'friday' => ['open' => '09:00', 'close' => '23:00', 'closed' => false],
                'saturday' => ['open' => '10:00', 'close' => '23:00', 'closed' => false],
                'sunday' => ['open' => '10:00', 'close' => '21:00', 'closed' => false],
            ],
            'primary_color' => '#10B981',
            'secondary_color' => '#059669',
            'accent_color' => '#F59E0B',
            'text_color' => '#1F2937',
            'background_color' => '#FFFFFF',
            'menu_enabled' => true,
            'categories_enabled' => true,
            'pricing_display' => true,
            'nutritional_info' => false,
            'allergen_info' => true,
            'online_ordering' => true,
            'pickup_enabled' => true,
            'delivery_enabled' => true,
            'delivery_fee' => 2.99,
            'minimum_order' => 15.00,
            'preparation_time' => 30,
            'payment_methods' => ['cash', 'card'],
            'cash_payment' => true,
            'card_payment' => true,
            'digital_wallet' => false,
            'currency' => 'USD',
            'currency_symbol' => '$',
            'tax_rate' => 0.0875, // 8.75%
            'tax_inclusive' => false,
            'tax_name' => 'Sales Tax',
            'email_notifications' => true,
            'sms_notifications' => false,
            'push_notifications' => false,
            'notification_settings' => [
                'order_confirmation' => true,
                'order_ready' => true,
                'order_delivered' => true,
                'promotional' => false,
            ],
            'website_url' => 'https://ourrestaurant.com',
            'facebook_url' => 'https://facebook.com/ourrestaurant',
            'instagram_url' => 'https://instagram.com/ourrestaurant',
            'twitter_url' => null,
            'youtube_url' => null,
            'meta_title' => 'Our Restaurant - Delicious Food & Great Service',
            'meta_description' => 'Experience exceptional dining at Our Restaurant. Fresh ingredients, delicious meals, and outstanding service in a welcoming atmosphere.',
            'meta_keywords' => 'restaurant, food, dining, delivery, pickup, fresh, delicious',
            'google_analytics_id' => null,
            'facebook_pixel_id' => null,
            'maintenance_mode' => false,
            'maintenance_message' => 'We are currently performing maintenance. Please check back soon.',
            'registration_enabled' => true,
            'guest_checkout' => true,
            'session_timeout' => 120,
            'additional_settings' => [
                'auto_confirm_orders' => false,
                'require_phone_verification' => false,
                'allow_custom_orders' => true,
                'max_order_items' => 20,
                'order_lead_time' => 15, // minutes
            ],
        ]);
    }
}
