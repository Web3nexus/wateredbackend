<?php

namespace Database\Seeders;

use App\Models\Audio;
use App\Models\Audio;
use App\Models\ContentCategory;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ContentCategory::all();
        // Create categories if missing
        if ($categories->isEmpty()) {
            $catAudio = ContentCategory::create(['name' => 'Meditation', 'slug' => 'meditation', 'type' => 'audio', 'is_active' => true]);
        } else {
            $catAudio = $categories->where('type', 'audio')->first() ?? $categories->first();
        }

        // 3. Audio
        Audio::create([
            'title' => 'Morning Meditation',
            'description' => 'Start your day with peace.',
            'audio_url' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1518609878373-06d740f60d8b?auto=format&fit=crop&q=80',
            'duration' => '06:12',
            'author' => 'Guru Test',
            'published_at' => now(),
            'is_active' => true,
            'is_featured' => true,
            'category_id' => $catAudio->id,
            'tradition_id' => 1,
        ]);
    }
}
