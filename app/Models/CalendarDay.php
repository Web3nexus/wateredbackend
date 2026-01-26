<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarDay extends Model
{
    protected $fillable = [
        'month',
        'day',
        'month_name',
        'day_name',
        'description',
        'associated_deities',
        'celebration_type',
        'is_special_day',
        'color',
    ];

    protected $casts = [
        'associated_deities' => 'array',
        'is_special_day' => 'boolean',
    ];

    /**
     * Get the Kemetic month names
     */
    public static function getMonthNames(): array
    {
        return [
            1 => 'Thoth',
            2 => 'Phaophi',
            3 => 'Athyr',
            4 => 'Choiak',
            5 => 'Tybi',
            6 => 'Mechir',
            7 => 'Phamenoth',
            8 => 'Pharmuthi',
            9 => 'Pachons',
            10 => 'Payni',
            11 => 'Epiphi',
            12 => 'Mesore',
            13 => 'Epagomenal Days',
        ];
    }

    /**
     * Scope to get special days
     */
    public function scopeSpecialDays($query)
    {
        return $query->where('is_special_day', true);
    }

    /**
     * Scope to get days by month
     */
    public function scopeByMonth($query, int $month)
    {
        return $query->where('month', $month);
    }
}
