<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // If user is authenticated, check if profile is completed
        if ($user) {
            // Check if user has completed their profile (has phone and permanent_location)
            if (!$user->phone || !$user->permanent_location) {
                // Allow access to complete-profile route and logout
                if (!$request->routeIs('complete-profile') &&
                    !$request->routeIs('complete-profile.update') &&
                    !$request->routeIs('logout')) {

                    return redirect()->route('complete-profile')
                        ->with('warning', 'Please complete your profile to continue.');
                }
            }
        }

        return $next($request);
    }
}
