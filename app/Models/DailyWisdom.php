<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyWisdom extends Model
{
    use HasFactory;

    protected $table = 'daily_wisdoms';

    protected $fillable = [
        'quote',
        'author',
        'background_image_url',
        'active_date',
        'is_active',
    ];

    protected $casts = [
        'active_date' => 'date',
        'is_active' => 'boolean',
    ];

    protected $appends = ['publish_date'];

    public function getPublishDateAttribute()
    {
        return $this->active_date;
    }
}
