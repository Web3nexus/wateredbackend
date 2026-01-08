<?php

namespace App\Policies;

use App\Models\Language;
use App\Models\AdminUser;
use Illuminate\Auth\Access\Response;

class LanguagePolicy
{
    public function viewAny(AdminUser $user): bool
    {
        return true;
    }

    public function view(AdminUser $user, Language $language): bool
    {
        return true;
    }

    public function create(AdminUser $user): bool
    {
        return true;
    }

    public function update(AdminUser $user, Language $language): bool
    {
        return true;
    }

    public function delete(AdminUser $user, Language $language): bool
    {
        return true;
    }
}
