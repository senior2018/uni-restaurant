<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'permanent_location',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'phone_verified_at'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_failed_attempt' => 'datetime',
            'failed_login_attempts' => 'integer'
        ];
    }

    /**********************************************
     * ADD THESE NEW METHODS INSIDE THE USER CLASS *
     **********************************************/

    /**
     * Get all orders placed by this user.
     * @return HasMany<Order>
     */
    // 1. ORDERS: A user can have many orders
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get all ratings submitted by this user.
     * @return HasMany<Rating>
     */

    // 2. RATINGS: A user can submit many ratings
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get all alerts raised by this user (as customer).
     * @return HasMany<Alert>
     */

    // 3. ALERTS: A user can raise many alerts (as customer)
    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is staff (including admins).
     */
    public function isStaff(): bool
    {
        return in_array($this->role, ['staff', 'admin']);
    }

    // 4. HANDLED ALERTS: A user (staff) can resolve many alerts
    public function handledAlerts(): HasMany
    {
        return $this->hasMany(Alert::class, 'staff_id');
    }
}
