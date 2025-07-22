<?php

namespace App\Providers;

use App\Models\Meal;
use App\Models\MealCategory;
use App\Policies\MealPolicy;
use App\Policies\MealCategoryPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
// use Illuminate\Support\ServiceProvider;
use App\Models\SupportTicket;
use App\Policies\SupportTicketPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Meal::class => MealPolicy::class,
        MealCategory::class => MealCategoryPolicy::class,
        SupportTicket::class => SupportTicketPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // You can put Gate definitions here too, if needed
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }
}
