<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Teaching', 'type' => 'video', 'slug' => 'teaching'],
            ['name' => 'Music Video', 'type' => 'video', 'slug' => 'music-video'],
            ['name' => 'Ritual', 'type' => 'video', 'slug' => 'ritual'],
            ['name' => 'Documentary', 'type' => 'video', 'slug' => 'documentary'],

            ['name' => 'Incantation', 'type' => 'audio', 'slug' => 'incantation'],
            ['name' => 'Music', 'type' => 'audio', 'slug' => 'music'],
            ['name' => 'Sermons', 'type' => 'audio', 'slug' => 'sermons'],
            ['name' => 'Meditation', 'type' => 'audio', 'slug' => 'meditation'],
        ];

        foreach ($categories as $category) {
            \App\Models\ContentCategory::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
