<?php

namespace App\Console\Commands;

use App\Models\AdminUser;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class PromoteToDeveloper extends Command
{
    protected $signature = 'admin:make-dev {email : The email of the admin user to promote}';
    protected $description = 'Promote an admin user to the Developer role';

    public function handle(): int
    {
        $email = $this->argument('email');
        $admin = AdminUser::where('email', $email)->first();

        if (!$admin) {
            $this->error("No admin found with email: {$email}");
            return Command::FAILURE;
        }

        if ($admin->isDeveloper()) {
            $this->warn("{$admin->name} ({$email}) is already a Developer.");
            return Command::SUCCESS;
        }

        $role = Role::where('name', 'Developer')->where('guard_name', 'admin')->first();

        if (!$role) {
            $this->error('Developer role not found. Run the role seeder first.');
            return Command::FAILURE;
        }

        $admin->assignRole($role);

        $this->info("✅ {$admin->name} ({$email}) has been promoted to Developer.");
        $this->line("They now have full access to all admin features.");

        return Command::SUCCESS;
    }
}
