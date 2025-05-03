<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified using OTP.
     */
    public function __invoke(Request $request): RedirectResponse
{
    $request->validate([
        'otp' => ['required', 'string', 'digits:6']
    ]);

    /** @var User $user */
    $user = $request->user();

    if ($user->hasVerifiedEmail()) {
        return redirect()->intended(route('dashboard').'?verified=1');
    }

    // Get latest valid OTP record
    $otpRecord = OtpVerification::where('user_id', $user->id)
        ->where('otp_type', 'email')
        ->where('expires_at', '>', now())
        ->whereNull('verified_at')
        ->latest()
        ->first();

    if (!$otpRecord || $otpRecord->otp !== $request->otp) {
        return back()->withErrors(['otp' => __('auth.invalid_otp')]);
    }

    // Update verification status
    $otpRecord->update(['verified_at' => now()]);
    $user->markEmailAsVerified();

    return redirect()->route('dashboard')
        ->with('status', __('auth.verified'));
}
}
