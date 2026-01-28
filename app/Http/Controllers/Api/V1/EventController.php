<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::where('start_time', '>=', now())
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
