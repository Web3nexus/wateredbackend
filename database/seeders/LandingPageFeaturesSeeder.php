<?php

namespace Database\Seeders;

use App\Models\LandingPageFeature;
use Illuminate\Database\Seeder;

class LandingPageFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First delete existing features to avoid duplicates if re-running
        LandingPageFeature::truncate();

        $features = [
            [
                'title' => 'Sacred Wisdom & Daily Teachings',
                'description' => 'Access a vast collection of ancient spiritual texts and modern interpretations. Receive daily wisdom to nourish your spirit and guide your growth.',
                'image_position' => 'left',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Divine Clarity & Guidance',
                'description' => 'Navigate your spiritual path with personalized insights and expert support. Experience a clearer connection to your higher self and destiny.',
                'image_position' => 'right',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Sacred Rituals & Community',
                'description' => 'Join a vibrant family of seekers. Participate in guided rituals and connect with others on the same spiritual path wherever you are in the world.',
                'image_position' => 'left',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($features as $feature) {
            LandingPageFeature::create($feature);
        }
    }
}
