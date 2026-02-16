<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reminder;
use Illuminate\Support\Facades\Storage;

class AudioResourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure sounds exist (Download if missing - handled by terminal command but safe here too)
        $sounds = [
            'sounds/alarm.mp3',
            'sounds/reminder.mp3',
        ];

        foreach ($sounds as $sound) {
            if (!Storage::disk('public')->exists($sound)) {
                $this->command->warn("Sound file missing: $sound. Please ensure it is in storage/app/public/sounds/");
            }
        }

        // 2. Update existing Reminders with default sound if they have none or invalid ones
        Reminder::whereNull('sound_path')
            ->orWhere('sound_path', '')
            ->update(['sound_path' => 'sounds/reminder.mp3']);

        $this->command->info('Audio resources seeded and reminders updated.');
    }
}
