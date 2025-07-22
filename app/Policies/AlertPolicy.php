<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Alert;
use App\Models\Order;

class AlertPolicy
{
    // Only staff/admin can view all alerts
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function view(User $user, Alert $alert)
    {
        return $user->isAdmin() || $user->isStaff() || $user->id === $alert->user_id;
    }

    public function create(User $user, Order $order)
    {
        return $user->id === $order->user_id && $order->status !== 'cancelled';
    }

    public function respond(User $user, Alert $alert)
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function resolve(User $user, Alert $alert)
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function delete(User $user, Alert $alert)
    {
        return $user->isAdmin();
    }
}
