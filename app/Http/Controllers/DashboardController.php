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
}
