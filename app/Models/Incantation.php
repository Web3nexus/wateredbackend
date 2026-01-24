<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incantation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'content',
        'audio_url',
        'is_paid',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
    ];
}
