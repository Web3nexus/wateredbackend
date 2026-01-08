<?php

namespace Database\Seeders;

use App\Models\Audio;
use App\Models\Tradition;
use Illuminate\Database\Seeder;

class AudioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $traditions = Tradition::all();

        if ($traditions->isEmpty()) {
            return;
        }

        $audios = [
            [
                'title' => 'Morning Adhkar & Meditation',
                'description' => 'A serene recitation of morning remembrances to start the day with clarity and peace.',
                'audio_url' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&q=80&w=800',
                'duration' => '12:45',
                'author' => 'Imam Al-Hadi',
                'published_at' => now()->subDays(2),
                'is_active' => true,
            ],
            [
                'title' => 'The Path of Inner Peace',
                'description' => 'A deep exploration into the ancient teachings of silence and presence.',
                'audio_url' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1528715471579-d1bcf0ba5e83?auto=format&fit=crop&q=80&w=800',
                'duration' => '45:20',
                'author' => 'Scholar Sarah Khan',
                'published_at' => now()->subDays(5),
                'is_active' => true,
            ],
            [
                'title' => 'Echoes of the Ancients',
                'description' => 'Chants and recitations from the 12th century, preserved in their original form.',
                'audio_url' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&q=80&w=800',
                'duration' => '08:15',
                'author' => 'Brother Yusuf',
                'published_at' => now()->subDays(10),
                'is_active' => true,
            ],
        ];

        foreach ($audios as $audioData) {
            $audioData['tradition_id'] = $traditions->random()->id;
            Audio::create($audioData);
        }
    }
}
