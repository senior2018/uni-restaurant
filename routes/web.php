<?php

use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\ResendOtpController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Alert;
use App\Http\Controllers\Admin\MealController;
use App\Http\Controllers\Admin\MealCategoryController;


Route::get('/verify/email', function (Request $request) {
    return Inertia::render('Auth/VerifyOtp', [
        'email' => $request->query('email'),
        'context' => $request->query('context'),
    ]);
})->name('verify.email');

Route::get('/email/verify-otp', function (Request $request) {
    return Inertia::render('Auth/VerifyOtp', [
        'email' => $request->query('email'),
        'context' => $request->query('context'),
    ]);
})->name('otp.verify.form');

// Social Register
Route::get('/auth/google/redirect', [SocialLoginController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [SocialLoginController::class, 'handleGoogleCallback'])->name('google.callback');

Route::middleware(['auth'])->group(function () {
    Route::get('/complete-profile', [\App\Http\Controllers\Auth\CompleteProfileController::class, 'edit'])->name('complete-profile');
    Route::post('/complete-profile', [\App\Http\Controllers\Auth\CompleteProfileController::class, 'update'])->name('complete-profile.update');
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

// Guest (public) routes
Route::get('/', function () {
    return Inertia::render('Guest/Home',[
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::middleware(['auth'])->get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::middleware(['auth', 'super_admin'])->group(function () {
    Route::get('/super-admin/dashboard', [DashboardController::class, 'index'])
        ->name('superadmin.dashboard');
});

Route::middleware(['auth', 'super_admin'])->group(function () {
    Route::get('/super-admin/admins', [DashboardController::class, 'listAdmins'])->name('admin.list');
});

// Profile Management
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    // Meals
    Route::get('/meals', [MealController::class, 'index'])->name('meals.index');
    Route::post('/meals', [MealController::class, 'store'])->name('meals.store');
    Route::put('/meals/{meal}', [MealController::class, 'update'])->name('meals.update');
    Route::post('/meals/{meal}/toggle', [MealController::class, 'toggleAvailability'])->name('meals.toggle');
    Route::delete('/meals/{meal}', [MealController::class, 'destroy'])->name('meals.destroy');

    // Categories
    Route::resource('meal-categories', MealCategoryController::class)->except(['show']);

    // routes for trash functionality
    Route::get('meal-categories-with-trashed', [MealCategoryController::class, 'indexWithTrashed'])
        ->name('meal-categories.with-trashed');
    Route::get('meal-categories-trashed', [MealCategoryController::class, 'trashedOnly'])
        ->name('meal-categories.trashed');
    Route::post('meal-categories/{id}/restore', [MealCategoryController::class, 'restore'])
        ->name('meal-categories.restore');
    Route::delete('meal-categories/{id}/force-delete', [MealCategoryController::class, 'forceDelete'])
        ->name('meal-categories.force-delete');
});


require __DIR__.'/auth.php';
