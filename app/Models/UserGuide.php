<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGuide extends Model
{
    protected $fillable = [
        'title',
        'content',
        'type',
        'video_url',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
