<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Configure test-specific settings
        $this->setUpTestEnvironment();
    }

    protected function setUpTestEnvironment(): void
    {
        // Disable rate limiting for tests unless specifically testing it
        RateLimiter::for('login', fn () => Limit::none());
        RateLimiter::for('otp', fn () => Limit::none());

        // Fake notifications and mail
        Notification::fake();
        \Illuminate\Support\Facades\Mail::fake();
    }

    protected function createUnverifiedUser(array $attributes = []): User
    {
        return User::factory()->unverified()->create([
            'password' => Hash::make('Password123!'),
            ...$attributes
        ]);
    }

    protected function createVerifiedUser(array $attributes = []): User
    {
        return User::factory()->create([
            'password' => Hash::make('Password123!'),
            'email_verified_at' => now(),
            ...$attributes
        ]);
    }

    protected function withOtpVerification(User $user, string $otp): self
    {
        // Create OTP record for the user
        $user->otpVerifications()->create([
            'otp_type' => 'email',
            'recipient' => $user->email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(15)
        ]);

        return $this;
    }

    protected function assertOtpWasSent(User $user): void
    {
        \Illuminate\Support\Facades\Mail::assertSent(
            \App\Mail\SendVerificationOTP::class,
            fn ($mail) => $mail->hasTo($user->email)
        );
    }

    protected function assertAuthenticationLockout(
        User $user,
        int $attempts = 3,
        string $message = 'Too many login attempts'
    ): void {
        for ($i = 0; $i < $attempts; $i++) {
            $this->post(route('login'), [
                'email' => $user->email,
                'password' => 'wrong-password'
            ]);
        }

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password'
        ]);

        $response->assertSessionHasErrors(['email' => $message]);
    }
}
