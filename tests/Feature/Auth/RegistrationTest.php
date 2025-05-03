<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Mail\SendVerificationOTP;
use Illuminate\Support\Facades\Mail;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_can_register()
{
    Mail::fake();

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '+255700000000',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'permanent_location' => 'Test Location'
    ]);

    $response->assertRedirect(route('verification.notice'));

    // Verify user exists but isn't logged in
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    $this->assertGuest();

    // Verify OTP email was sent
    Mail::assertSent(SendVerificationOTP::class);
}
}
