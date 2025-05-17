<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class EmailVerificationOtpController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:6']);

        $otpRecord = $request->user()
            ->otpVerifications()
            ->where('otp_type', 'email')
            ->latest()
            ->first();

        if (!$otpRecord || !Hash::check($request->otp, $otpRecord->otp) || now()->gt($otpRecord->expires_at)) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP']);
        }

        $otpRecord->update(['verified_at' => now(), 'used' => true]);

        $request->user()->markEmailAsVerified();

        return redirect()->route('login')->with('success', 'Email verified successfully. Please log in.');
    }

    public function resend(Request $request)
    {
        // reuse the existing logic to send OTP
        app(EmailVerificationNotificationController::class)->store($request);
        return back()->with('status', 'New OTP sent.');
    }
}
