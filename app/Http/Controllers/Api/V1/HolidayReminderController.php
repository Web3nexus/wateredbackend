<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\HolidayReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HolidayReminderController extends Controller
{
    /**
     * Get all holiday reminders for the authenticated user
     */
    public function index()
    {
        $reminders = HolidayReminder::where('user_id', Auth::id())
            ->with(['holiday', 'calendarDay'])
            ->orderBy('reminder_time', 'asc')
            ->get();

        return response()->json([
            'data' => $reminders
        ]);
    }

    /**
     * Store holiday reminders
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'holiday_id' => 'nullable|exists:holidays,id',
            'calendar_day_id' => 'nullable|exists:calendar_days,id',
            'holiday_name' => 'required|string',
            'reminders' => 'required|array',
            'reminders.*.time' => 'required|date|after:now',
            'reminders.*.type' => 'required|string', // day_of, 24h_before, custom
        ]);

        $savedReminders = [];

        foreach ($validated['reminders'] as $r) {
            // Upsert reminder based on type and holiday/calendar_day
            $reminder = HolidayReminder::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'holiday_id' => $validated['holiday_id'] ?? null,
                    'calendar_day_id' => $validated['calendar_day_id'] ?? null,
                    'reminder_type' => $r['type'],
                ],
                [
                    'holiday_name' => $validated['holiday_name'],
                    'reminder_time' => $r['time'],
                    'status' => 'active',
                ]
            );
            $savedReminders[] = $reminder;
        }

        return response()->json([
            'message' => 'Holiday reminders saved successfully',
            'data' => $savedReminders
        ], 201);
    }

    /**
     * Remove a specific reminder
     */
    public function destroy(HolidayReminder $holidayReminder)
    {
        if ($holidayReminder->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $holidayReminder->delete();

        return response()->json([
            'message' => 'Reminder removed successfully'
        ]);
    }
}
