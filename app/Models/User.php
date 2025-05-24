<?php

namespace App\Models;

use App\Notifications\EmailVerificationNotification;
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
        'google_id',
        'phone',
        'password',
        'permanent_location',
        'role',
        'last_failed_attempt',
        'failed_login_attempts',
        'locked_at'
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
     * Check if user is a super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    /**
     * Check if user is staff (including admins).
     */
    public function isStaff(): bool
    {
        return in_array($this->role, ['staff', 'admin', 'super_admin']);
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

    public function sendEmailVerificationNotification()
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->notify(new EmailVerificationNotification($otp));
    }

    /**********************************************
     * PASSWORD RESET OTP FUNCTIONALITY *
     **********************************************/

    /**
     * Send password reset OTP notification
     */
    public function sendPasswordResetOtp()
    {
        $plainOtp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->notify(new \App\Notifications\PasswordResetOtpNotification($plainOtp));
    }

    /**
     * Lock the user account
     */
    public function lockAccount(): void
    {
        $this->update([
            'locked_at' => now(),
            'failed_login_attempts' => 0 // Reset attempts after lock
        ]);
    }

    /**
     * Unlock the user account
     */
    public function unlockAccount(): void
    {
        $this->update([
            'locked_at' => null,
            'failed_login_attempts' => 0,
            'last_failed_attempt' => null
        ]);
    }

    /**
     * Check if account is locked
     */
    public function isLocked(): bool
    {
        return !is_null($this->locked_at);
    }

    /**
     * Increment failed login attempts
     */
    public function recordFailedAttempt(): void
    {
        $this->increment('failed_login_attempts');
        $this->update(['last_failed_attempt' => now()]);
    }

    /**
     * Get active password reset OTP
     */
    public function activePasswordResetOtp(): HasOne
    {
        return $this->hasOne(OtpVerification::class)
            ->where('otp_type', 'password_reset')
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest();
    }
}
