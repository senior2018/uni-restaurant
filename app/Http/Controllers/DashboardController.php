<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\User;
use App\Models\Alert;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            // Show guest dashboard or home
            return Inertia::render('Guest/Home');
        }

        switch ($user->role) {
            case 'super_admin':
                return Inertia::render('SuperAdmin/Dashboard', [
                    'user' => $user,
                    'stats' => [
                        'total_users' => \App\Models\User::count(),
                        'total_admins' => \App\Models\User::where('role', 'admin')->count(),
                        'active_orders' => \App\Models\Order::where('status', '!=', 'delivered')->count(),
                    ],
                    'recentAdmins' => \App\Models\User::where('role', 'admin')
                        ->latest()
                        ->take(5)
                        ->get(['id', 'name', 'created_at']),
                ]);
            case 'admin':
                return Inertia::render('Admin/Dashboard', [
                    'user' => $user,
                    'stats' => [
                        'total_users' => User::count(),
                        'pending_orders' => Order::where('status', 'pending')->count(),
                        'preparing_orders' => Order::where('status', 'preparing')->count(),
                        'delivered_orders' => Order::where('status', 'delivered')->count(),
                        'cancelled_orders' => Order::where('status', 'cancelled')->count(),
                        'pending_cancellation_count' => Order::where('status', 'preparing')->where('cancellation_requested', true)->count(),
                        'unseen_cancellation_count' => Order::where('status', 'preparing')->where('cancellation_requested', true)->where('cancellation_request_seen', false)->count(),
                    ],
                    'pendingAlerts' => Alert::where('resolved', false)->get(),
                ]);

            case 'staff':
                return Inertia::render('Staff/Dashboard', [
                    'user' => $user,
                    'unassignedOrders' => Order::whereNull('staff_id')
                        ->where('status', 'pending')
                        ->orderByDesc('created_at')
                        ->take(20)
                        ->get(),
                    'myOrders' => Order::where('staff_id', $user->id)
                        ->where('status', '!=', 'cancelled')
                        ->orderByDesc('created_at')
                        ->take(20)
                        ->get(),
                ]);

            case 'customer':
                return Inertia::render('Customer/Dashboard', [
                    'user' => $user,
                    'orders' => Order::where('user_id', $user->id)
                        ->orderByDesc('created_at')
                        ->take(10)
                        ->get(),
                ]);

            default:
                abort(403, 'Unauthorized');
        }
    }

    public function listAdmins()
    {
        $admins = \App\Models\User::where('role', 'admin')
            ->latest()
            ->get(['id', 'name', 'email', 'created_at']);

        return Inertia::render('SuperAdmin/AdminList', [
            'admins' => $admins
        ]);
    }
}
