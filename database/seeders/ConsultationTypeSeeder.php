<?php

namespace Database\Seeders;

use App\Models\ConsultationType;
use Illuminate\Database\Seeder;

class ConsultationTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'General Spiritual Guidance',
                'description' => 'A introductory session to discuss your spiritual journey and path.',
                'price' => 50,
                'duration_minutes' => 60,
                'category' => 'general',
            ],
            [
                'name' => 'Sacred Text Reading',
                'description' => 'In-depth analysis and guidance based on our sacred books.',
                'price' => 75,
                'duration_minutes' => 45,
                'category' => 'divination',
            ],
            [
                'name' => 'Healing Consultation',
                'description' => 'Focused on emotional and spiritual well-being.',
                'price' => 100,
                'duration_minutes' => 90,
                'category' => 'healing',
            ],
        ];

        foreach ($types as $type) {
            ConsultationType::updateOrCreate(['name' => $type['name']], $type);
        }
    }
}
