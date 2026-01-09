<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'duration_minutes',
        'price',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'price' => 'integer',
        'is_active' => 'boolean',
    ];
}
