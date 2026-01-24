<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    /**
     * Get user reminders
     */
    public function index(Request $request)
    {
        $reminders = $request->user()->reminders()->latest()->get();
        return response()->json(['data' => $reminders]);
    }

    /**
     * Create or Sync reminder
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'time' => 'required', // HH:MM:SS
            'days' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $reminder = $request->user()->reminders()->create([
            'title' => $request->title,
            'time' => $request->time,
            'days' => $request->days,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json(['data' => $reminder], 201);
    }

    // Future: update/delete endpoints
    public function destroy(Request $request, Reminder $reminder)
    {
        if ($request->user()->id !== $reminder->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $reminder->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function update(Request $request, Reminder $reminder)
    {
        if ($request->user()->id !== $reminder->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reminder->update($request->all());
        return response()->json(['data' => $reminder]);
    }
}
