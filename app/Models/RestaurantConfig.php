<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class RestaurantConfig extends Model
{
    protected $fillable = [
        'restaurant_name',
        'restaurant_slug',
        'description',
        'logo_url',
        'favicon_url',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'business_hours',
        'primary_color',
        'secondary_color',
        'accent_color',
        'text_color',
        'background_color',
        'menu_enabled',
        'categories_enabled',
        'pricing_display',
        'nutritional_info',
        'allergen_info',
        'online_ordering',
        'pickup_enabled',
        'delivery_enabled',
        'delivery_fee',
        'minimum_order',
        'preparation_time',
        'payment_methods',
        'cash_payment',
        'card_payment',
        'digital_wallet',
        'currency',
        'currency_symbol',
        'tax_rate',
        'tax_inclusive',
        'tax_name',
        'email_notifications',
        'sms_notifications',
        'push_notifications',
        'notification_settings',
        'website_url',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'youtube_url',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'google_analytics_id',
        'facebook_pixel_id',
        'maintenance_mode',
        'maintenance_message',
        'registration_enabled',
        'guest_checkout',
        'session_timeout',
        'additional_settings',
    ];

    protected $casts = [
        'business_hours' => 'array',
        'payment_methods' => 'array',
        'notification_settings' => 'array',
        'additional_settings' => 'array',
        'delivery_fee' => 'decimal:2',
        'minimum_order' => 'decimal:2',
        'tax_rate' => 'decimal:4',
        'menu_enabled' => 'boolean',
        'categories_enabled' => 'boolean',
        'pricing_display' => 'boolean',
        'nutritional_info' => 'boolean',
        'allergen_info' => 'boolean',
        'online_ordering' => 'boolean',
        'pickup_enabled' => 'boolean',
        'delivery_enabled' => 'boolean',
        'cash_payment' => 'boolean',
        'card_payment' => 'boolean',
        'digital_wallet' => 'boolean',
        'tax_inclusive' => 'boolean',
        'email_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'push_notifications' => 'boolean',
        'maintenance_mode' => 'boolean',
        'registration_enabled' => 'boolean',
        'guest_checkout' => 'boolean',
    ];

    /**
     * Get the current restaurant configuration
     */
    public static function current()
    {
        return Cache::remember('restaurant_config', 3600, function () {
            return self::first() ?? self::createDefault();
        });
    }

    /**
     * Create default restaurant configuration
     */
    public static function createDefault()
    {
        return self::create([
            'restaurant_name' => 'Our Restaurant',
            'restaurant_slug' => 'our-restaurant',
            'description' => 'Welcome to Our Restaurant - Your favorite dining destination',
            'primary_color' => '#10B981',
            'secondary_color' => '#059669',
            'accent_color' => '#F59E0B',
            'text_color' => '#1F2937',
            'background_color' => '#FFFFFF',
            'currency' => 'TZS',
            'currency_symbol' => 'TSh',
            'tax_rate' => 0.0875, // 8.75%
            'tax_name' => 'Sales Tax',
            'business_hours' => [
                'monday' => ['open' => '09:00', 'close' => '22:00', 'closed' => false],
                'tuesday' => ['open' => '09:00', 'close' => '22:00', 'closed' => false],
                'wednesday' => ['open' => '09:00', 'close' => '22:00', 'closed' => false],
                'thursday' => ['open' => '09:00', 'close' => '22:00', 'closed' => false],
                'friday' => ['open' => '09:00', 'close' => '23:00', 'closed' => false],
                'saturday' => ['open' => '10:00', 'close' => '23:00', 'closed' => false],
                'sunday' => ['open' => '10:00', 'close' => '21:00', 'closed' => false],
            ],
            'payment_methods' => ['cash', 'card'],
        ]);
    }

    /**
     * Clear configuration cache when updated
     */
    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('restaurant_config');
        });

        static::deleted(function () {
            Cache::forget('restaurant_config');
        });
    }

    /**
     * Get formatted business hours
     */
    public function getFormattedBusinessHoursAttribute()
    {
        if (!$this->business_hours) {
            return null;
        }

        $days = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];

        $formatted = [];
        foreach ($this->business_hours as $day => $hours) {
            if ($hours['closed']) {
                $formatted[$days[$day]] = 'Closed';
            } else {
                $formatted[$days[$day]] = $hours['open'] . ' - ' . $hours['close'];
            }
        }

        return $formatted;
    }

    /**
     * Check if restaurant is currently open
     */
    public function isOpen()
    {
        if (!$this->business_hours) {
            return false;
        }

        $currentDay = strtolower(now()->format('l')); // monday, tuesday, etc.
        $currentTime = now()->format('H:i');

        if (!isset($this->business_hours[$currentDay])) {
            return false;
        }

        $hours = $this->business_hours[$currentDay];

        if ($hours['closed']) {
            return false;
        }

        return $currentTime >= $hours['open'] && $currentTime <= $hours['close'];
    }

    /**
     * Get tax amount for a given price
     */
    public function calculateTax($price)
    {
        return $price * $this->tax_rate;
    }

    /**
     * Get total price including tax
     */
    public function getTotalWithTax($price)
    {
        if ($this->tax_inclusive) {
            return $price;
        }

        return $price + $this->calculateTax($price);
    }
}
