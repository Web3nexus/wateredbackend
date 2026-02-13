<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tradition;

class UpdateTraditionImagesSeeder extends Seeder
{
    public function run(): void
    {
        $traditions = [
            'yoruba' => 'https://images.unsplash.com/photo-1599839575945-a9e5af0c3fa5?auto=format&fit=crop&q=80', // River/Nature for Oshun/Yemaya context
            'igbo' => 'https://images.unsplash.com/photo-1547471080-166298e77a1c?auto=format&fit=crop&q=80', // African art/mask texture
            'kemet' => 'https://images.unsplash.com/photo-1539650116455-251c93c0dd7d?auto=format&fit=crop&q=80', // Pyramids/Egypt
            'akan' => 'https://images.unsplash.com/photo-1523805009345-7448845a9e53?auto=format&fit=crop&q=80', // Kente/Texture
            'zulu' => 'https://images.unsplash.com/photo-1523805009345-7448845a9e53?auto=format&fit=crop&q=80', // Texture/Landscape
            'dogon' => 'https://images.unsplash.com/photo-1444703686981-a3abbc4d4fe3?auto=format&fit=crop&q=80', // Starry Sky
            'hausa' => 'https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?auto=format&fit=crop&q=80', // Savannah
            'ganda' => 'https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?auto=format&fit=crop&q=80', // Landscape
            'san' => 'https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?auto=format&fit=crop&q=80', // Landscape/Rock
            'maasai' => 'https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?auto=format&fit=crop&q=80', // Landscape
            'sumerian' => 'https://images.unsplash.com/photo-1599839575945-a9e5af0c3fa5?auto=format&fit=crop&q=80', // Ancient/Nature
            'origins' => 'https://images.unsplash.com/photo-1444703686981-a3abbc4d4fe3?auto=format&fit=crop&q=80', // Stars
        ];

        foreach ($traditions as $slug => $imageUrl) {
            Tradition::where('slug', $slug)->update(['deity_image_url' => $imageUrl]);
        }
    }
}
