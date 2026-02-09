<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'image_position',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
