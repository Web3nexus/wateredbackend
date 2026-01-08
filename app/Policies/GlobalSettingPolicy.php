<?php

namespace App\Policies;

use App\Models\GlobalSetting;
use App\Models\AdminUser;
use Illuminate\Auth\Access\Response;

class GlobalSettingPolicy
{
    public function viewAny(AdminUser $user): bool
    {
        return true;
    }

    public function view(AdminUser $user, GlobalSetting $globalSetting): bool
    {
        return true;
    }

    public function create(AdminUser $user): bool
    {
        return false;
    }

    public function update(AdminUser $user, GlobalSetting $globalSetting): bool
    {
        return true;
    }

    public function delete(AdminUser $user, GlobalSetting $globalSetting): bool
    {
        return false;
    }
}
