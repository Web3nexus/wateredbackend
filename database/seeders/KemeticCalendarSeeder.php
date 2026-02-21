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
            1 => ['name' => 'Odomankoma', 'deity' => 'Djehuti', 'meaning' => 'Beginning, Wisdom', 'season' => 'Akhet', 'ref' => 'July 19 - Aug 17'],
            2 => ['name' => 'Yemoja', 'deity' => 'Hathor', 'meaning' => 'Love, Beauty', 'season' => 'Akhet', 'ref' => 'Aug 18 - Sept 16'],
            3 => ['name' => 'Amadioha', 'deity' => 'Sekhmet', 'meaning' => 'Power, Healing', 'season' => 'Akhet', 'ref' => 'Sept 17 - Oct 16'],
            4 => ['name' => 'Ovia', 'deity' => 'Ma’at', 'meaning' => 'Balance, Truth', 'season' => 'Akhet', 'ref' => 'Oct 17 - Nov 15'],
            5 => ['name' => 'Osun', 'deity' => 'Geb', 'meaning' => 'Earth, Fertility', 'season' => 'Peret', 'ref' => 'Nov 16 - Dec 15'],
            6 => ['name' => 'Etegbere', 'deity' => 'Nut', 'meaning' => 'Sky, Mystery', 'season' => 'Peret', 'ref' => 'Dec 16 - Jan 14'],
            7 => ['name' => 'Rezi', 'deity' => 'Auset', 'meaning' => 'Magic, Motherhood', 'season' => 'Peret', 'ref' => 'Jan 15 - Feb 13'],
            8 => ['name' => 'Tiurakh', 'deity' => 'Ausar', 'meaning' => 'Resurrection, Eternity', 'season' => 'Peret', 'ref' => 'Feb 14 - Mar 15'],
            9 => ['name' => 'Sango', 'deity' => 'Heru', 'meaning' => 'Victory, Kingship', 'season' => 'Shemu', 'ref' => 'Mar 16 - Apr 14'],
            10 => ['name' => 'Kibuka', 'deity' => 'Seth', 'meaning' => 'Chaos, Transformation', 'season' => 'Shemu', 'ref' => 'Apr 15 - May 14'],
            11 => ['name' => 'Oya', 'deity' => 'Nebeth-Het', 'meaning' => 'Protection, Transition', 'season' => 'Shemu', 'ref' => 'May 15 - Jun 13'],
            12 => ['name' => 'Inkosazana', 'deity' => 'Anpu', 'meaning' => 'Guidance, Afterlife', 'season' => 'Shemu', 'ref' => 'Jun 14 - Jul 13'],
            13 => ['name' => 'Festival Days', 'deity' => 'Multiple', 'meaning' => 'Festival, Completion', 'season' => 'Festival', 'ref' => 'July 14 - July 18'],
        ];

        // Anchor date for calculation: July 19th
        $anchor = Carbon::create(2025, 7, 19);

        foreach ($months as $num => $data) {
            $month = CalendarMonth::create([
                'number' => $num,
                'standard_name' => $data['name'],
                'season' => $data['season'],
                'gregorian_reference' => $data['ref'],
                'deities' => $data['deity'] ?? null,
                'meaning' => $data['meaning'] ?? null,
                'year' => '6262',
            ]);

            $daysInMonth = ($num === 13) ? 5 : 30;

            for ($d = 1; $d <= $daysInMonth; $d++) {
                CalendarDay::create([
                    'calendar_month_id' => $month->id,
                    'day_number' => $d,
                    'gregorian_day' => $anchor->format('M d'),
                ]);
                $anchor->addDay();
            }
        }
    }
}
