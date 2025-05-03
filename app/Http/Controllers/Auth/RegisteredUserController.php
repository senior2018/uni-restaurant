<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use App\Mail\SendVerificationOTP;


class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle incoming registration with OTP verification
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'phone' => ['required', 'string', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'permanent_location' => ['required', 'string', 'max:255'],
        ]);

        $plainOtp = $this->generateOTP();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'permanent_location' => $request->permanent_location,
            'email_verified_at' => null,
            'email_verification_otp' => $plainOtp,
            'email_verification_otp_expires_at' => now()->addMinutes(15),
        ]);

        Mail::to($user->email)->send(new SendVerificationOTP($plainOtp, $user));

        event(new Registered($user));

        return redirect()->route('verification.notice');
    }


    /**
     * Generate 6-digit numeric OTP
     */
    private function generateOTP(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
