<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class SocialLoginController extends Controller
{
    public function redirectToGoogle()
    {
        try {
            Log::info('Redirecting to Google OAuth, APP_URL: ' . config('app.url'));
            return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            Log::error('Google OAuth redirect failed: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Google login is currently unavailable. Please try again later.');
        }
    }

    public function handleGoogleCallback()
    {
        try {
            Log::info('Google OAuth callback received, APP_URL: ' . config('app.url'));
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(Str::random(16)),
                ]);

                // Automatically mark Google user as verified
                $user->markEmailAsVerified();
            } else {
                // Optional: Update google_id if missing
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }

                // Also verify email if not yet verified
                if (!$user->hasVerifiedEmail()) {
                    $user->markEmailAsVerified();
                }
            }

            Auth::login($user, true);

            // Redirect to profile completion if needed
            if (!$user->phone || !$user->permanent_location) {
                Log::info('Redirecting to complete-profile for user: ' . $user->email);
                return redirect()->route('complete-profile');
            }

            Log::info('Redirecting to dashboard for user: ' . $user->email);
            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            Log::error('Google Login Failed: '.$e->getMessage());
            return redirect('/login')->with('error', 'Google login failed. Please try again.');
        }
    }
}
