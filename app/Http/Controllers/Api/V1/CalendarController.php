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

        // Fetch DB Day and Month Metadata
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
                'month_name' => $month?->display_name ?? 'Unknown',
                'deities' => $month?->deities ?? 'Unknown',
                'meaning' => $month?->meaning ?? '',
                'year' => $today->year + 4241,
                'gregorian_reference' => $month?->gregorian_reference ?? $today->format('F d'),
            ],
            'day_details' => $day,
        ]);
    }
}
