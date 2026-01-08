<?php

namespace App\Policies;

use App\Models\Status;
use App\Models\AdminUser;
use Illuminate\Auth\Access\Response;

class StatusPolicy
{
    public function viewAny(AdminUser $user): bool
    {
        return true;
    }

    public function view(AdminUser $user, Status $status): bool
    {
        return true;
    }

    public function create(AdminUser $user): bool
    {
        return true;
    }

    public function update(AdminUser $user, Status $status): bool
    {
        return true;
    }

    public function delete(AdminUser $user, Status $status): bool
    {
        return !$status->is_system;
    }
}
