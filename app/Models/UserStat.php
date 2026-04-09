<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ShopOrder;

class UserStat extends Model
{
    protected $fillable = [
        'user_id',
        'daily_streak',
        'time_spent_minutes',
        'nima_sedani_time_minutes',
        'amount_spent_kobo',
        'last_active_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Update (or start) the daily streak for this stat record.
     * Call any time the user shows activity for the day.
     */
    public function updateStreak(): void
    {
        $today = Carbon::today();

        if (!$this->last_active_date) {
            $this->last_active_date = $today;
            $this->daily_streak     = 1;
            $this->save();
            return;
        }

        $lastActive = Carbon::parse($this->last_active_date);

        if ($lastActive->isToday()) {
            // Already counted today – nothing to do
            return;
        }

        if ($lastActive->isYesterday()) {
            $this->daily_streak += 1;
        } else {
            // Gap of 2 or more days – reset
            $this->daily_streak = 1;
        }

        $this->last_active_date = $today;
        $this->save();
    }

    /**
     * Add time (in seconds) to the running totals.
     * Pass the page name so Nima Sedani time is tracked separately.
     */
    public function addTime(int $durationSeconds, string $page = ''): void
    {
        if ($durationSeconds < 30) {
            return; // Ignore very short visits
        }

        $minutes = (int) ceil($durationSeconds / 60);
        $this->time_spent_minutes += $minutes;

        if (stripos($page, 'nima_sedani') !== false ||
            stripos($page, 'nima sedani') !== false ||
            stripos($page, 'Nima Sedani Book') !== false) {
            $this->nima_sedani_time_minutes += $minutes;
        }

        $this->save();
    }

    /**
     * Recalculate amount_spent_kobo from the user's paid shop orders.
     * Returns the updated value.
     */
    public function syncAmountSpent(): int
    {
        $total = ShopOrder::where('user_id', $this->user_id)
            ->where('status', 'paid')
            ->sum('amount_kobo');

        $total = (int) $total;

        if ($total !== $this->amount_spent_kobo) {
            $this->amount_spent_kobo = $total;
            $this->save();
        }

        return $total;
    }
}
