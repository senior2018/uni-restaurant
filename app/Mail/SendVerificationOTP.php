<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendVerificationOTP extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The plaintext OTP for email display.
     * @var string
     */
    public $plainOtp;

    /**
     * OTP expiration time display.
     * @var string|null
     */
    public $expires;

    /**
     * User's name for personalization.
     * @var string
     */
    public $name;

    /**
     * Create a new message instance.
     *
     * @param string $plainOtp The unhashed OTP to display in email
     * @param User $user The associated user model
     */
    public function __construct(string $plainOtp, User $user)
    {
        $this->plainOtp = $plainOtp;
        $this->name = $user->name;

        // Get expiration from latest OTP verification record
        $latestOtp = $user->otpVerifications()
            ->where('otp_type', 'email')
            ->latest()
            ->first();

        $this->expires = $latestOtp?->expires_at
            ? $latestOtp->expires_at->diffForHumans()
            : '15 minutes';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Our Restaurant - Verify Your Email',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): content
    {
        return new Content(
            view: 'emails.verify-otp',
            with: [
                'otp' => $this->plainOtp,
                'expires' => $this->expires,
                'name' => $this->name,
            ]
        );
    }
}
