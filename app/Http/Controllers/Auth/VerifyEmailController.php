<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;

class VerifyEmailController extends Controller
{
    /**
     * Show OTP verification form (GET)
     */
    public function showForm(Request $request)
    {
        $context = $request->query('context', 'email');

        return Inertia::render('Auth/VerifyOtp', [
            'email' => $request->query('email'),
            'context' => $context,
            'status' => session('status')
        ]);
    }

    /**
     * Verify OTP for email verification or other contexts
     */
    public function verify(Request $request): RedirectResponse
    {


        $request->validate([
            'otp' => 'required|digits:6',
            'context' => 'required|string',
            'email' => 'required|email',
        ]);

        $context = $request->input('context');

        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            return back()->withErrors(['otp' => 'User not found. Please try again.']);
        }

        // dd($user);

        $otpRecord = OtpVerification::where('user_id', $user->id)
            ->where('otp', $request->input('otp'))
            ->where('otp_type', $context)
            // ->whereNull('verified_at')
            ->latest()
            ->first();

        //

        if (
            !$otpRecord ||
            $otpRecord->expires_at < now() ||
            $otpRecord->otp !== $request->otp
        ) {

            return back()->withErrors(['otp' => 'Invalid or expired OTP']);
        }

        // Mark OTP as verified
        $otpRecord->update(['verified_at' => now()]);

        switch ($context) {


            case 'email':
                if (!$user->hasVerifiedEmail()) {
                    $user->markEmailAsVerified();
                }
                if (!Auth::check()) {
                    Auth::login($user);
                }
                return redirect()->route('dashboard')->with('verified', true);

            case 'forgot_password':

                return redirect()->route('password.reset.form', [
                    'user_id' => $user->id,
                    'verified' => true
                ]);

            case 'locked_account':
                // dd($otpRecord);
                return redirect()->route('locked.reset.form', [
                    'user_id' => $user->id,
                    'verified' => true
                ]);

            default:
                return back()->withErrors(['otp' => 'Unknown OTP context.']);

            }
    }

    private function generateAndStoreOtp(User $user): OtpVerification
    {
        $otp = $this->generateSecureOtp();

        return $user->otpVerifications()->create([
            'otp_type' => 'email',
            'recipient' => $user->email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(15)
        ]);
    }

    private function generateSecureOtp(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
