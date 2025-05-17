<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Example: fetch the logged-in user and pass it to the dashboard
        return Inertia::render('Dashboard', [
            'user' => auth()->user(),
            'recentOrders' => [], // replace with actual data
            'pendingAlerts' => [] // replace with actual data
        ]);
    }
}
