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

        // Find the CalendarDay record where gregorian_day matches "MMM d"
        $gregorianRef = $today->format('M j'); // e.g., "Feb 16" (Note: seeder uses 'M d', let's check format)

        $day = CalendarDay::with('month')
            ->where('gregorian_day', $today->format('M d'))
            ->first();

        if (!$day) {
            // Fallback or retry with different format if necessary
            $day = CalendarDay::with('month')
                ->where('gregorian_day', $today->format('M j'))
                ->first();
        }

        return response()->json([
            'gregorian_date' => $today->toDateString(),
            'kemetic_date' => [
                'month_number' => $day?->month?->number ?? 0,
                'day_number' => $day?->day_number ?? 0,
                'month_name' => $day?->month?->standard_name ?? 'Unknown',
                'deities' => $day?->month?->deities ?? 'Unknown',
                'meaning' => $day?->month?->meaning ?? '',
                'year' => $today->year + 4241, // Traditional Kemetic offset
                'season' => $day?->month?->season ?? '',
            ],
            'day_details' => $day,
        ]);
    }
}
