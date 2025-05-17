<?php

namespace Database\Factories;

use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OtpVerificationFactory extends Factory
{
    protected $model = OtpVerification::class;

    public function definition(): array
    {
        $otpType = $this->faker->randomElement(['email', 'phone', 'password_reset', 'two_factor']);

        return [
            'user_id' => User::factory(), // auto-generate user or pass ID in seeder
            'otp_type' => $otpType,
            'recipient' => $this->faker->email(), // can be phone if type is 'phone'
            'otp' => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'expires_at' => now()->addMinutes(10),
            'verified_at' => null,
            'used' => false,
        ];
    }

    public function verified(): static
    {
        return $this->state([
            'verified_at' => now(),
            'used' => true,
        ]);
    }
}
