<?php

namespace App\Policies;

use App\Models\AdminUser;

class AdminUserPolicy
{
    public function viewAny(AdminUser $user): bool
    {
        return $user->hasPermissionTo('access_admin_users', 'admin');
    }

    public function view(AdminUser $user, AdminUser $adminUser): bool
    {
        return $user->hasPermissionTo('access_admin_users', 'admin');
    }

    public function create(AdminUser $user): bool
    {
        return $user->hasPermissionTo('access_admin_users', 'admin');
    }

    public function update(AdminUser $user, AdminUser $adminUser): bool
    {
        if (!$user->hasPermissionTo('access_admin_users', 'admin')) {
            return false;
        }

        if ($adminUser->isDeveloper() && !$user->isDeveloper()) {
            return false;
        }

        return true;
    }

    public function delete(AdminUser $user, AdminUser $adminUser): bool
    {
        if (!$user->hasPermissionTo('access_admin_users', 'admin')) {
            return false;
        }

        if ($adminUser->isDeveloper() && !$user->isDeveloper()) {
            return false;
        }

        return true;
    }

    public function deleteAny(AdminUser $user): bool
    {
        if (!$user->hasPermissionTo('access_admin_users', 'admin')) {
            return false;
        }

        return $user->isDeveloper();
    }
}
