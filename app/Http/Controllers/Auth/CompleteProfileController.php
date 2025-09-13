<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CompleteProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        // Redirect to login if not authenticated
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to complete your profile.');
        }

        return inertia('Auth/CompleteProfile', ['user' => $user]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Redirect to login if not authenticated
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to complete your profile.');
        }

        $request->validate([
            'phone' => 'required|string|unique:users,phone,' . $user->id,
            'permanent_location' => 'required|string|max:255',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],
        ], [
            'phone.required' => 'Phone number is required.',
            'permanent_location.required' => 'Permanent location is required.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        $user->update([
            'phone' => $request->phone,
            'permanent_location' => $request->permanent_location,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/dashboard')->with('success', 'Profile completed successfully.');
    }
}

