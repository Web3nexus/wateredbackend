<?php

namespace Database\Seeders;

use App\Models\CalendarMonth;
use App\Models\CalendarDay;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class KemeticCalendarSeeder extends Seeder
{
    public function run(): void
    {
        // Prevent duplicates
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        CalendarDay::truncate();
        CalendarMonth::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $months = [
            1 => ['name' => 'January', 'custom' => 'Rezi', 'deity' => 'Auset', 'meaning' => 'Magic, Motherhood', 'season' => 'Peret'],
            2 => ['name' => 'February', 'custom' => 'Tiurakh', 'deity' => 'Ausar', 'meaning' => 'Resurrection, Eternity', 'season' => 'Peret'],
            3 => ['name' => 'March', 'custom' => 'Sango', 'deity' => 'Heru', 'meaning' => 'Victory, Kingship', 'season' => 'Shemu'],
            4 => ['name' => 'April', 'custom' => 'Kibuka', 'deity' => 'Seth', 'meaning' => 'Chaos, Transformation', 'season' => 'Shemu'],
            5 => ['name' => 'May', 'custom' => 'Oya', 'deity' => 'Nebeth-Het', 'meaning' => 'Protection, Transition', 'season' => 'Shemu'],
            6 => ['name' => 'June', 'custom' => 'Inkosazana', 'deity' => 'Anpu', 'meaning' => 'Guidance, Afterlife', 'season' => 'Shemu'],
            7 => ['name' => 'July', 'custom' => 'Odomankoma', 'deity' => 'Djehuti', 'meaning' => 'Beginning, Wisdom', 'season' => 'Akhet'],
            8 => ['name' => 'August', 'custom' => 'Yemoja', 'deity' => 'Hathor', 'meaning' => 'Love, Beauty', 'season' => 'Akhet'],
            9 => ['name' => 'September', 'custom' => 'Amadioha', 'deity' => 'Sekhmet', 'meaning' => 'Power, Healing', 'season' => 'Akhet'],
            10 => ['name' => 'October', 'custom' => 'Ovia', 'deity' => 'Ma’at', 'meaning' => 'Balance, Truth', 'season' => 'Akhet'],
            11 => ['name' => 'November', 'custom' => 'Osun', 'deity' => 'Geb', 'meaning' => 'Earth, Fertility', 'season' => 'Peret'],
            12 => ['name' => 'December', 'custom' => 'Etegbere', 'deity' => 'Nut', 'meaning' => 'Sky, Mystery', 'season' => 'Peret'],
        ];

        foreach ($months as $num => $data) {
            $month = CalendarMonth::create([
                'number' => $num,
                'standard_name' => $data['name'],
                'custom_name' => $data['custom'],
                'season' => $data['season'],
                'gregorian_reference' => $data['name'],
                'deities' => $data['deity'] ?? null,
                'meaning' => $data['meaning'] ?? null,
                'year' => date('Y'),
            ]);

            $daysInMonth = Carbon::create(2025, $num, 1)->daysInMonth;

            for ($d = 1; $d <= $daysInMonth; $d++) {
                CalendarDay::create([
                    'calendar_month_id' => $month->id,
                    'day_number' => $d,
                    'gregorian_day' => Carbon::create(2025, $num, $d)->format('M d'),
                ]);
            }
        }
    }
}
