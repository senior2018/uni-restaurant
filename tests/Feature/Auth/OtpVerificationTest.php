<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\OtpVerification;

class OtpVerificationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_email_verification_with_valid_otp()
{
    $user = User::factory()->unverified()->create();
    $otp = OtpVerification::create([
        'user_id' => $user->id,
        'otp' => '123456',
        'expires_at' => now()->addMinutes(15)
    ]);

    $response = $this->actingAs($user)
        ->post(route('verification.verify'), ['otp' => '123456']);

    $response->assertRedirect(route('dashboard'))
            ->assertSessionHas('status', 'Email verified!');
    $this->assertTrue($user->fresh()->hasVerifiedEmail());
}
}
