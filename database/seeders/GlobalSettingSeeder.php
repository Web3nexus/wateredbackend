<?php

namespace Database\Seeders;

use App\Models\GlobalSetting;
use Illuminate\Database\Seeder;

class GlobalSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GlobalSetting::updateOrCreate(
            ['id' => 1],
            [
                'site_name' => 'Watered',
                'site_description' => 'Spiritual teachings and sacred traditions',
                'primary_color' => '#6366f1',
                'secondary_color' => '#8b5cf6',
                'default_language' => 'en',
                'supported_languages' => ['en', 'es', 'fr', 'ar'],
                'maintenance_mode' => false,
                'privacy_policy' => '<h1>Privacy Policy</h1><p>This is the privacy policy for Watered.</p>',
                'terms_of_service' => '<h1>Terms of Service</h1><p>These are the terms of service for Watered.</p>',
            ]
        );
    }
}
