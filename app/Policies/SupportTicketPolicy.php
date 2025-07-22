<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SupportTicket;

class SupportTicketPolicy
{
    /**
     * Determine whether the user can view any support tickets.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    /**
     * Determine whether the user can view a specific support ticket.
     */
    public function view(User $user, SupportTicket $ticket): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    // Add more methods as needed (create, update, delete, etc.)
}
