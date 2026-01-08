<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\Tradition;

class TraditionPolicy
{
    public function viewAny(AdminUser $user): bool
    {
        return true;
    }

    public function view(AdminUser $user, Tradition $tradition): bool
    {
        return true;
    }

    public function create(AdminUser $user): bool
    {
        return true;
    }

    public function update(AdminUser $user, Tradition $tradition): bool
    {
        return true;
    }

    public function delete(AdminUser $user, Tradition $tradition): bool
    {
        return true;
    }

    public function restore(AdminUser $user, Tradition $tradition): bool
    {
        return true;
    }

    public function forceDelete(AdminUser $user, Tradition $tradition): bool
    {
        return true;
    }
}
