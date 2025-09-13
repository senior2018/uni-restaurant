<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Meal;
use App\Models\Alert;
use App\Models\SupportTicket;
use App\Models\Rating;
use App\Models\RestaurantConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SuperAdminController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display the super admin dashboard with comprehensive statistics
     */
    public function dashboard()
    {
        $this->authorize('viewAny', User::class);

        $stats = [
            // User Statistics
            'total_users' => User::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_staff' => User::where('role', 'staff')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_users_this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)->count(),

            // Order Statistics
            'total_orders' => Order::count(),
            'active_orders' => Order::whereNotIn('status', ['delivered', 'cancelled'])->count(),
            'orders_today' => Order::whereDate('created_at', today())->count(),
            'orders_this_week' => Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'orders_this_month' => Order::whereMonth('created_at', now()->month)->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),

            // Revenue Statistics
            'total_revenue' => Order::where('status', 'delivered')->sum('total_price'),
            'revenue_today' => Order::where('status', 'delivered')->whereDate('created_at', today())->sum('total_price'),
            'revenue_this_week' => Order::where('status', 'delivered')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_price'),
            'revenue_this_month' => Order::where('status', 'delivered')->whereMonth('created_at', now()->month)->sum('total_price'),
            'average_order_value' => Order::where('status', 'delivered')->avg('total_price') ?? 0,

            // Menu Statistics
            'total_meals' => Meal::count(),
            'available_meals' => Meal::where('is_available', true)->count(),
            'unavailable_meals' => Meal::where('is_available', false)->count(),
            'total_categories' => \App\Models\MealCategory::count(),

            // Support & Alerts
            'total_alerts' => Alert::count(),
            'unresolved_alerts' => Alert::where('resolved', false)->count(),
            'total_support_tickets' => SupportTicket::count(),
            'open_tickets' => SupportTicket::where('status', 'open')->count(),
            'resolved_tickets' => SupportTicket::where('status', 'resolved')->count(),

            // Ratings & Reviews
            'total_ratings' => Rating::count(),
            'average_rating' => (float) (Rating::avg('rating') ?? 0),
            'ratings_this_month' => Rating::whereMonth('created_at', now()->month)->count(),
        ];

        $recentAdmins = User::where('role', 'admin')
            ->latest()
            ->take(5)
            ->get(['id', 'name', 'email', 'created_at']);

        $recentOrders = Order::with(['user', 'items.meal'])
            ->latest()
            ->take(10)
            ->get(['id', 'user_id', 'total_price', 'status', 'created_at']);

        $systemHealth = [
            'database_status' => $this->getDatabaseStatus(),
            'storage_usage' => $this->getStorageUsage(),
            'last_backup' => $this->getLastBackupDate(),
            'system_uptime' => $this->getSystemUptime(),
            'memory_usage' => $this->getMemoryUsage(),
            'disk_health' => $this->getDiskHealth(),
            'cache_status' => $this->getCacheStatus(),
        ];

        $performanceMetrics = [
            'user_growth_trend' => $this->getUserGrowthTrend(),
            'revenue_trend' => $this->getRevenueTrend(),
            'order_completion_rate' => $this->getOrderCompletionRate(),
            'customer_satisfaction' => $this->getCustomerSatisfaction(),
            'popular_meals' => $this->getPopularMealsData(),
            'peak_hours' => $this->getPeakHours(),
        ];

        return Inertia::render('SuperAdmin/Dashboard', [
            'user' => Auth::user(),
            'stats' => $stats,
            'recentAdmins' => $recentAdmins,
            'recentOrders' => $recentOrders,
            'systemHealth' => $systemHealth,
            'performanceMetrics' => $performanceMetrics,
        ]);
    }

    /**
     * Display all admin users with management options
     */
    public function listAdmins()
    {
        $this->authorize('viewAny', User::class);

        $admins = User::where('role', 'admin')
            ->withCount(['orders', 'alerts'])
            ->latest()
            ->paginate(20);

        return Inertia::render('SuperAdmin/AdminManagement', [
            'admins' => $admins,
        ]);
    }

    /**
     * Show the form for creating a new admin
     */
    public function createAdmin()
    {
        $this->authorize('create', User::class);

        return Inertia::render('SuperAdmin/CreateAdmin');
    }

    /**
     * Store a newly created admin
     */
    public function storeAdmin(Request $request)
    {
        $this->authorize('create', User::class);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'permanent_location' => 'required|string|max:255',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'permanent_location' => $request->permanent_location,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        return redirect()->route('superadmin.admins')
            ->with('success', "Admin '{$admin->name}' created successfully.");
    }

    /**
     * Show the form for editing an admin
     */
    public function editAdmin(User $admin)
    {
        $this->authorize('update', $admin);

        if ($admin->role !== 'admin') {
            abort(403, 'Only admin users can be edited through this interface.');
        }

        return Inertia::render('SuperAdmin/EditAdmin', [
            'admin' => $admin,
        ]);
    }

    /**
     * Update the specified admin
     */
    public function updateAdmin(Request $request, User $admin)
    {
        $this->authorize('update', $admin);

        if ($admin->role !== 'admin') {
            abort(403, 'Only admin users can be updated through this interface.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $admin->id,
            'phone' => 'required|string|max:20|unique:users,phone,' . $admin->id,
            'permanent_location' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'permanent_location' => $request->permanent_location,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $admin->update($updateData);

        return redirect()->route('superadmin.admins')
            ->with('success', "Admin '{$admin->name}' updated successfully.");
    }

    /**
     * Remove the specified admin
     */
    public function destroyAdmin(User $admin)
    {
        $this->authorize('delete', $admin);

        if ($admin->role !== 'admin') {
            abort(403, 'Only admin users can be deleted through this interface.');
        }

        $adminName = $admin->name;
        $admin->delete();

        return redirect()->route('superadmin.admins')
            ->with('success', "Admin '{$adminName}' deleted successfully.");
    }

    /**
     * Display all users with role management
     */
    public function manageUsers()
    {
        $this->authorize('viewAny', User::class);

        $users = User::withCount(['orders', 'ratings', 'alerts'])
            ->latest()
            ->paginate(20);

        return Inertia::render('SuperAdmin/UserManagement', [
            'users' => $users,
        ]);
    }

    /**
     * Update user role
     */
    public function updateUserRole(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validator = Validator::make($request->all(), [
            'role' => 'required|in:customer,staff,admin',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Prevent changing super admin role
        if ($user->role === 'super_admin') {
            return back()->with('error', 'Cannot change super admin role.');
        }

        $oldRole = $user->role;
        $user->update(['role' => $request->role]);

        return back()->with('success', "User '{$user->name}' role changed from {$oldRole} to {$request->role}.");
    }

    /**
     * Display system configuration
     */
    public function systemConfig()
    {
        $this->authorize('viewAny', User::class);

        $config = [
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'app_env' => config('app.env'),
            'database_connection' => config('database.default'),
            'mail_driver' => config('mail.default'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_connection' => config('queue.default'),
        ];

        return Inertia::render('SuperAdmin/SystemConfig', [
            'config' => $config,
        ]);
    }

    /**
     * Display system analytics
     */
    public function analytics()
    {
        $this->authorize('viewAny', User::class);

        $analytics = [
            'user_growth' => $this->getUserGrowthData(),
            'order_trends' => $this->getOrderTrendsData(),
            'revenue_analytics' => $this->getRevenueAnalytics(),
            'popular_meals' => $this->getPopularMealsData(),
            'rating_distribution' => $this->getRatingDistribution(),
        ];

        return Inertia::render('SuperAdmin/Analytics', [
            'analytics' => $analytics,
        ]);
    }

    /**
     * Display system logs
     */
    public function systemLogs()
    {
        $this->authorize('viewAny', User::class);

        $logs = $this->getSystemLogs();

        return Inertia::render('SuperAdmin/SystemLogs', [
            'logs' => $logs,
        ]);
    }

    /**
     * Display restaurant configuration management
     */
    public function restaurantConfig()
    {
        $this->authorize('viewAny', User::class);

        $config = RestaurantConfig::current();

        return Inertia::render('SuperAdmin/RestaurantConfig', [
            'config' => $config,
        ]);
    }

    /**
     * Update restaurant configuration
     */
    public function updateRestaurantConfig(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $validator = Validator::make($request->all(), [
            'restaurant_name' => 'required|string|max:255',
            'restaurant_slug' => 'required|string|max:255|regex:/^[a-z0-9-]+$/',
            'description' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:2',
            'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'currency' => 'required|string|max:3',
            'currency_symbol' => 'required|string|max:5',
            'tax_rate' => 'required|numeric|min:0|max:1',
            'tax_name' => 'required|string|max:100',
            'delivery_fee' => 'nullable|numeric|min:0',
            'minimum_order' => 'nullable|numeric|min:0',
            'preparation_time' => 'required|integer|min:1|max:480',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $config = RestaurantConfig::current();
        $config->update($request->all());

        return back()->with('success', 'Restaurant configuration updated successfully.');
    }

    /**
     * Update business hours
     */
    public function updateBusinessHours(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $validator = Validator::make($request->all(), [
            'business_hours' => 'required|array',
            'business_hours.*.open' => 'required|date_format:H:i',
            'business_hours.*.close' => 'required|date_format:H:i',
            'business_hours.*.closed' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $config = RestaurantConfig::current();
        $config->update(['business_hours' => $request->business_hours]);

        return back()->with('success', 'Business hours updated successfully.');
    }

    /**
     * Update payment methods
     */
    public function updatePaymentMethods(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $validator = Validator::make($request->all(), [
            'payment_methods' => 'required|array',
            'payment_methods.*' => 'in:cash,card,digital_wallet',
            'cash_payment' => 'required|boolean',
            'card_payment' => 'required|boolean',
            'digital_wallet' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $config = RestaurantConfig::current();
        $config->update([
            'payment_methods' => $request->payment_methods,
            'cash_payment' => $request->cash_payment,
            'card_payment' => $request->card_payment,
            'digital_wallet' => $request->digital_wallet,
        ]);

        return back()->with('success', 'Payment methods updated successfully.');
    }

    /**
     * Update notification settings
     */
    public function updateNotificationSettings(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $validator = Validator::make($request->all(), [
            'email_notifications' => 'required|boolean',
            'sms_notifications' => 'required|boolean',
            'push_notifications' => 'required|boolean',
            'notification_settings' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $config = RestaurantConfig::current();
        $config->update([
            'email_notifications' => $request->email_notifications,
            'sms_notifications' => $request->sms_notifications,
            'push_notifications' => $request->push_notifications,
            'notification_settings' => $request->notification_settings,
        ]);

        return back()->with('success', 'Notification settings updated successfully.');
    }

    /**
     * Update system settings
     */
    public function updateSystemSettings(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $validator = Validator::make($request->all(), [
            'maintenance_mode' => 'required|boolean',
            'maintenance_message' => 'nullable|string',
            'registration_enabled' => 'required|boolean',
            'guest_checkout' => 'required|boolean',
            'session_timeout' => 'required|integer|min:5|max:1440',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $config = RestaurantConfig::current();
        $config->update([
            'maintenance_mode' => $request->maintenance_mode,
            'maintenance_message' => $request->maintenance_message,
            'registration_enabled' => $request->registration_enabled,
            'guest_checkout' => $request->guest_checkout,
            'session_timeout' => $request->session_timeout,
        ]);

        return back()->with('success', 'System settings updated successfully.');
    }

    // Helper methods for system information
    private function getDatabaseStatus()
    {
        try {
            DB::connection()->getPdo();
            return [
                'status' => 'healthy',
                'connection' => 'active',
                'response_time' => $this->getDatabaseResponseTime(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'connection' => 'failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function getDatabaseResponseTime()
    {
        $start = microtime(true);
        DB::select('SELECT 1');
        $end = microtime(true);
        return round(($end - $start) * 1000, 2) . 'ms';
    }

    private function getStorageUsage()
    {
        $total = disk_total_space(storage_path());
        $free = disk_free_space(storage_path());
        $used = $total - $free;

        return [
            'total' => $this->formatBytes($total),
            'used' => $this->formatBytes($used),
            'free' => $this->formatBytes($free),
            'percentage' => round(($used / $total) * 100, 2),
            'status' => ($used / $total) > 0.9 ? 'critical' : (($used / $total) > 0.8 ? 'warning' : 'healthy'),
        ];
    }

    private function getMemoryUsage()
    {
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = ini_get('memory_limit');

        return [
            'current' => $this->formatBytes($memoryUsage),
            'limit' => $memoryLimit,
            'percentage' => $this->getMemoryPercentage($memoryUsage, $memoryLimit),
            'status' => $this->getMemoryStatus($memoryUsage, $memoryLimit),
        ];
    }

    private function getMemoryPercentage($usage, $limit)
    {
        $limitBytes = $this->parseMemoryLimit($limit);
        return $limitBytes > 0 ? round(($usage / $limitBytes) * 100, 2) : 0;
    }

    private function parseMemoryLimit($limit)
    {
        $unit = strtolower(substr($limit, -1));
        $value = (int) $limit;

        switch ($unit) {
            case 'g': return $value * 1024 * 1024 * 1024;
            case 'm': return $value * 1024 * 1024;
            case 'k': return $value * 1024;
            default: return $value;
        }
    }

    private function getMemoryStatus($usage, $limit)
    {
        $percentage = $this->getMemoryPercentage($usage, $limit);
        return $percentage > 90 ? 'critical' : ($percentage > 80 ? 'warning' : 'healthy');
    }

    private function getDiskHealth()
    {
        $storage = $this->getStorageUsage();
        return [
            'status' => $storage['status'],
            'usage_percentage' => $storage['percentage'],
            'free_space' => $storage['free'],
        ];
    }

    private function getCacheStatus()
    {
        try {
            Cache::put('health_check', 'ok', 60);
            $status = Cache::get('health_check') === 'ok' ? 'healthy' : 'error';
            Cache::forget('health_check');

            return [
                'status' => $status,
                'driver' => config('cache.default'),
                'response_time' => $this->getCacheResponseTime(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'driver' => config('cache.default'),
                'error' => $e->getMessage(),
            ];
        }
    }

    private function getCacheResponseTime()
    {
        $start = microtime(true);
        Cache::put('test_key', 'test_value', 1);
        Cache::get('test_key');
        Cache::forget('test_key');
        $end = microtime(true);
        return round(($end - $start) * 1000, 2) . 'ms';
    }

    private function getLastBackupDate()
    {
        // Check if backup files exist in storage/app/backups
        $backupPath = storage_path('app/backups');
        if (is_dir($backupPath)) {
            $files = glob($backupPath . '/*.sql');
            if (!empty($files)) {
                $latestFile = max($files);
                return date('Y-m-d H:i:s', filemtime($latestFile));
            }
        }
        return 'Never';
    }

    private function getSystemUptime()
    {
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return [
                'load_average' => $load,
                'status' => $load[0] > 2 ? 'high' : ($load[0] > 1 ? 'medium' : 'low'),
            ];
        }
        return ['status' => 'unknown', 'load_average' => [0, 0, 0]];
    }

    // Performance metrics methods
    private function getUserGrowthTrend()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = User::whereDate('created_at', '<=', $date)->count();
            $data[] = ['date' => $date, 'users' => $count];
        }
        return $data;
    }

    private function getRevenueTrend()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $revenue = Order::where('status', 'delivered')
                ->whereDate('created_at', $date)
                ->sum('total_price');
            $data[] = ['date' => $date, 'revenue' => $revenue];
        }
        return $data;
    }

    private function getOrderCompletionRate()
    {
        $totalOrders = Order::count();
        $completedOrders = Order::where('status', 'delivered')->count();

        return $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 2) : 0;
    }

    private function getCustomerSatisfaction()
    {
        $totalRatings = Rating::count();
        $averageRating = Rating::avg('rating') ?? 0;
        $positiveRatings = Rating::where('rating', '>=', 4)->count();

        return [
            'average_rating' => round($averageRating, 2),
            'total_ratings' => $totalRatings,
            'satisfaction_rate' => $totalRatings > 0 ? round(($positiveRatings / $totalRatings) * 100, 2) : 0,
        ];
    }

    private function getPeakHours()
    {
        $data = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $count = Order::whereRaw('EXTRACT(HOUR FROM created_at) = ?', [$hour])->count();
            $data[] = ['hour' => $hour, 'orders' => $count];
        }
        return $data;
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    private function getUserGrowthData()
    {
        // Get user growth over the last 30 days
        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = User::whereDate('created_at', '<=', $date)->count();
            $data[] = ['date' => $date, 'users' => $count];
        }
        return $data;
    }

    private function getOrderTrendsData()
    {
        // Get order trends over the last 30 days
        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = Order::whereDate('created_at', $date)->count();
            $data[] = ['date' => $date, 'orders' => $count];
        }
        return $data;
    }

    private function getRevenueAnalytics()
    {
        $totalRevenue = Order::where('status', 'delivered')->sum('total_price');
        $monthlyRevenue = Order::where('status', 'delivered')
            ->whereMonth('created_at', now()->month)
            ->sum('total_price');
        $dailyRevenue = Order::where('status', 'delivered')
            ->whereDate('created_at', now()->toDateString())
            ->sum('total_price');

        return [
            'total' => $totalRevenue,
            'monthly' => $monthlyRevenue,
            'daily' => $dailyRevenue,
        ];
    }

    private function getPopularMealsData()
    {
        return Meal::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(10)
            ->get(['id', 'name', 'order_items_count']);
    }

    private function getRatingDistribution()
    {
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = Rating::where('rating', $i)->count();
            $distribution[] = ['rating' => $i, 'count' => $count];
        }
        return $distribution;
    }

    private function getSystemLogs()
    {
        // This would typically read from Laravel's log files
        return [
            ['level' => 'info', 'message' => 'System started successfully', 'time' => now()->subMinutes(5)],
            ['level' => 'warning', 'message' => 'High memory usage detected', 'time' => now()->subMinutes(10)],
            ['level' => 'error', 'message' => 'Database connection timeout', 'time' => now()->subMinutes(15)],
        ];
    }
}
