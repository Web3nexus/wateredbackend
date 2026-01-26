<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CalendarDay;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Get all calendar days
     */
    public function index()
    {
        $days = CalendarDay::orderBy('month')->orderBy('day')->get();

        return response()->json([
            'data' => $days,
            'month_names' => CalendarDay::getMonthNames(),
        ]);
    }

    /**
     * Get calendar days for a specific month
     */
    public function getMonth(int $month)
    {
        $days = CalendarDay::byMonth($month)->orderBy('day')->get();

        return response()->json([
            'data' => $days,
            'month_name' => CalendarDay::getMonthNames()[$month] ?? null,
        ]);
    }

    /**
     * Get special/sacred days
     */
    public function getSpecialDays()
    {
        $days = CalendarDay::specialDays()->orderBy('month')->orderBy('day')->get();

        return response()->json([
            'data' => $days,
        ]);
    }

    /**
     * Get today's calendar day (based on Kemetic calendar calculation)
     */
    public function getToday()
    {
        // This would need proper Kemetic calendar conversion logic
        // For now, returning a placeholder
        $today = CalendarDay::first();

        return response()->json([
            'data' => $today,
            'message' => 'Kemetic date calculation to be implemented',
        ]);
    }
}
