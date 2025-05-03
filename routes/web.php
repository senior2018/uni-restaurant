<?php

use App\Http\Controllers\Auth\{
    AuthenticatedSessionController,
    RegisteredUserController,
    VerifyEmailController,
    EmailVerificationPromptController
};
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\PasswordConfirmationController;


// Email verification notice route
Route::get('/email/verify', [EmailVerificationPromptController::class, '__invoke'])
    ->name('verification.notice');

// Authentication routes
Route::middleware(['guest'])->group(function () {
    Route::controller(RegisteredUserController::class)->group(function () {
        Route::get('/register', 'create')->name('register');
        Route::post('/register', 'store');
    });

    Route::controller(AuthenticatedSessionController::class)->group(function () {
        Route::get('/login', 'create')->name('login');
        Route::post('/login', 'store')->name('login.attempt');
    });
});

// Email verification after login
Route::middleware(['auth'])->group(function () {
    Route::post('/email/verify', [VerifyEmailController::class, '__invoke'])
        ->name('verification.verify');
});

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

Route::get('/confirm-password', [PasswordConfirmationController::class, 'show'])->middleware('auth');
Route::post('/confirm-password', [PasswordConfirmationController::class, 'store'])->middleware('auth');

