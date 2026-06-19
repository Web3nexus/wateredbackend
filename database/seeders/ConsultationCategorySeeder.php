<?php

namespace Database\Seeders;

use App\Models\ConsultationCategory;
use Illuminate\Database\Seeder;

class ConsultationCategorySeeder extends Seeder
{
    public function run(): void
    {
        ConsultationCategory::updateOrCreate(
            ['slug' => 'temple_visit'],
            [
                'name' => 'Visit the Temple',
                'is_free' => true,
                'availability' => [
                    ['days' => [1, 2, 3, 5, 0], 'start' => '10:00', 'end' => '16:00'],
                    ['days' => [4, 6], 'start' => '07:00', 'end' => '18:00'],
                ],
                'description' => 'Free temple visitation — visitors may not meet Lord Uzih the priest during temple visits.',
                'sort_order' => 1,
            ]
        );

        ConsultationCategory::updateOrCreate(
            ['slug' => 'lord_uzih'],
            [
                'name' => 'Talk to Lord Uzih',
                'is_free' => false,
                'availability' => [
                    ['days' => [2, 3, 5], 'start' => '10:00', 'end' => '16:00'],
                ],
                'description' => 'Paid consultations with Lord Uzih.',
                'sort_order' => 2,
            ]
        );
    }
}
