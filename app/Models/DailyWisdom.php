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
        'publish_date',
        'is_active',
    ];

    protected $casts = [
        'publish_date' => 'date',
        'is_active' => 'boolean',
    ];
}
