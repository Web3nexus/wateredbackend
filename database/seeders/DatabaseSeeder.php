<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            KemeticCalendarSeeder::class,
            DailyWisdomSeeder::class,
            EmailTemplateSeeder::class,
            InformationalSeeder::class,
            TempleSeeder::class,
            FeatureTwoSeeder::class,
            NimaSedaniContentSeeder::class,
            LandingPageFeaturesSeeder::class,
            TeachingsSeeder::class,
            AnnouncementsSeeder::class,
        ]);
    }
}
