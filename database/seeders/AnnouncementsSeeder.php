<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $announcements = [
            [
                'title' => 'Welcome to the New Watered App!',
                'content' => 'We are thrilled to launch the new and improved Watered app. Explore our new features including real-time announcements, enhanced sacred rituals, and a more vibrant community space. Thank you for being part of our journey!',
                'is_urgent' => true,
                'target_audience' => 'all',
                'scheduled_at' => now(),
            ],
            [
                'title' => 'Upcoming Event: Full Moon Ritual',
                'content' => 'Join us this Friday for a collective Full Moon Ritual. We will be connecting globally to harness the energy of the moon for clarity and spiritual growth. Don\'t miss out!',
                'is_urgent' => false,
                'target_audience' => 'premium',
                'scheduled_at' => now()->addDays(3),
            ],
            [
                'title' => 'Maintenance Update',
                'content' => 'We will be performing brief server maintenance this Sunday at 2 AM UTC. The app may be temporarily unavailable for about 15 minutes. We apologize for any inconvenience.',
                'is_urgent' => false,
                'target_audience' => 'all',
                'scheduled_at' => now()->addDays(5),
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::updateOrCreate(
                ['title' => $announcement['title']],
                $announcement
            );
        }
    }
}
