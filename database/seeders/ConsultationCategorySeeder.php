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
                'description' => 'Free temple visitation — visitors may not meet Lord Uzih the priest during temple visits.',
                'sort_order' => 1,
            ]
        );

        ConsultationCategory::updateOrCreate(
            ['slug' => 'lord_uzih'],
            [
                'name' => 'Talk to Lord Uzih',
                'is_free' => false,
                'price' => 50000.00,
                'description' => 'Paid consultations with Lord Uzih.',
                'sort_order' => 2,
            ]
        );
    }
}
