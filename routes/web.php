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

// Complete profile routes - accessible after Google login
Route::get('/complete-profile', [\App\Http\Controllers\Auth\CompleteProfileController::class, 'edit'])->name('complete-profile');
Route::post('/complete-profile', [\App\Http\Controllers\Auth\CompleteProfileController::class, 'update'])->name('complete-profile.update');

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
})->name('home');

Route::get('/login', function () {
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('login');

Route::get('/register', function () {
    return Inertia::render('Auth/Register', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('register');

// SINGLE DASHBOARD ROUTE FOR ALL ROLES
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'profile.completed'])
    ->name('dashboard');

// SUPER ADMIN ROUTES
Route::middleware(['auth', 'super_admin'])->group(function () {
    Route::get('/super-admin/dashboard', [DashboardController::class, 'index'])
        ->name('superadmin.dashboard');
});

Route::middleware(['auth', 'super_admin'])->group(function () {
    Route::get('/super-admin/admins', [DashboardController::class, 'listAdmins'])->name('admin.list');
});

// Profile Management
Route::middleware(['auth', 'profile.completed'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//ADMIN & STAFF
Route::middleware(['auth', 'verified', 'profile.completed'])->group(function () {
    // Shared toggle route for both admin and staff
    // Route::post('/meals/{meal}/toggle', [MealController::class, 'toggleAvailability'])
    //     ->name('meals.toggle')
    //     ->middleware('can:toggle,meal');
});

// ADMIN ROUTES
Route::middleware(['auth', 'verified', 'profile.completed'])->prefix('admin')->group(function () {
    // Meals
    Route::get('/meals', [MealController::class, 'index'])->name('meals.index');
    Route::post('/meals', [MealController::class, 'store'])->name('meals.store');
    Route::put('/meals/{meal}', [MealController::class, 'update'])->name('meals.update');
    Route::post('/meals/{meal}/toggle', [MealController::class, 'toggleAvailability'])->name('meals.toggle');
    Route::delete('/meals/{meal}', [MealController::class, 'destroy'])->name('meals.destroy');

    // Soft delete operations
    Route::post('/meals/{meal}/restore', [MealController::class, 'restore'])->name('meals.restore');
    Route::delete('/meals/{meal}/force', [MealController::class, 'forceDelete'])->name('meals.force-delete');

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

// Admin Order Management
Route::middleware(['auth', 'admin', 'profile.completed'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/orders', [\App\Http\Controllers\Admin\AdminOrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{order}/assign-staff', [\App\Http\Controllers\Admin\AdminOrderController::class, 'assignStaff'])->name('orders.assignStaff');
    Route::get('/orders/{order}', [\App\Http\Controllers\Admin\AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/approve-cancellation', [\App\Http\Controllers\Admin\AdminOrderController::class, 'approveCancellation'])->name('orders.approveCancellation');
    Route::post('/orders/{order}/reject-cancellation', [\App\Http\Controllers\Admin\AdminOrderController::class, 'rejectCancellation'])->name('orders.rejectCancellation');
    Route::post('/orders/mark-cancellation-seen', [\App\Http\Controllers\Admin\AdminOrderController::class, 'markCancellationSeen'])->name('orders.markCancellationSeen');
    Route::get('/pending-cancellations', [\App\Http\Controllers\Admin\AdminOrderController::class, 'pendingCancellations'])->name('pendingCancellations');
    Route::post('/orders/{order}/status', [\App\Http\Controllers\Admin\AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
});

//STAFF ROUTES
Route::middleware(['auth', 'verified', 'profile.completed'])->prefix('staff')->name('staff.')->group(function () {
    // Staff meals index page - only toggle functionality
    Route::get('/meals', [MealController::class, 'staffIndex'])
        ->name('meals.index')
        ->middleware('can:viewAny,App\\Models\\Meal');
    Route::post('/meals/{meal}/toggle', [MealController::class, 'toggleAvailability'])->name('meals.toggle');

    // Staff order management
    Route::post('/orders/{order}/claim', [\App\Http\Controllers\StaffOrderController::class, 'claim'])->name('orders.claim');
    Route::post('/orders/{order}/status', [\App\Http\Controllers\StaffOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('/my-orders', [\App\Http\Controllers\StaffOrderController::class, 'myOrders'])->name('myOrders');
    Route::get('/unassigned-orders', [\App\Http\Controllers\StaffOrderController::class, 'unassignedOrders'])->name('unassignedOrders');
    Route::post('/orders/{order}/approve-cancellation', [\App\Http\Controllers\StaffOrderController::class, 'approveCancellation'])->name('orders.approveCancellation');
    Route::post('/orders/{order}/reject-cancellation', [\App\Http\Controllers\StaffOrderController::class, 'rejectCancellation'])->name('orders.rejectCancellation');
    Route::post('/orders/mark-cancellation-seen', [\App\Http\Controllers\StaffOrderController::class, 'markCancellationSeen'])->name('orders.markCancellationSeen');
    Route::get('/pending-cancellations', [\App\Http\Controllers\StaffOrderController::class, 'pendingCancellations'])->name('pendingCancellations');
    Route::get('/report-issue', [\App\Http\Controllers\Staff\ReportIssueController::class, 'create'])->name('report-issue');
    Route::post('/report-issue', [\App\Http\Controllers\SupportTicketController::class, 'store'])->name('report-issue.store');
    Route::get('/my-support-tickets', [\App\Http\Controllers\SupportTicketController::class, 'myTickets'])->name('support-tickets.mine');
});

// CUSTOMER ROUTES (if needed)
Route::middleware(['auth', 'verified', 'role:customer', 'profile.completed'])->group(function () {
    Route::get('/menu', [MealController::class, 'customerIndex'])->name('menu.index');
});

// Public menu browsing route
Route::get('/menu', [\App\Http\Controllers\CustomerMenuController::class, 'index'])->name('menu.public');

// Customer cart page (must be logged in)
Route::middleware(['auth', 'verified', 'profile.completed'])->get('/cart', function () {
    return Inertia::render('Customer/Cart', [
        'user' => request()->user(),
    ]);
})->name('cart');

// Customer checkout page (must be logged in)
Route::middleware(['auth', 'verified', 'profile.completed'])->get('/checkout', function () {
    return Inertia::render('Customer/Checkout', [
        'user' => request()->user(),
    ]);
})->name('checkout');

// Customer order placement (POST)
Route::middleware(['auth', 'verified', 'profile.completed'])->post('/checkout', [\App\Http\Controllers\CustomerOrderController::class, 'store'])->name('checkout.store');

// Customer order cancel (POST with reason)
Route::middleware(['auth', 'verified', 'profile.completed'])->post('/orders/{order}/cancel', [\App\Http\Controllers\CustomerOrderController::class, 'cancel'])->name('customer.orders.cancel');
// Customer order tracking (My Orders)
Route::middleware(['auth', 'verified', 'profile.completed'])->get('/my-orders', [\App\Http\Controllers\CustomerOrderController::class, 'index'])->name('customer.orders');
// Customer update order (POST)
Route::middleware(['auth', 'verified', 'profile.completed'])->post('/my-orders/{order}/edit', [\App\Http\Controllers\CustomerOrderController::class, 'update'])->name('customer.orders.update');
// Customer withdraw cancellation request (POST)
Route::middleware(['auth', 'verified', 'profile.completed'])->post('/orders/{order}/cancel-request', [\App\Http\Controllers\CustomerOrderController::class, 'cancelRequest'])->name('customer.orders.cancelRequest');
require __DIR__.'/auth.php';

// Ratings & Alerts (Customer)
Route::middleware(['auth', 'verified', 'profile.completed'])->group(function () {
    Route::post('/ratings', [\App\Http\Controllers\RatingController::class, 'store'])->name('ratings.store');
    Route::post('/alerts', [\App\Http\Controllers\AlertController::class, 'store'])->name('alerts.store');
});

// Ratings & Alerts (Admin/Staff)
Route::middleware(['auth', 'admin', 'profile.completed'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/ratings', [\App\Http\Controllers\RatingController::class, 'index'])->name('ratings.index');
    Route::get('/ratings/{rating}', [\App\Http\Controllers\RatingController::class, 'show'])->name('ratings.show');
    Route::patch('/ratings/{rating}/respond', [\App\Http\Controllers\RatingController::class, 'respond'])->name('ratings.respond');

    Route::get('/alerts', [\App\Http\Controllers\AlertController::class, 'index'])->name('alerts.index');
    Route::get('/alerts/{alert}', [\App\Http\Controllers\AlertController::class, 'show'])->name('alerts.show');
    Route::patch('/alerts/{alert}/respond', [\App\Http\Controllers\AlertController::class, 'respond'])->name('alerts.respond');
    Route::patch('/alerts/{alert}/resolve', [\App\Http\Controllers\AlertController::class, 'resolve'])->name('alerts.resolve');
});

// Admin alerts management
Route::middleware(['auth', 'profile.completed'])->group(function () {
    Route::get('/admin/alerts', [\App\Http\Controllers\AlertController::class, 'adminIndex'])
        ->name('admin.alerts.index')
        ->middleware('can:viewAny,App\\Models\\Alert');
    Route::patch('/admin/alerts/{alert}/respond', [\App\Http\Controllers\AlertController::class, 'respond'])
        ->name('admin.alerts.respond')
        ->middleware('can:viewAny,App\\Models\\Alert');
    Route::patch('/admin/alerts/{alert}/resolve', [\App\Http\Controllers\AlertController::class, 'resolve'])
        ->name('admin.alerts.resolve')
        ->middleware('can:viewAny,App\\Models\\Alert');
    Route::delete('/admin/alerts/bulk-destroy', [\App\Http\Controllers\AlertController::class, 'bulkDestroy'])
        ->name('admin.alerts.bulkDestroy')
        ->middleware('can:viewAny,App\\Models\\Alert');
    Route::delete('/admin/alerts/{alert}', [\App\Http\Controllers\AlertController::class, 'destroy'])
        ->name('admin.alerts.destroy')
        ->middleware('can:delete,alert');
});
// Staff alerts management
Route::middleware(['auth', 'profile.completed'])->group(function () {
    Route::get('/staff/alerts', [\App\Http\Controllers\AlertController::class, 'staffIndex'])
        ->name('staff.alerts.index')
        ->middleware('can:viewAny,App\\Models\\Alert');
    Route::patch('/staff/alerts/{alert}/respond', [\App\Http\Controllers\AlertController::class, 'respond'])
        ->name('staff.alerts.respond')
        ->middleware('can:viewAny,App\\Models\\Alert');
    // No resolve route for staff
});

// Notification mark as read
Route::middleware(['auth', 'verified', 'profile.completed'])->post('/notifications/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
Route::middleware(['auth', 'verified', 'profile.completed'])->post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead']);

// Support ticket/contact routes
Route::post('/contact', [\App\Http\Controllers\SupportTicketController::class, 'store'])->name('contact.store');
Route::middleware(['auth', 'profile.completed'])->group(function () {
    Route::get('/admin/support-tickets', [\App\Http\Controllers\SupportTicketController::class, 'index'])->name('admin.support-tickets.index');
    Route::patch('/support-tickets/{ticket}/respond', [\App\Http\Controllers\SupportTicketController::class, 'adminRespond'])->name('support-tickets.respond');
});

Route::get('/contact', function () {
    return Inertia::render('Guest/Contact', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('contact');
