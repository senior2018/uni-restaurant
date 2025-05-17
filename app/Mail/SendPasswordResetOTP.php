<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendPasswordResetOTP extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $otp,
        public User $user
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Password Reset Verification Code',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset-otp',
            with: [
                'name' => $this->user->name,
                'otp' => $this->otp,
                'expires' => now()->addMinutes(15)->diffForHumans()
            ]
        );
    }
}
