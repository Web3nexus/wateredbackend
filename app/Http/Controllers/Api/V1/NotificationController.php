<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\InAppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // List notifications for bell icon
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()
            ->latest()
            ->paginate(20);
        return response()->json($notifications);
    }

    // Mark as read
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->update(['read_at' => now()]);
        return response()->json(['message' => 'Notification marked as read.']);
    }

    // Update FCM token
    public function updateToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'device_type' => 'nullable|string|in:ios,android,web',
        ]);

        $request->user()->fcmTokens()->updateOrCreate(
            ['token' => $request->token],
            ['device_type' => $request->device_type]
        );

        return response()->json(['message' => 'FCM token updated.']);
    }

    /**
     * Get user notification settings
     */
    public function getSettings(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'data' => [
                'push_notifications' => $user->push_notifications,
                'ritual_reminders' => $user->ritual_reminders,
                'event_updates' => $user->event_updates,
                'community_activity' => $user->community_activity,
            ]
        ]);
    }

    /**
     * Update user notification settings
     */
    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'push_notifications' => 'sometimes|boolean',
            'ritual_reminders' => 'sometimes|boolean',
            'event_updates' => 'sometimes|boolean',
            'community_activity' => 'sometimes|boolean',
        ]);

        $request->user()->update($data);

        return response()->json([
            'message' => 'Notification settings updated.',
            'data' => $data,
        ]);
    }
}
