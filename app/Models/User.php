<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\OtpVerification;
use App\Models\Order;
use App\Models\Rating;
use App\Models\Alert;


class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

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
     * RELATIONSHIPS *
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
     * Get all OTP verification records for this user.
     * @return HasMany<OtpVerification>
     */
    public function otpVerifications(): HasMany
    {
        return $this->hasMany(OtpVerification::class);
    }

    /**
     * Get the active email OTP verification.
     * @return HasOne<OtpVerification>
     */
    public function activeEmailOtp(): HasOne
    {
        return $this->hasOne(OtpVerification::class)
            ->valid()       // Defined in OtpVerification model
            ->emailType()   // Defined in OtpVerification model
            ->latest();
    }

    /**********************************************
     * ROLE METHODS *
     **********************************************/


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

    /**********************************************
     * ALERT MANAGEMENT *
     **********************************************/

    /**
     * Get alerts handled by this staff member.
     * @return HasMany<Alert>
     */
    public function handledAlerts(): HasMany
    {
        return $this->hasMany(Alert::class, 'staff_id');
    }

    /**********************************************
     * EMAIL VERIFICATION *
     **********************************************/
    /**
     * Mark the user's email as verified.
     */

    // Updated verification method
}
