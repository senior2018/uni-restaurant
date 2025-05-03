<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendVerificationOTP;
use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new OTP verification email
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        // Generate new OTP
        $otpRecord = $this->generateAndStoreOtp($user);

        // Send email with plaintext OTP
        Mail::to($user->email)->send(new SendVerificationOTP(
            $otpRecord->plain_otp, // Pass the temporary plaintext value
            $user
        ));

        return back()->with('status', __('auth.otp_resent'));
    }

    private function generateAndStoreOtp(User $user): OtpVerification
    {
        // Generate plaintext OTP first
        $plainOtp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Create and return OTP record (will be hashed via model mutator)
        return $user->otpVerifications()->create([
            'otp_type' => 'email',
            'recipient' => $user->email,
            'otp' => $plainOtp,
            'expires_at' => now()->addMinutes(15),
            'plain_otp' => $plainOtp // Temporary storage for email sending
        ]);
    }
}
