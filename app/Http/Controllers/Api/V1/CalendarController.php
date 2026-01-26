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
        $year = $today->year;

        // Kemetic New Year (Wepet Renpet) usually around July 19th
        $newYear = \Carbon\Carbon::create($year, 7, 19);

        if ($today->lt($newYear)) {
            $newYear = \Carbon\Carbon::create($year - 1, 7, 19);
        }

        $daysSinceNewYear = $today->diffInDays($newYear);

        // There are 12 months of 30 days
        $monthNumber = (int) floor($daysSinceNewYear / 30) + 1;
        $dayInMonth = (int) ($daysSinceNewYear % 30) + 1;

        // Handle Epagomenal days (Month 13)
        if ($monthNumber > 12) {
            $monthNumber = 13;
            $dayInMonth = (int) ($daysSinceNewYear - 360) + 1;
        }

        $month = CalendarMonth::where('number', $monthNumber)->first();
        $day = null;

        if ($month) {
            $day = CalendarDay::where('calendar_month_id', $month->id)
                ->where('day_number', $dayInMonth)
                ->first();
        }

        return response()->json([
            'gregorian_date' => $today->toDateString(),
            'kemetic_date' => [
                'month_number' => $monthNumber,
                'day_number' => $dayInMonth,
                'month_name' => $month?->display_name,
                'season' => $month?->season,
                'year' => $month?->year,
                'gregorian_reference' => $day?->gregorian_day,
            ],
            'day_details' => $day,
        ]);
    }
}
