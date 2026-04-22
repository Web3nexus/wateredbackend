<?php

namespace App\Traits;

trait HasModuleAccess
{
    public static function canViewAny(): bool
    {
        $permission = static::getPermissionName();
        
        if (!$permission) {
            return true;
        }

        return auth('admin')->user()?->hasPermissionTo($permission, 'admin') ?? false;
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
