<?php

namespace App\Traits;

use Spatie\Permission\Exceptions\PermissionDoesNotExist;

trait HasModuleAccess
{
    public static function canViewAny(): bool
    {
        $permission = static::getPermissionName();
        
        if (!$permission) {
            return true;
        }

        try {
            return auth('admin')->user()?->hasPermissionTo($permission, 'admin') ?? false;
        } catch (PermissionDoesNotExist $e) {
            return false;
        }
    }

    /**
     * Define the permission name for this resource.
     * Override this in the resource class.
     */
    public static function getPermissionName(): ?string
    {
        return null;
    }
}
