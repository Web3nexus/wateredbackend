<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserStat;
use App\Models\ShopOrder;
use App\Models\OrderApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserStatController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $stat = UserStat::firstOrCreate(
            ['user_id' => $user->id],
            ['daily_streak' => 0, 'time_spent_minutes' => 0, 'nima_sedani_time_minutes' => 0, 'amount_spent_kobo' => 0]
        );

        // Update streak on dashboard visit (covers users who don't use the app heavily)
        $stat->updateStreak();

        // Real-time sync of amount spent from paid shop orders
        $stat->syncAmountSpent();

        // Orders placed = ritual / spiritual order applications submitted by this user
        $ordersCount = OrderApplication::where('user_id', $user->id)->count();

        // Saved items = bookmarks
        $savedCount = \App\Models\Bookmark::where('user_id', $user->id)->count();

        // Rank = count of users who strictly outrank this user + 1
        $rank = $this->computeRank($stat);

        return response()->json([
            'id'                        => $stat->id,
            'daily_streak'              => $stat->daily_streak,
            'time_spent_minutes'        => $stat->time_spent_minutes,
            'nima_sedani_time_minutes'  => $stat->nima_sedani_time_minutes,
            'amount_spent_kobo'         => $stat->amount_spent_kobo,
            'orders_count'              => $ordersCount,
            'saved_items_count'         => $savedCount,
            'leaderboard_rank'          => $rank,
        ]);
    }

    public function leaderboard(Request $request)
    {
        $currentUser = $request->user();

        // Ensure current user always has a stat record (so they appear)
        $myStat = UserStat::firstOrCreate(
            ['user_id' => $currentUser->id],
            ['daily_streak' => 0, 'time_spent_minutes' => 0, 'nima_sedani_time_minutes' => 0, 'amount_spent_kobo' => 0]
        );

        // Rank users: amount_spent DESC → time_spent DESC → daily_streak DESC → user_id ASC
        $topStats = UserStat::with('user:id,name,profile_photo_path')
            ->orderByDesc('amount_spent_kobo')
            ->orderByDesc('time_spent_minutes')
            ->orderByDesc('daily_streak')
            ->orderBy('user_id')
            ->limit(50)
            ->get();

        $entries = $topStats->map(function ($stat, $index) use ($currentUser) {
            return [
                'rank'               => $index + 1,
                'user_id'            => $stat->user_id,
                'name'               => $stat->user?->name ?? 'Initiate',
                'avatar'             => $stat->user?->profile_photo_path,
                'daily_streak'       => (int) ($stat->daily_streak ?? 0),
                'time_spent_minutes' => (int) ($stat->time_spent_minutes ?? 0),
                'amount_spent_kobo'  => (int) ($stat->amount_spent_kobo ?? 0),
                'is_current_user'    => $stat->user_id === $currentUser->id,
            ];
        });

        $myGlobalRank = $this->computeRank($myStat);

        return response()->json([
            'entries'        => $entries,
            'my_global_rank' => $myGlobalRank,
            'total_users'    => UserStat::count(),
        ]);
    }

    public function trackTime(Request $request)
    {
        $request->validate([
            'duration_minutes' => 'required|integer|min:1|max:1440',
            'category'         => 'nullable|string',
        ]);

        $user = $request->user();
        $stat = UserStat::firstOrCreate(
            ['user_id' => $user->id],
            ['daily_streak' => 0, 'time_spent_minutes' => 0, 'nima_sedani_time_minutes' => 0, 'amount_spent_kobo' => 0]
        );

        $stat->updateStreak();

        $stat->time_spent_minutes += $request->duration_minutes;
        if ($request->category === 'nima_sedani') {
            $stat->nima_sedani_time_minutes += $request->duration_minutes;
        }
        $stat->save();

        return response()->json(['message' => 'Time tracked successfully', 'stat' => $stat]);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Compute the current user's rank using the 4-level tie-breaker hierarchy.
     * Rank = number of users who strictly beat this stat + 1.
     */
    private function computeRank(UserStat $stat): int
    {
        return UserStat::where(function ($q) use ($stat) {
            $q->where('amount_spent_kobo', '>', $stat->amount_spent_kobo)
              ->orWhere(function ($q2) use ($stat) {
                  $q2->where('amount_spent_kobo', '=', $stat->amount_spent_kobo)
                     ->where('time_spent_minutes', '>', $stat->time_spent_minutes);
              })
              ->orWhere(function ($q2) use ($stat) {
                  $q2->where('amount_spent_kobo', '=', $stat->amount_spent_kobo)
                     ->where('time_spent_minutes', '=', $stat->time_spent_minutes)
                     ->where('daily_streak', '>', $stat->daily_streak);
              })
              ->orWhere(function ($q2) use ($stat) {
                  $q2->where('amount_spent_kobo', '=', $stat->amount_spent_kobo)
                     ->where('time_spent_minutes', '=', $stat->time_spent_minutes)
                     ->where('daily_streak', '=', $stat->daily_streak)
                     ->where('user_id', '<', $stat->user_id);
              });
        })->count() + 1;
    }
}
