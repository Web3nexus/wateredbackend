<?php

namespace Database\Seeders;

use App\Models\ConsultationType;
use Illuminate\Database\Seeder;

class ConsultationTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            // CATEGORY A: Visit the Temple
            [
                'name' => 'Visit the Temple',
                'description' => 'Visitors may not meet Lord Uzih the priest during temple visits.',
                'price' => 0,
                'duration_minutes' => 60,
                'category' => 'temple_visit',
                'is_active' => true,
            ],
            // CATEGORY B: Talk to Lord Uzih
            [
                'name' => 'Phone Call',
                'description' => 'A private session via phone with Lord Uzih.',
                'price' => 50000,
                'duration_minutes' => 30,
                'category' => 'lord_uzih',
                'is_active' => true,
            ],
            [
                'name' => 'Meet Face-to-Face',
                'description' => 'A private in-person meeting with Lord Uzih.',
                'price' => 100000,
                'duration_minutes' => 60,
                'category' => 'lord_uzih',
                'is_active' => true,
            ],
            [
                'name' => 'Spiritual Consultation',
                'description' => 'An in-depth spiritual consultation session with Lord Uzih.',
                'price' => 300000,
                'duration_minutes' => 90,
                'category' => 'lord_uzih',
                'is_active' => true,
            ],
        ];

        foreach ($types as $type) {
            ConsultationType::updateOrCreate(['name' => $type['name']], $type);
        }

        // Deactivate old types if they exist and are not in the new list
        ConsultationType::whereNotIn('name', array_column($types, 'name'))->update(['is_active' => false]);
    }
}
