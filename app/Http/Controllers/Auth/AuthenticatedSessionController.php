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
            // Send OTP for email verification
            $user->sendOtp('email');

            return redirect()->route('verify.email', [
                'email' => $user->email,
                'context' => 'login_unverified',
            ]);
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
            }

                return back()->withErrors([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        // Reset on successful login
        $request->user()->update([
            'failed_login_attempts' => 0,
            'last_failed_attempt' => null,
            'locked_at' => null,
        ]);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
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

