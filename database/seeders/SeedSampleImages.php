<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\GlobalSetting;

class SeedSampleImages extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Update Global Settings with high-quality Unsplash images
        $settings = GlobalSetting::first();
        if ($settings) {
            $settings->update([
                'hero_image' => 'https://images.unsplash.com/photo-1518182798836-70e2380d0d82?q=80&w=2924&auto=format&fit=crop',
                'rituals_image' => 'https://images.unsplash.com/photo-1545231499-f2c90c00ce52?q=80&w=2938&auto=format&fit=crop',
            ]);
        }

        // 2. Update Events with category-appropriate Unsplash images
        $eventImages = [
            'Osun Festival' => 'https://images.unsplash.com/photo-1599839575945-a9e5af0c3fa5?auto=format&fit=crop&q=80',
            'Sacred Water Symposium' => 'https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?auto=format&fit=crop&q=80',
            'Day of Amadioha (Fire Feast)' => 'https://images.unsplash.com/photo-1548013146-72479768bada?q=80&w=2952&auto=format&fit=crop',
        ];

        foreach ($eventImages as $title => $imageUrl) {
            Event::where('title', $title)->update(['banner_image' => $imageUrl]);
        }

        // Fallback for any other events
        Event::whereNull('banner_image')
            ->whereNull('image_url')
            ->update(['banner_image' => 'https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?auto=format&fit=crop&q=80']);
    }
}
