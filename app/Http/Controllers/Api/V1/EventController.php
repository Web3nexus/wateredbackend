<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::where('start_time', '>=', now())
            ->when($request->category, function ($query, $category) {
                return $query->where('category', $category);
            })
            ->when($request->recurrence, function ($query, $recurrence) {
                return $query->where('recurrence', $recurrence);
            })
            ->when($request->tradition_id, function ($query, $traditionId) {
                return $query->where('tradition_id', $traditionId);
            })
            ->orderBy('start_time', 'asc')
            ->get();

        return response()->json([
            'data' => $events
        ]);
    }

    public function show(Event $event)
    {
        return response()->json([
            'data' => $event
        ]);
    }
}
