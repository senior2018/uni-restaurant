<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\OtpVerification;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;

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
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'phone' => 'required|string|unique:users',
            'permanent_location' => 'nullable|string|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'permanent_location' => $request->permanent_location,
            'password' => Hash::make($request->password),
        ]);

        // event(new Registered($user));

        Auth::login($user);

        $otp = random_int(100000, 999999);

        // Save OTP to database
        otpVerification::create([
            'user_id' => $user->id,
            'otp_type' => 'email',
            'recipient' => $user->email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send the OTP via email
        $user->notify(new \App\Notifications\EmailVerificationNotification($otp));

        $otpType = $request->input('context', 'email');

        // Redirect to OTP verification screen (you'll define this)
        return redirect()->route('verify.otp.form', [
            'email' => $user->email,
            'context' => 'register',
        ])->with('status', 'OTP sent to your email address');

        // return redirect(route('dashboard', absolute: false));
    }
}
