<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * List upcoming events
     */
    public function index(Request $request)
    {
        $events = Event::where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->paginate(10);

        // Append 'is_registered' status for the current user
        $events->getCollection()->transform(function ($event) use ($request) {
            $event->is_registered = $event->isRegistered($request->user());
            return $event;
        });

        return response()->json($events);
    }

    /**
     * Get event details
     */
    public function show(Request $request, Event $event)
    {
        $event->is_registered = $event->isRegistered($request->user());
        return response()->json($event);
    }

    /**
     * Register for an event
     */
    public function register(Request $request, Event $event)
    {
        $user = $request->user();

        // Check if already registered
        $registration = $event->registrations()
            ->where('user_id', $user->id)
            ->first();

        if ($registration) {
            if ($registration->status === 'cancelled') {
                $registration->update(['status' => 'registered']);
                return response()->json(['message' => 'Registration reactivated.', 'status' => 'registered']);
            }
            return response()->json(['message' => 'Already registered.', 'status' => 'registered']);
        }

        // Create new registration
        $event->registrations()->create([
            'user_id' => $user->id,
            'status' => 'registered',
        ]);

        return response()->json(['message' => 'Successfully registered.', 'status' => 'registered']);
    }

    /**
     * Cancel registration
     */
    public function cancel(Request $request, Event $event)
    {
        $user = $request->user();

        $registration = $event->registrations()
            ->where('user_id', $user->id)
            ->first();

        if ($registration) {
            $registration->delete(); // Or update status to 'cancelled' if we want history
            // For now, hard delete or simple remove is fine, but let's stick to update if we want constraints to persist
            // Actually, let's delete to keep it simple for "toggle" logic if needed, or update status.
            // Requirement says "cancel", let's delete for clean slate or update status.
            // Let's delete to make re-registration easy.
            $registration->delete();
        }

        return response()->json(['message' => 'Registration cancelled.', 'status' => 'cancelled']);
    }
}
