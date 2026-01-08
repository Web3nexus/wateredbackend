<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Verse extends Model
{
    use HasTranslations;

    protected $fillable = [
        'chapter_id',
        'number',
        'content',
    ];

    public array $translatable = ['content'];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }
}
