<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
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
     * Handle an incoming authentication request with enhanced security
     */
    public function store(Request $request): RedirectResponse
    {
        // First validate basic credentials
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        // Check account lock status
        if ($user && $user->failed_login_attempts >= 3) {
            $lockMinutes = 15;

            // Calculate remaining lock time
            if ($user->last_failed_attempt && $user->last_failed_attempt->addMinutes($lockMinutes)->isFuture()) {
                throw ValidationException::withMessages([
                    'email' => __('Your account is locked. Please try again in :minutes minutes.', [
                        'minutes' => $lockMinutes - $user->last_failed_attempt->diffInMinutes(now())
                    ]),
                ]);
            } else {
                // Reset attempts if lock period has expired
                $user->update([
                    'failed_login_attempts' => 0,
                    'last_failed_attempt' => null
                ]);
            }
        }

        // Attempt authentication
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            // Update failed attempts
            if ($user) {
                $user->increment('failed_login_attempts');
                $user->update(['last_failed_attempt' => now()]);
            }

            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        // Check if email is verified
        if (!$request->user()->hasVerifiedEmail()) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => __('You need to verify your email address before logging in.'),
            ]);
        }

        // Reset security counters on successful login
        $request->user()->update([
            'failed_login_attempts' => 0,
            'last_failed_attempt' => null
        ]);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
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
