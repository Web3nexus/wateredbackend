<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventReminderController extends Controller
{
    /**
     * Get all reminders for the authenticated user
     */
    public function index()
    {
        $reminders = EventReminder::where('user_id', Auth::id())
            ->with('event')
            ->where('reminder_status', 'active')
            ->orderBy('reminder_time', 'asc')
            ->get();

        return response()->json([
            'data' => $reminders
        ]);
    }

    /**
     * Save a reminder for an event
     */
    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'reminder_time' => 'nullable|date|after:now',
        ]);

        // Check if reminder already exists
        $existing = EventReminder::where('user_id', Auth::id())
            ->where('event_id', $event->id)
            ->first();

        if ($existing) {
            $existing->update([
                'reminder_status' => 'active',
                'reminder_time' => $validated['reminder_time'] ?? $event->start_time->subHours(24),
            ]);
            $reminder = $existing;
        } else {
            $reminder = EventReminder::create([
                'user_id' => Auth::id(),
                'event_id' => $event->id,
                'reminder_status' => 'active',
                'reminder_time' => $validated['reminder_time'] ?? $event->start_time->subHours(24),
            ]);
        }

        return response()->json([
            'message' => 'Reminder saved successfully',
            'data' => $reminder
        ], 201);
    }

    /**
     * Remove a reminder for an event
     */
    public function destroy(Event $event)
    {
        $reminder = EventReminder::where('user_id', Auth::id())
            ->where('event_id', $event->id)
            ->first();

        if (!$reminder) {
            return response()->json([
                'message' => 'Reminder not found'
            ], 404);
        }

        $reminder->update(['reminder_status' => 'cancelled']);

        return response()->json([
            'message' => 'Reminder removed successfully'
        ]);
    }
}
