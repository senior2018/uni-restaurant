<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->role === 'admin';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->isSuperAdmin() || $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Super admin can update anyone
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Users can update their own profile
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Super admin can delete anyone except other super admins
        if ($user->isSuperAdmin()) {
            return $model->role !== 'super_admin';
        }

        // Regular users cannot delete other users
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Super admin can force delete anyone except other super admins
        if ($user->isSuperAdmin()) {
            return $model->role !== 'super_admin';
        }

        return false;
    }

    /**
     * Determine whether the user can manage roles.
     */
    public function manageRoles(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view system analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view system configuration.
     */
    public function viewSystemConfig(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view system logs.
     */
    public function viewSystemLogs(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}
