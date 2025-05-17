<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendPasswordResetOTP;
use Illuminate\Support\Facades\RateLimiter;
use Inertia\Inertia;

class PasswordResetController extends Controller
{
    // Show password reset form (regular flow)
    public function showResetRequest()
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    // Show locked account notice
    public function showLockedAccount()
    {
        return Inertia::render('Auth/LockedAccount');
    }

    // Handle locked account submission
    public function handleLockedAccount(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = $request->input('email');

        $user = User::where('email', $email)
            ->whereNotNull('locked_at')
            ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No locked account found']);
        }

        $otp = $this->createOtpRecord($user, 'locked_account');

        Mail::to($user->email)->send(new SendPasswordResetOTP($otp, $user));

        return redirect()->route('verify.email', [
            'email' => $user->email,
            'context' => 'locked_account',
        ])->with('status', 'OTP sent successfully to your email.');
    }

    // Send OTP for both flows
    public function sendResetOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Account not found']);
        }

        $type = $request->input('type', 'forgot_password');
        $otp = $this->createOtpRecord($user, $type);

        Mail::to($user->email)->send(new SendPasswordResetOTP($otp, $user));

        return Inertia::render('Auth/VerifyOtp', [
            'email' => $user->email,
            'context' => $type,
            'user_id' => $user->id,
            'status' => 'OTP sent successfully'
        ]);
    }

    // Show OTP verification form
    public function showVerifyOtp()
    {
        return Inertia::render('Auth/VerifyOtp', [
            'email' => $request->query('email', ''),
            'context' => $request->query('context', 'register'),
        ]);
    }

    // Show password reset form
    public function showResetForm(Request $request)
    {
        $userId = $request->query('user_id');
        $verified = $request->query('verified');

        if (!$verified|| !$userId) {
            return redirect()->route('login')->withErrors(['otp' => 'OTP verification required']);
        }

        $user = User::findOrFail($userId);

        $context = $request->query('context', 'forgot');

        return Inertia::render('Auth/ResetPassword', [
            'email' => $user->email,
            'user_id' => $user->id,
            'verified' => $verified,
            'context' => $context,
        ]);
    }

    // Process password reset
    public function resetPassword(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = User::findOrFail($request->input('user_id'));

        $user->update([
            'password' => Hash::make($request->password),
            'locked_at' => null,
            'failed_login_attempts' => 0,
            'last_failed_attempt' => null
        ]);

        return redirect()->route('login')->with('status', 'Password reset successfully!');
    }

    private function createOtpRecord(User $user, string $type): string
    {
        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $user->otpVerifications()->updateOrCreate(
            ['otp_type' => $type],
            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(15),
                'recipient' => $user->email,
            ]
        );

        return $otp;
    }
}
