<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Order;
use App\Models\User;
use App\Notifications\NewAlertNotification;
use App\Notifications\AlertRespondedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AlertController extends Controller
{
    use AuthorizesRequests;

    // List all alerts (admin/staff)
    public function index(Request $request)
    {
        $this->authorize('viewAny', Alert::class);
        $query = Alert::with(['user', 'order', 'staff']);
        if ($request->filled('resolved')) {
            $query->where('resolved', $request->resolved);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        $alerts = $query->latest()->paginate(20);
        // Map alerts to include responder info
        $alerts->getCollection()->transform(function ($alert) {
            return [
                'id' => $alert->id,
                'order_id' => $alert->order_id,
                'user_id' => $alert->user_id,
                'user' => $alert->user,
                'reason' => $alert->reason,
                'resolved' => $alert->resolved,
                'staff_response' => $alert->staff_response,
                'responded_at' => $alert->responded_at,
                'staff_id' => $alert->staff_id,
                'responder_name' => $alert->staff ? $alert->staff->name : null,
                'responder_role' => $alert->staff ? $alert->staff->role : null,
                'created_at' => $alert->created_at,
                'updated_at' => $alert->updated_at,
            ];
        });
        return Inertia::render('Shared/AlertsIndex', [
            'alerts' => $alerts,
            'user' => $request->user(),
            'role' => $request->user()->role,
        ]);
    }

    // Show a single alert
    public function show(Alert $alert)
    {
        $this->authorize('view', $alert);
        $alert->load(['user', 'order', 'staff']);
        return Inertia::render('Admin/Alerts/Show', [
            'alert' => $alert
        ]);
    }

    // Customer submits an alert
    public function store(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'reason' => 'required|string|max:1000',
        ]);
        $order = Order::findOrFail($validated['order_id']);
        $this->authorize('create', [Alert::class, $order]);
        // Only allow alert if order belongs to user and is not cancelled
        if ($order->user_id !== $user->id || $order->status === 'cancelled') {
            abort(403, 'You can only report issues for your active orders.');
        }
        // Prevent duplicate unresolved alert for same order
        if (Alert::where('user_id', $user->id)->where('order_id', $order->id)->where('resolved', false)->exists()) {
            return back()->with('error', 'You already have an unresolved alert for this order.');
        }
        $alert = Alert::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'reason' => $validated['reason'],
            'resolved' => false,
        ]);
        // Notify staff/admin (to be filled in)
        // Notify all staff/admin
        $staffAndAdmins = User::whereIn('role', ['admin', 'staff', 'super_admin'])->get();
        \Notification::send($staffAndAdmins, new NewAlertNotification($alert));
        return back()->with('success', 'Your alert has been submitted. We will address it soon.');
    }

    // Admin/staff respond to an alert
    public function respond(Request $request, Alert $alert)
    {
        $this->authorize('respond', $alert);
        $validated = $request->validate([
            'staff_response' => 'required|string|max:1000',
        ]);
        $alert->staff_id = $request->user()->id;
        $alert->staff_response = $validated['staff_response'];
        $alert->responded_at = now();
        $alert->save();
        // Notify customer (to be filled in)
        $alert->user->notify(new AlertRespondedNotification($alert));
        return back()->with('success', 'Response sent to customer.');
    }

    // Admin/staff mark alert as resolved
    public function resolve(Request $request, Alert $alert)
    {
        $this->authorize('resolve', $alert);
        $alert->resolved = true;
        $alert->save();
        return back()->with('success', 'Alert marked as resolved.');
    }

    // Admin alerts management page
    public function adminIndex(Request $request)
    {
        $this->authorize('viewAny', Alert::class);
        $query = Alert::with(['user', 'order', 'staff']);
        if ($request->filled('resolved')) {
            $query->where('resolved', $request->resolved);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        $alerts = $query->latest()->paginate(20);
        $alerts->getCollection()->transform(function ($alert) {
            return [
                'id' => $alert->id,
                'order_id' => $alert->order_id,
                'user_id' => $alert->user_id,
                'user' => $alert->user,
                'reason' => $alert->reason,
                'resolved' => $alert->resolved,
                'staff_response' => $alert->staff_response,
                'responded_at' => $alert->responded_at,
                'staff_id' => $alert->staff_id,
                'responder_name' => $alert->staff ? $alert->staff->name : null,
                'responder_role' => $alert->staff ? $alert->staff->role : null,
                'created_at' => $alert->created_at,
                'updated_at' => $alert->updated_at,
            ];
        });
        $unresolvedAlertCount = Alert::where('resolved', false)->count();
        return Inertia::render('Admin/AlertsIndex', [
            'alerts' => $alerts,
            'user' => $request->user(),
            'role' => 'admin',
            'unresolvedAlertCount' => $unresolvedAlertCount,
        ]);
    }

    // Staff alerts management page
    public function staffIndex(Request $request)
    {
        $this->authorize('viewAny', Alert::class);
        $query = Alert::with(['user', 'order', 'staff']);
        if ($request->filled('resolved')) {
            $query->where('resolved', $request->resolved);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        $alerts = $query->latest()->paginate(20);
        $alerts->getCollection()->transform(function ($alert) {
            return [
                'id' => $alert->id,
                'order_id' => $alert->order_id,
                'user_id' => $alert->user_id,
                'user' => $alert->user,
                'reason' => $alert->reason,
                'resolved' => $alert->resolved,
                'staff_response' => $alert->staff_response,
                'responded_at' => $alert->responded_at,
                'staff_id' => $alert->staff_id,
                'responder_name' => $alert->staff ? $alert->staff->name : null,
                'responder_role' => $alert->staff ? $alert->staff->role : null,
                'created_at' => $alert->created_at,
                'updated_at' => $alert->updated_at,
            ];
        });
        $unresolvedAlertCount = Alert::where('resolved', false)->count();
        return Inertia::render('Staff/AlertsIndex', [
            'alerts' => $alerts,
            'user' => $request->user(),
            'role' => 'staff',
            'unresolvedAlertCount' => $unresolvedAlertCount,
        ]);
    }

    public function destroy(Request $request, Alert $alert)
    {
        $this->authorize('delete', $alert);
        $alert->delete();
        return back()->with('success', 'Alert deleted.');
    }

    public function bulkDestroy(Request $request)
    {
        $this->authorize('viewAny', Alert::class); // Only admin can access this page
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return back()->with('error', 'No alerts selected.');
        }
        // Only allow admin to delete
        if (!$request->user()->isAdmin()) {
            abort(403);
        }
        Alert::whereIn('id', $ids)->delete();
        return back()->with('success', 'Selected alerts deleted.');
    }
}
