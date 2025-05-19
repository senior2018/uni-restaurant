<?php

namespace Database\Seeders;

use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Database\Seeder;

class OtpVerificationsSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        OtpVerification::create([
            'user_id' => $user->id,
            'otp_type' => 'password_reset',
            'recipient' => $user->email,
            'otp' => '123456',
            'expires_at' => now()->addMinutes(10),
        ]);
    }
}
