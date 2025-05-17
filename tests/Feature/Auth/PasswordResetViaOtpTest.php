<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\OtpVerification;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetViaOtpTest extends TestCase
{
    use RefreshDatabase;

    // tests/Feature/Auth/PasswordResetViaOtpTest.php
    public function test_user_can_reset_password_after_lockout_with_otp()
    {
        // 1. Create locked-out user
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('original-password'),
            'email_verified_at' => now(),
            'failed_login_attempts' => 3,
            'last_failed_attempt' => now(),
        ]);

        // 2. Create valid OTP
        $otp = '123456';
        OtpVerification::create([
            'user_id' => $user->id,
            'recipient' => 'user@example.com',
            'otp' => $otp,
            'otp_type' => 'password_reset',
            'used' => false,
            'expires_at' => now()->addMinutes(10),
        ]);

        // 3. Verify OTP (without password)
        $response = $this->post(route('password.otp.verify'), [
            'email' => 'user@example.com',
            'otp' => $otp,
        ]);

        $response->assertSessionHasNoErrors();

        $response->assertRedirect(route('password.reset.form'));

        // 4. Submit new password
        $response = $this->post(route('password.reset.save'), [
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('login'));

        // 5. Verify login works
        $this->post(route('login.attempt'), [
            'email' => 'user@example.com',
            'password' => 'new-password',
        ])->assertRedirect(route('dashboard'));
    }


}
