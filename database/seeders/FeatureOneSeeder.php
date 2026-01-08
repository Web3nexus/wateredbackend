<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\GlobalSetting;
use App\Models\Language;
use App\Models\Status;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FeatureOneSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'admin']);

        // 2. Admin User
        $admin = AdminUser::firstOrCreate(
            ['email' => 'admin@watered.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole($superAdminRole);

        // 3. Languages
        Language::firstOrCreate(['code' => 'en'], ['name' => 'English', 'is_default' => true, 'is_active' => true]);
        Language::firstOrCreate(['code' => 'ar'], ['name' => 'Arabic', 'is_default' => false, 'is_active' => true]);

        // 4. Statuses (system-locked)
        $statuses = [
            ['name' => 'Draft', 'is_system' => true],
            ['name' => 'Published', 'is_system' => true],
            ['name' => 'Archived', 'is_system' => true],
        ];

        foreach ($statuses as $status) {
            Status::firstOrCreate(
                ['slug' => Str::slug($status['name'])],
                [
                    'name' => $status['name'],
                    'is_system' => $status['is_system'],
                ]
            );
        }

        // 5. Global Settings (Singleton)
        if (GlobalSetting::count() === 0) {
            GlobalSetting::create([
                'site_name' => 'Watered',
                'site_description' => 'The Bible/Quran-style structured reading platform.',
                'contact_email' => 'contact@watered.com',
            ]);
        }
    }
}