<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AdminUserPolicy
{
    public function viewAny(AdminUser $user): bool
    {
        return true;
    }

    public function view(AdminUser $user, AdminUser $adminUser): bool
    {
        return true;
    }

    public function create(AdminUser $user): bool
    {
        return true;
    }

    public function update(AdminUser $user, AdminUser $adminUser): bool
    {
        return true;
    }

    public function delete(AdminUser $user, AdminUser $adminUser): bool
    {
        return true;
    }
}
