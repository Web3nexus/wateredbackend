<?php

namespace Database\Seeders;

use App\Models\Audio;
use App\Models\Video;
use App\Models\ContentCategory;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ContentCategory::all();
        // Create categories if missing
        if ($categories->isEmpty()) {
            $catVideo = ContentCategory::create(['name' => 'Teaching', 'slug' => 'teaching', 'type' => 'video', 'is_active' => true]);
            $catAudio = ContentCategory::create(['name' => 'Meditation', 'slug' => 'meditation', 'type' => 'audio', 'is_active' => true]);
        } else {
            $catVideo = $categories->where('type', 'video')->first() ?? $categories->first();
            $catAudio = $categories->where('type', 'audio')->first() ?? $categories->first();
        }

        // 1. YouTube Video
        Video::create([
            'title' => 'Nature Relaxation',
            'description' => 'A beautiful look at nature.',
            'youtube_url' => 'https://www.youtube.com/watch?v=ImJ98eC1X6Y', // 4K Nature
            'video_type' => 'youtube',
            'thumbnail_url' => 'https://img.youtube.com/vi/ImJ98eC1X6Y/maxresdefault.jpg',
            'duration' => '10:00',
            'published_at' => now(),
            'is_active' => true,
            'is_featured' => true,
            'category_id' => $catVideo->id,
            'tradition_id' => 1, // Default tradition
        ]);

        // 2. Direct File Video (Public Test URL)
        Video::create([
            'title' => 'Big Buck Bunny (Test MP4)',
            'description' => 'Testing direct file playback.',
            'storage_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
            'video_type' => 'file',
            'thumbnail_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/images/BigBuckBunny.jpg',
            'duration' => '09:56',
            'published_at' => now()->subDay(),
            'is_active' => true,
            'is_featured' => false,
            'category_id' => $catVideo->id,
            'tradition_id' => 1,
        ]);

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
