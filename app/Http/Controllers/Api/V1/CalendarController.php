<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CalendarMonth;
use App\Models\CalendarDay;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Get all calendar months with their days
     */
    public function index()
    {
        $months = CalendarMonth::with('days')->orderBy('number')->get();

        return response()->json([
            'data' => $months,
        ]);
    }

    /**
     * Get details for a specific month
     */
    public function getMonth(int $number)
    {
        $month = CalendarMonth::with('days')
            ->where('number', $number)
            ->firstOrFail();

        return response()->json([
            'data' => $month,
        ]);
    }

    /**
     * Get special/sacred days
     */
    public function getSpecialDays()
    {
        $days = CalendarDay::with('month')
            ->where('is_sacred', true)
            ->get();

        return response()->json([
            'data' => $days,
        ]);
    }

    /**
     * Get today's calendar day based on Gregorian date
     * Implementation assumes Kemetic Year starts July 19th
     */
    public function getToday()
    {
        $today = now();
        $monthNumber = $today->month;
        $dayNumber = $today->day;

        // Static Mapping per User Rules
        $wateredMonths = [
            1 => ['name' => 'Djehuti / Odomankoma', 'deity' => 'Djehuti', 'meaning' => 'Beginning, Wisdom'],
            2 => ['name' => 'Hathor / Yemoja', 'deity' => 'Hathor', 'meaning' => 'Love, Beauty'],
            3 => ['name' => 'Sekhmet / Amadioha', 'deity' => 'Sekhmet', 'meaning' => 'Power, Healing'],
            4 => ['name' => 'Ma’at / Ovia', 'deity' => 'Ma’at', 'meaning' => 'Balance, Truth'],
            5 => ['name' => 'Geb / Osun', 'deity' => 'Geb', 'meaning' => 'Earth, Fertility'],
            6 => ['name' => 'Nut / Etegbere', 'deity' => 'Nut', 'meaning' => 'Sky, Mystery'],
            7 => ['name' => 'Auset / Rezi', 'deity' => 'Auset', 'meaning' => 'Magic, Motherhood'],
            8 => ['name' => 'Ausar / Tiurakh', 'deity' => 'Ausar', 'meaning' => 'Resurrection, Eternity'],
            9 => ['name' => 'Heru / Sango', 'deity' => 'Heru', 'meaning' => 'Victory, Kingship'],
            10 => ['name' => 'Seth / Kibuka', 'deity' => 'Seth', 'meaning' => 'Chaos, Transformation'],
            11 => ['name' => 'Nebeth-Het / Oya', 'deity' => 'Nebeth-Het', 'meaning' => 'Protection, Transition'],
            12 => ['name' => 'Anpu / Inkosazana', 'deity' => 'Anpu', 'meaning' => 'Guidance, Afterlife'],
        ];

        $currentMonthData = $wateredMonths[$monthNumber] ?? $wateredMonths[1];

        // Fetch DB Day Logic
        $month = CalendarMonth::where('number', $monthNumber)->first();
        $day = null;

        if ($month) {
            $day = CalendarDay::where('calendar_month_id', $month->id)
                ->where('day_number', $dayNumber)
                ->first();
        }

        return response()->json([
            'gregorian_date' => $today->toDateString(),
            'kemetic_date' => [
                'month_number' => $monthNumber,
                'day_number' => $dayNumber,
                'month_name' => $currentMonthData['name'],
                'deities' => $currentMonthData['deity'], // Simplified for UI
                'meaning' => $currentMonthData['meaning'],
                'year' => $today->year + 4241, // Optional: Keep Kemetic year offset if desired, or just Gregorian
                'gregorian_reference' => $today->format('F d'),
            ],
            'day_details' => $day, // Now includes activities/restrictions if seeded
        ]);
    }
}
