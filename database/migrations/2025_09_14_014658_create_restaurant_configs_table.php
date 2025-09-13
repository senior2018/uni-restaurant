<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('restaurant_configs', function (Blueprint $table) {
            $table->id();

            // Restaurant Basic Information
            $table->string('restaurant_name')->default('Our Restaurant');
            $table->string('restaurant_slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('favicon_url')->nullable();

            // Contact Information
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('US');

            // Business Hours
            $table->json('business_hours')->nullable(); // Store as JSON for flexibility

            // Branding & Theme
            $table->string('primary_color')->default('#10B981');
            $table->string('secondary_color')->default('#059669');
            $table->string('accent_color')->default('#F59E0B');
            $table->string('text_color')->default('#1F2937');
            $table->string('background_color')->default('#FFFFFF');

            // Menu Configuration
            $table->boolean('menu_enabled')->default(true);
            $table->boolean('categories_enabled')->default(true);
            $table->boolean('pricing_display')->default(true);
            $table->boolean('nutritional_info')->default(false);
            $table->boolean('allergen_info')->default(false);

            // Order Configuration
            $table->boolean('online_ordering')->default(true);
            $table->boolean('pickup_enabled')->default(true);
            $table->boolean('delivery_enabled')->default(false);
            $table->decimal('delivery_fee', 8, 2)->default(0.00);
            $table->decimal('minimum_order', 8, 2)->default(0.00);
            $table->integer('preparation_time')->default(30); // minutes

            // Payment Configuration
            $table->json('payment_methods')->nullable(); // Store enabled payment methods
            $table->boolean('cash_payment')->default(true);
            $table->boolean('card_payment')->default(true);
            $table->boolean('digital_wallet')->default(false);
            $table->string('currency')->default('USD');
            $table->string('currency_symbol')->default('$');

            // Tax Configuration
            $table->decimal('tax_rate', 5, 4)->default(0.0000); // 0.0875 = 8.75%
            $table->boolean('tax_inclusive')->default(false);
            $table->string('tax_name')->default('Sales Tax');

            // Notification Settings
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->boolean('push_notifications')->default(false);
            $table->json('notification_settings')->nullable();

            // Social Media & Links
            $table->string('website_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('youtube_url')->nullable();

            // SEO & Marketing
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('google_analytics_id')->nullable();
            $table->string('facebook_pixel_id')->nullable();

            // System Settings
            $table->boolean('maintenance_mode')->default(false);
            $table->text('maintenance_message')->nullable();
            $table->boolean('registration_enabled')->default(true);
            $table->boolean('guest_checkout')->default(false);
            $table->integer('session_timeout')->default(120); // minutes

            // Additional Settings (JSON for flexibility)
            $table->json('additional_settings')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_configs');
    }
};
