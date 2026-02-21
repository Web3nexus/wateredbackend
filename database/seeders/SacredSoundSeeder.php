<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SacredSound;

class SacredSoundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sounds = [
            [
                'title' => 'Ancient Chant',
                'file_path' => 'sounds/ancient_chant.mp3', // Placeholder, admin can upload real ones
                'type' => 'notification',
                'is_active' => true,
            ],
            [
                'title' => 'Watered Bell',
                'file_path' => 'sounds/watered_bell.mp3',
                'type' => 'notification',
                'is_active' => true,
            ],
            [
                'title' => 'Sacred Drum',
                'file_path' => 'sounds/sacred_drum.mp3',
                'type' => 'notification',
                'is_active' => true,
            ],
            [
                'title' => 'Nature Ambience',
                'file_path' => 'sounds/nature_ambience.mp3',
                'type' => 'notification',
                'is_active' => true,
            ]
        ];

        foreach ($sounds as $sound) {
            SacredSound::updateOrCreate(
                ['title' => $sound['title']],
                $sound
            );
        }
    }
}
