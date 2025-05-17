<?php

namespace App\Notifications;

use App\Models\OtpVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class PasswordResetOtpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $plainOtp
    ) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Create OTP record
        OtpVerification::create([
            'user_id' => $notifiable->id,
            'otp_type' => 'password_reset',
            'recipient' => $notifiable->email,
            'otp' => Hash::make($this->plainOtp),
            'expires_at' => now()->addMinutes(15)
        ]);

        return (new MailMessage)
            ->subject('Password Reset Verification Code')
            ->line("Your password reset code is: **{$this->plainOtp}**")
            ->line('This code will expire in 15 minutes');
    }
}
