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

        $month = CalendarMonth::where('number', $today->month)->first();

        $day = null;
        if ($month) {
            $day = CalendarDay::where('calendar_month_id', $month->id)
                ->where('day_number', $today->day)
                ->first();
        }

        return response()->json([
            'gregorian_date' => $today->toDateString(),
            'kemetic_date' => [
                'month_number' => $month?->number ?? 0,
                'day_number' => $day?->day_number ?? 0,
                'month_name' => $month?->standard_name ?? 'Unknown',
                'custom_month_name' => $month?->custom_name ?? '',
                'deities' => $month?->deities ?? 'Unknown',
                'meaning' => $month?->meaning ?? '',
                'year' => $today->year + 4241, // Traditional Kemetic offset
                'season' => $month?->season ?? '',
            ],
            'day_details' => $day,
        ]);
    }
}
