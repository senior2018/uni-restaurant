<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
{
    $user = $request->user();

    if (!$user || !$user->hasVerifiedEmail()) {
        return $request->expectsJson()
            ? abort(403, 'Email verification required')
            : redirect()->route('verification.notice');
    }

    return $next($request);
}
}
