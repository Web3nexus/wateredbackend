<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserActivity;
use App\Models\UserStat;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Track user activity AND update the live UserStat record.
     *
     * This is the primary activity-tracking endpoint called by the Flutter app.
     * It replaces the need for the separate track-time endpoint for most cases.
     */
    public function store(Request $request)
    {
        $request->validate([
            'page'             => 'required|string|max:255',
            'duration_seconds' => 'required|integer|min:0',
            'visited_at'       => 'nullable|date',
        ]);

        $user = $request->user();

        // 1. Save the raw activity log
        $activity = UserActivity::create([
            'user_id'          => $user->id,
            'page'             => $request->page,
            'duration_seconds' => $request->duration_seconds,
            'visited_at'       => $request->visited_at ?? now(),
        ]);

        // 2. Update (or create) the live UserStat record so the Stats Dashboard
        //    and Leaderboard reflect real-time activity without needing a
        //    separate API call.
        try {
            $stat = UserStat::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'daily_streak'              => 0,
                    'time_spent_minutes'        => 0,
                    'nima_sedani_time_minutes'  => 0,
                    'amount_spent_kobo'         => 0,
                ]
            );

            // Streak: registers today as an active day
            $stat->updateStreak();

            // Time: accumulates minutes for this page visit
            $stat->addTime($request->duration_seconds, $request->page);

        } catch (\Throwable $e) {
            // Non-fatal — never break the activity track call
            \Log::warning('[ActivityController] UserStat sync failed: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Activity tracked successfully',
            'data'    => $activity,
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
