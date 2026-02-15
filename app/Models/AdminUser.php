<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class AdminUser extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\AdminUserFactory> */
    use HasRoles, Notifiable;

    protected $guard_name = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if this admin user has the Developer role
     */
    public function isDeveloper(): bool
    {
        return $this->hasRole('Developer', 'admin');
    }

    /**
     * Boot method to add model event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Prevent deletion of users with Developer role
        static::deleting(function ($admin) {
            if ($admin->isDeveloper()) {
                throw new \Exception('Cannot delete admin users with Developer role. This role is protected.');
            }
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true; // Further restricted by roles/permissions if needed
    }
}