<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        //Get validated credentials
        $credentials = $request->validated();

        //check if user exists and is locked
        $user = User::where('email', $credentials['email'])->first();

        if ($user && $user->locked_at) {
            // Redirect to locked account OTP handler
            return redirect()->route('account.locked');
        }

        // If user exists but email not verified
        if ($user && !$user->hasVerifiedEmail()) {
            
            // Generate new OTP
            $otp = random_int(100000, 999999);

             // Save or update OTP in database
            \App\Models\OtpVerification::updateOrCreate(
                ['user_id' => $user->id, 'otp_type' => 'email'],
                [
                    'recipient' => $user->email,
                    'otp' => $otp,
                    'expires_at' => now()->addMinutes(10),
                ]
            );

            // Send the OTP to email
            $user->notify(new \App\Notifications\EmailVerificationNotification($otp));

            // Redirect to OTP verification screen
            return redirect()->route('verify.otp.form', [
                'email' => $user->email,
                'context' => 'login_unverified',
            ])->with('status', 'Please verify your email to log in. OTP sent.');
        }

        // Try to login
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            // If user exists, track failed attempt
            if ($user) {
                $user->recordFailedAttempt();

                if ($user->failed_login_attempts >= 3) {
                    $user->lockAccount();

                    return redirect()->route('account.locked');
                }

                // User exists but wrong password
                return back()->withErrors([
                    'email' => 'The password you entered is incorrect. Please try again.',
                ]);
            } else {
                // User doesn't exist
                return back()->withErrors([
                    'email' => 'No account found with this email address. Please create an account first.',
                ]);
            }
        }

        // Reset on successful login
        $request->user()->update([
            'failed_login_attempts' => 0,
            'last_failed_attempt' => null,
            'locked_at' => null,
        ]);

        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Welcome back!');
    }

    public function showLockedAccount()
    {
        return Inertia::render('Auth/LockedAccount');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

