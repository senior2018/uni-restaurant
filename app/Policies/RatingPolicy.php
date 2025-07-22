<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Rating;
use App\Models\Order;

class RatingPolicy
{
    // Anyone can view ratings (for now)
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function view(User $user, Rating $rating)
    {
        return $user->isAdmin() || $user->isStaff() || $user->id === $rating->user_id;
    }

    public function create(User $user, Order $order)
    {
        return $user->id === $order->user_id && $order->status === 'delivered';
    }

    public function respond(User $user, Rating $rating)
    {
        return $user->isAdmin() || $user->isStaff();
    }
}
