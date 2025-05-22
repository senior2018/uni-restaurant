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
        return inertia('Auth/CompleteProfile', ['user' => $user]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|unique:users,phone,' . Auth::id(),
            'permanent_location' => 'required|string|max:255',
            'password' => 'nullable|string|confirmed|min:8',
        ]);

        $user = Auth::user();

        $rules = [
            'phone' => 'required|string|unique:users,phone,' . $user->id,
            'permanent_location' => 'required|string|max:255',
            'password' => 'required|string|confirmed|min:8',
        ];

        $request->validate($rules);

        $user->update([
            'phone' => $request->phone,
            'permanent_location' => $request->permanent_location,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/dashboard')->with('success', 'Profile completed successfully.');
    }
}

