<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'super_admin' => \App\Http\Middleware\EnsureSuperAdmin::class,
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
        ]);
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

    $app->middleware([
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Session\Middleware\StartSession::class,
    ]);

    $app->routeMiddleware([
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
    ]);

    // Rate limiting configuration
    \Illuminate\Support\Facades\RateLimiter::for('login', function (Request $request) {
        return \Illuminate\Cache\RateLimiting\Limit::perMinute(3)->by($request->ip());
    });
