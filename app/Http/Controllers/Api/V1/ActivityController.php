<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserActivity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Track user activity
     */
    public function store(Request $request)
    {
        $request->validate([
            'page' => 'required|string|max:255',
            'duration_seconds' => 'required|integer|min:0',
            'visited_at' => 'nullable|date',
        ]);

        $activity = UserActivity::create([
            'user_id' => $request->user()->id,
            'page' => $request->page,
            'duration_seconds' => $request->duration_seconds,
            'visited_at' => $request->visited_at ?? now(),
        ]);

        return response()->json([
            'message' => 'Activity tracked successfully',
            'data' => $activity,
        ], 201);
    }

    /**
     * Get user activity history
     */
    public function index(Request $request)
    {
        $activities = UserActivity::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json($activities);
    }
}
