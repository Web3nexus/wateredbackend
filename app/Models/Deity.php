<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image_url',
        'tradition_id',
        'origin',
        'mythology_story',
        'symbols',
        'domains',
        'sacred_elements',
    ];

    public function tradition(): BelongsTo
    {
        return $this->belongsTo(Tradition::class)->withDefault();
    }
}
