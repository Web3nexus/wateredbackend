<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
