<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DailyWisdomSeeder extends Seeder
{
    public function run(): void
    {
        $wisdoms = [
            [
                'quote' => 'The river does not drink its own water; the tree does not eat its own fruit. Living for others is the rule of nature.',
                'author' => 'African Proverb',
                'active_date' => Carbon::now()->toDateString(),
                'background_image_url' => 'https://images.unsplash.com/photo-1518173946687-a4c8892bbd9f?auto=format&fit=crop&q=80',
            ],
            [
                'quote' => 'However long the night, the dawn will break.',
                'author' => 'African Proverb',
                'active_date' => Carbon::now()->addDays(1)->toDateString(),
                'background_image_url' => 'https://images.unsplash.com/photo-1470252649378-9c29740c9fa8?auto=format&fit=crop&q=80',
            ],
            [
                'quote' => 'If you want to go fast, go alone. If you want to go far, go together.',
                'author' => 'African Proverb',
                'active_date' => Carbon::now()->addDays(2)->toDateString(),
                'background_image_url' => 'https://images.unsplash.com/photo-1528642474498-1af0c17fd8c3?auto=format&fit=crop&q=80',
            ],
            [
                'quote' => 'Smooth seas do not make skillful sailors.',
                'author' => 'African Proverb',
                'active_date' => Carbon::now()->addDays(3)->toDateString(),
                'background_image_url' => 'https://images.unsplash.com/photo-1505118380757-91f5f5632de0?auto=format&fit=crop&q=80',
            ],
            [
                'quote' => 'A person who is not afraid of the sea will soon be drowned.',
                'author' => 'Nigerian Proverb',
                'active_date' => Carbon::now()->addDays(4)->toDateString(),
                'background_image_url' => 'https://images.unsplash.com/photo-1621609764095-6b23630d97ee?auto=format&fit=crop&q=80',
            ],
            [
                'quote' => 'Wisdom is like a baobab tree; no one individual can embrace it.',
                'author' => 'Akan Proverb',
                'active_date' => Carbon::now()->addDays(5)->toDateString(),
                'background_image_url' => 'https://images.unsplash.com/photo-1542273917363-3b1817f69a2d?auto=format&fit=crop&q=80',
            ],
            [
                'quote' => 'When you pray, move your feet.',
                'author' => 'Shared Wisdom',
                'active_date' => Carbon::now()->addDays(6)->toDateString(),
                'background_image_url' => 'https://images.unsplash.com/photo-1507608869269-bfac20366eb6?auto=format&fit=crop&q=80',
            ],
            [
                'quote' => 'Do not follow the path. Go where there is no path and leave a trail.',
                'author' => 'Ashanti Proverb',
                'active_date' => Carbon::now()->addDays(7)->toDateString(),
                'background_image_url' => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&q=80',
            ],
            [
                'quote' => 'Patience is the mother of a beautiful character.',
                'author' => 'Bantu Proverb',
                'active_date' => Carbon::now()->addDays(8)->toDateString(),
                'background_image_url' => 'https://images.unsplash.com/photo-1465146344425-f00d5f5c8f07?auto=format&fit=crop&q=80',
            ],
            [
                'quote' => 'Tomorrow belongs to the people who prepare for it today.',
                'author' => 'African Proverb',
                'active_date' => Carbon::now()->addDays(9)->toDateString(),
                'background_image_url' => 'https://images.unsplash.com/photo-1472214103451-9374bd1c798e?auto=format&fit=crop&q=80',
            ],
        ];

        foreach ($wisdoms as $wisdom) {
            DB::table('daily_wisdoms')->insert([
                'quote' => $wisdom['quote'],
                'author' => $wisdom['author'],
                'active_date' => $wisdom['active_date'],
                'background_image_url' => $wisdom['background_image_url'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
