<?php

namespace App\Console\Commands;

use App\Models\AdminUser;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AssignAdminRole extends Command
{
    protected $signature = 'admin:assign-role {email} {role}';
    protected $description = 'Assign a role to an admin user';

    public function handle()
    {
        $email = $this->argument('email');
        $roleName = $this->argument('role');

        $admin = AdminUser::where('email', $email)->first();

        if (!$admin) {
            $this->error("Admin user with email {$email} not found.");
            return 1;
        }

        $role = Role::where('name', $roleName)->where('guard_name', 'admin')->first();

        if (!$role) {
            $this->error("Role '{$roleName}' not found for admin guard.");
            $this->info("Available roles:");
            Role::where('guard_name', 'admin')->pluck('name')->each(fn($name) => $this->line("  - {$name}"));
            return 1;
        }

        // Remove all existing roles and assign the new one
        $admin->syncRoles([$role]);

        $this->info("Successfully assigned '{$roleName}' role to {$admin->name} ({$email})");
        return 0;
    }
}
