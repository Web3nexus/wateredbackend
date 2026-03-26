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
            'sacred_date' => [
                'month_number'     => $month?->number ?? 0,
                'day_number'       => $day?->day_number ?? 0,
                'month_name'       => $month?->standard_name ?? 'Unknown',
                'custom_month_name'=> $month?->custom_name ?? '',
                'deities'          => $month?->deities ?? 'Unknown',
                'meaning'          => $month?->meaning ?? '',
                'year'             => (string) $today->year,
                'season'           => $month?->season ?? '',
            ],
            'day_details' => $day,
        ]);
    }

    /**
     * Add or update a celebration on a calendar day.
     * POST /calendar/celebrate
     */
    public function storeCelebration(Request $request)
    {
        $request->validate([
            'day_number'       => 'required|integer|between:1,31',
            'month_number'     => 'required|integer|between:1,12',
            'celebration_type' => 'required|string|max:100',
            'celebration_note' => 'nullable|string|max:500',
        ]);

        $month = CalendarMonth::where('number', $request->month_number)->first();

        if (!$month) {
            return response()->json(['message' => 'Calendar month not found.'], 404);
        }

        $day = CalendarDay::firstOrCreate(
            [
                'calendar_month_id' => $month->id,
                'day_number'        => $request->day_number,
            ],
            [
                'gregorian_day' => now()->setMonth($request->month_number)->setDay($request->day_number)->format('M j'),
                'is_sacred'     => false,
            ]
        );

        $day->celebration_type = $request->celebration_type;

        if ($request->filled('celebration_note')) {
            $existing = $day->content ?? '';
            $note = '🎉 ' . $request->celebration_note;
            $day->content = $existing ? ($existing . "\n\n" . $note) : $note;
        }

        $day->save();

        return response()->json([
            'message' => 'Celebration saved successfully.',
            'day'     => $day->fresh(['month']),
        ]);
    }
}
