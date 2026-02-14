<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HolidayReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'holiday_id',
        'calendar_day_id',
        'holiday_name',
        'reminder_time',
        'reminder_type',
        'status',
        'notified_at',
    ];

    protected $casts = [
        'reminder_time' => 'datetime',
        'notified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function holiday(): BelongsTo
    {
        return $this->belongsTo(Holiday::class);
    }

    public function calendarDay(): BelongsTo
    {
        return $this->belongsTo(CalendarDay::class, 'calendar_day_id');
    }
}
