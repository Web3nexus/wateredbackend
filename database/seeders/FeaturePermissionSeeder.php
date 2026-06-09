<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\AdminUser;

class FeaturePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = 'admin';

        $features = [
            'access_library' => 'Manage Spiritual Library (Books, Chapters, Traditions)',
            'access_audio' => 'Manage Audio & Sounds (Audios, Sacred Sounds)',
            'access_shop' => 'Manage Shop (Products, Orders, Applications)',
            'access_rituals' => 'Manage Rituals & Teachings (Incantations, Temples)',
            'access_events' => 'Manage Events & News (Announcements, Newsletters)',
            'access_appointments' => 'Manage Appointments (Consultations, Statuses)',
            'access_users' => 'Manage Users (App Users, Staff)',
            'access_financials' => 'Manage Financials (Revenue, Subscriptions)',
            'access_settings' => 'Manage Settings (Global Settings, Templates, CMS, Languages)',
            'access_discovery' => 'Manage Discovery (Temples, Locations)',
            'access_subscriptions' => 'Manage Subscriptions (Apple, Paystack)',
            'access_spiritual_practices' => 'Manage Spiritual Practices (Orders, Incantations, Rituals)',
            'access_community' => 'Manage Community (Newsletters, Announcements, Teachings)',
            'access_calendar' => 'Manage Calendar (Holidays, Watered Calendar)',
            'access_content' => 'Manage Content (FAQs)',
            'access_sacred_texts' => 'Manage Sacred Texts (Entries, Chapters, Nima Sedani)',
            'access_deities' => 'Manage Deities',
            'access_teaching' => 'Manage Teaching',
            'access_analytics' => 'Manage Analytics (User Activity)',
        ];

        foreach ($features as $name => $description) {
            Permission::findOrCreate($name, $guard);
        }

        // Create Developer Role if it doesn't exist
        $devRole = Role::findOrCreate('Developer', $guard);
        $devRole->givePermissionTo(Permission::where('guard_name', $guard)->get());

        // Create Staff Role
        Role::findOrCreate('Staff', $guard);

        // Assign Developer role to first admin if they don't have it
        $firstAdmin = AdminUser::first();
        if ($firstAdmin) {
            $firstAdmin->assignRole($devRole);
        }
    }
}
