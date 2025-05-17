<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\ResendOtpController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;

// Home Route
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/verify/email', function (Request $request) {
    return Inertia::render('Auth/VerifyOtp', [
        'email' => $request->query('email'),
        'context' => $request->query('context'),
    ]);
})->name('verify.email');

// Email verification routes
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify-otp', fn () => Inertia::render('Auth/VerifyOtp'))
        ->name('otp.verify.form');
    });

    Route::post('/email/verify', [VerifyEmailController::class, 'verify'])
        ->name('verification.verify');

    //Otp Verification - Registration
    Route::post('/otp/verify', [VerifyEmailController::class, 'verify'])
        ->name('otp.verify');

    //OTP Verification -Password Reser
    Route::post('/password-reset/verify-otp', [PasswordResetController::class, 'verifyOtp'])
        ->name('otp.verify.password_reset');

    // Show the OTP form after registration
    Route::get('/verify-otp', [VerifyEmailController::class, 'showForm'])
        ->name('verify.otp.form');

    // Handle OTP submission
    Route::post('/verify-otp', [VerifyEmailController::class, 'verify'])
        ->name('verify.otp.submit');

    // Resend OTP
    Route::post('/resend-otp', [ResendOtpController::class, 'resend'])
        ->name('verify.otp.resend');

    // Forgot password - show new password form
    Route::get('/reset-password-form', [PasswordResetController::class, 'showResetForm'])
        ->name('password.reset.form');

    // Locked account - show new password form
    Route::get('/locked-account-reset-form', [PasswordResetController::class, 'showResetForm'])
        ->name('locked.reset.form');



// Password Reset Routes (Cleaned, No Duplicates)
Route::controller(PasswordResetController::class)->group(function () {
    // Regular password reset flow
    Route::get('/forgot-password', [PasswordResetController::class, 'showResetRequest'])
        ->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetOtp'])
        ->name('password.email');

    // Locked account specific flow
    Route::get('/locked-account', [PasswordResetController::class, 'showLockedAccount'])
        ->name('account.locked');
    Route::post('/locked-account', [PasswordResetController::class, 'handleLockedAccount'])
        ->name('account.locked.submit');

    // Common OTP verification form for password reset
    Route::get('/verify-password-otp', [PasswordResetController::class, 'showVerifyOtp'])
        ->name('password.verify');
    Route::post('/verify-password-otp', [PasswordResetController::class, 'verifyOtp'])
        ->name('password.verify.submit');

    // Unified Reset Password submission (POST)
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
        ->name('password.reset.otp');
});

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Profile Management
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
