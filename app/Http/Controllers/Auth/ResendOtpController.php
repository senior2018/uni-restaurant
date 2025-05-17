<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\OtpVerification;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;

class ResendOtpController extends Controller
{
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'context' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        $context = $request->input('context');

        // Normalize to match your database OTP types
        switch ($context) {
            case 'email':
                $otp = $this->createOtp($user, 'email');
                $user->notify(new \App\Notifications\EmailVerificationNotification($otp));
                break;

            case 'forgot_password':
                $otp = $this->createOtp($user, 'forgot_password');
                Mail::to($user->email)->send(new \App\Mail\SendPasswordResetOTP($otp, $user));
                break;

            case 'locked_account':
                if (!$user->locked_at) {
                    return back()->withErrors(['email' => 'Account is not locked.']);
                }
                $otp = $this->createOtp($user, 'locked_account');
                Mail::to($user->email)->send(new \App\Mail\SendPasswordResetOTP($otp, $user));
                break;

            default:
                return back()->withErrors(['context' => 'Invalid OTP context.']);
        }

        return back()->with('status', 'A new OTP has been sent to your email.');
    }

    private function createOtp(User $user, string $type): string
    {
        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Clean up old OTPs of this type
        OtpVerification::where('user_id', $user->id)
            ->where('otp_type', $type)
            ->whereNull('verified_at')
            ->delete();

        OtpVerification::create([
            'user_id' => $user->id,
            'otp_type' => $type,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(15),
            'recipient' => $user->email,
            'used' => false,
        ]);

        return $otp;
    }
}
