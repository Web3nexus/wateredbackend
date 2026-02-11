<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'is_active',
    ];
}
