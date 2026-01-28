<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user in the admins table
        AdminUser::firstOrCreate(
            ['email' => 'admin@watered.app'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Admin user created in admins table!');
        $this->command->info('Email: admin@watered.app');
        $this->command->info('Password: admin123');
        $this->command->info('URL: /securegate');
    }
}
