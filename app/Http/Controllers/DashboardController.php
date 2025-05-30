<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\User;
use App\Models\Alert;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

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
                        'active_orders' => Order::where('status', '!=', 'delivered')->count(),
                    ],
                    'pendingAlerts' => Alert::where('resolved', false)->get(),
                ]);

            case 'staff':
                return Inertia::render('Staff/Dashboard', [
                    'user' => $user,
                    'recentOrders' => Order::orderByDesc('created_at')->take(10)->get(),
                ]);

            case 'customer':
                return Inertia::render('Customer/Dashboard', [
                    'user' => $user,
                    'recentOrders' => Order::where('user_id', $user->id)
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
