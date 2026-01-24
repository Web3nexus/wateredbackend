<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ritual extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'content',
        'media_urls',
    ];

    protected $casts = [
        'media_urls' => 'array',
    ];
}
