<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles for admin guard
        $roles = [
            ['name' => 'Super Admin', 'guard_name' => 'admin'],
            ['name' => 'Developer', 'guard_name' => 'admin'],
            ['name' => 'Admin', 'guard_name' => 'admin'],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate($roleData);
        }

        $this->command->info('Roles created successfully!');
        $this->command->info('Total roles: ' . Role::where('guard_name', 'admin')->count());
    }
}
