<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarDay extends Model
{
    protected $fillable = [
        'calendar_month_id',
        'day_number',
        'custom_day_name',
        'gregorian_day',
        'content',
        'associated_deities',
        'celebration_type',
        'is_sacred',
        'ui_color',
        'activities',
        'restrictions',
    ];

    protected $casts = [
        'associated_deities' => 'array',
        'activities' => 'array',
        'restrictions' => 'array',
        'is_sacred' => 'boolean',
    ];

    public function month(): BelongsTo
    {
        return $this->belongsTo(CalendarMonth::class, 'calendar_month_id');
    }
}
