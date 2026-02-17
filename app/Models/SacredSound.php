<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SacredSound extends Model
{
    protected $fillable = [
        'title',
        'file_path',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
