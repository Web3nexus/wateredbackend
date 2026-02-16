<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CalendarMonth extends Model
{
    protected $fillable = [
        'number',
        'standard_name',
        'custom_name',
        'year',
        'season',
        'gregorian_reference',
        'description',
        'deities',
        'meaning',
    ];

    public function days(): HasMany
    {
        return $this->hasMany(CalendarDay::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->custom_name ?: $this->standard_name;
    }
}
