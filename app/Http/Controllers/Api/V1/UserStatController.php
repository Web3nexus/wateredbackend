<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserStat;
use App\Models\ShopOrder;
use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserStatController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $stat = UserStat::firstOrCreate(
            ['user_id' => $user->id],
            ['daily_streak' => 0, 'time_spent_minutes' => 0, 'amount_spent_kobo' => 0]
        );

        // Update streak logic
        $this->updateStreak($stat);

        // Orders & Bookmarks count
        $ordersCount = ShopOrder::where('user_id', $user->id)->count();
        $savedCount = Bookmark::where('user_id', $user->id)->count();

        // Calc spent amount from Paid shop orders
        $totalSpent = ShopOrder::where('user_id', $user->id)->where('status', 'paid')->sum('amount_kobo');
        if ($totalSpent > $stat->amount_spent_kobo) {
            $stat->update(['amount_spent_kobo' => $totalSpent]);
        }

        // Calculate Leaderboard Rank (1-indexed based on amount_spent and time_spent)
        $rank = UserStat::where('amount_spent_kobo', '>', $stat->amount_spent_kobo)
            ->orWhere(function ($query) use ($stat) {
                $query->where('amount_spent_kobo', '=', $stat->amount_spent_kobo)
                      ->where('time_spent_minutes', '>', $stat->time_spent_minutes);
            })
            ->count() + 1;

        return response()->json([
            'id' => $stat->id,
            'daily_streak' => $stat->daily_streak,
            'time_spent_minutes' => $stat->time_spent_minutes,
            'nima_sedani_time_minutes' => $stat->nima_sedani_time_minutes,
            'amount_spent_kobo' => $stat->amount_spent_kobo,
            'orders_count' => $ordersCount,
            'saved_items_count' => $savedCount,
            'leaderboard_rank' => $rank,
        ]);
    }

    public function leaderboard(Request $request)
    {
        $currentUser = $request->user();

        // Get top 50 users ordered by composite score
        $topStats = UserStat::with('user:id,name,profile_photo_path')
            ->orderByDesc('amount_spent_kobo')
            ->orderByDesc('time_spent_minutes')
            ->orderByDesc('daily_streak')
            ->limit(50)
            ->get();

        $entries = $topStats->map(function ($stat, $index) use ($currentUser) {
            return [
                'rank'             => $index + 1,
                'user_id'         => $stat->user_id,
                'name'            => $stat->user?->name ?? 'Initiate',
                'avatar'          => $stat->user?->profile_photo_path,
                'daily_streak'    => $stat->daily_streak ?? 0,
                'time_spent_minutes' => $stat->time_spent_minutes ?? 0,
                'amount_spent_kobo'  => $stat->amount_spent_kobo ?? 0,
                'is_current_user' => $stat->user_id === $currentUser->id,
            ];
        });

        // Compute current user's global rank (in case they're outside top 50)
        $myStat = UserStat::firstOrCreate(
            ['user_id' => $currentUser->id],
            ['daily_streak' => 0, 'time_spent_minutes' => 0, 'amount_spent_kobo' => 0]
        );
        $myGlobalRank = UserStat::where('amount_spent_kobo', '>', $myStat->amount_spent_kobo)
            ->orWhere(function ($q) use ($myStat) {
                $q->where('amount_spent_kobo', '=', $myStat->amount_spent_kobo)
                  ->where('time_spent_minutes', '>', $myStat->time_spent_minutes);
            })
            ->count() + 1;

        return response()->json([
            'entries'         => $entries,
            'my_global_rank'  => $myGlobalRank,
            'total_users'     => UserStat::count(),
        ]);
    }

    public function trackTime(Request $request)
    {
        $request->validate([
            'duration_minutes' => 'required|integer|min:1|max:1440',
            'category' => 'nullable|string',
        ]);

        $user = $request->user();
        $stat = UserStat::firstOrCreate(
            ['user_id' => $user->id],
            ['daily_streak' => 0, 'time_spent_minutes' => 0, 'amount_spent_kobo' => 0]
        );

        $this->updateStreak($stat);

        $stat->time_spent_minutes += $request->duration_minutes;
        if ($request->category === 'nima_sedani') {
            $stat->nima_sedani_time_minutes += $request->duration_minutes;
        }

        $stat->save();

        return response()->json(['message' => 'Time tracked successfully', 'stat' => $stat]);
    }

    private function updateStreak(UserStat $stat)
    {
        $today = Carbon::today();
        
        if (!$stat->last_active_date) {
            $stat->last_active_date = $today;
            $stat->daily_streak = 1;
            $stat->save();
            return;
        }

        $lastActive = Carbon::parse($stat->last_active_date);

        if ($lastActive->isYesterday()) {
            $stat->daily_streak += 1;
            $stat->last_active_date = $today;
            $stat->save();
        } elseif ($lastActive->isBefore(Carbon::yesterday())) {
            $stat->daily_streak = 1;
            $stat->last_active_date = $today;
            $stat->save();
        }
    }
}
