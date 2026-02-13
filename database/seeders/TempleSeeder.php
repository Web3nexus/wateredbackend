<?php

namespace Database\Seeders;

use App\Models\Temple;
use Illuminate\Database\Seeder;

class TempleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $temples = [
            [
                'name' => 'Temple of the Sacred Waters',
                'description' => 'A serene sanctuary dedicated to the healing powers of water. Visitors can partake in cleansing rituals and meditation by the crystal-clear streams.',
                'address' => '123 River Spirit Way, Aqaba',
                'latitude' => 29.531919,
                'longitude' => 35.006087,
                'image_url' => 'https://images.unsplash.com/photo-1545231499-f2c90c00ce52?q=80&w=2938&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Solar Flare Shrine',
                'description' => 'Dedicated to the sun god, this temple sits atop a high hill, catching the first and last rays of delight. Perfect for sunrise meditations.',
                'address' => '88 Sunburst Ave, Heliopolis',
                'latitude' => 30.130526,
                'longitude' => 31.411132,
                'image_url' => 'https://images.unsplash.com/photo-1548013146-72479768bada?q=80&w=2952&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Forest Whispers Grove',
                'description' => 'Hidden deep within the ancient woods, this open-air temple connects you directly with the spirits of nature. A place of deep silence and grounding.',
                'address' => '42 Deep Woods Ln, Eldertree',
                'latitude' => 51.178882,
                'longitude' => -1.826215,
                'image_url' => 'https://images.unsplash.com/photo-1589823901763-71ab52994993?q=80&w=2938&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Crystal Cavern Sanctuary',
                'description' => 'Located inside a glowing cavern, this sanctuary offers a unique acoustic environment for chanting and sound healing.',
                'address' => '7 Underground Rd, Mineralia',
                'latitude' => 39.739236,
                'longitude' => -104.990251,
                'image_url' => 'https://images.unsplash.com/photo-1518182798836-70e2380d0d82?q=80&w=2924&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Temple of Eternal Flame',
                'description' => 'House of the undying fire. A place for transformation and burning away the old to make way for the new.',
                'address' => '1 Fire Keep, Ember',
                'latitude' => 35.689487,
                'longitude' => 139.691706,
                'image_url' => 'https://images.unsplash.com/photo-1590005244569-80cf89ddce92?q=80&w=2942&auto=format&fit=crop',
                'is_active' => true,
            ],
        ];

        foreach ($temples as $temple) {
            Temple::updateOrCreate(
                ['name' => $temple['name']],
                $temple
            );
        }
    }
}
