<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entry extends Model
{
    protected $fillable = [
        'number',
        'verse_number',
        'text',
        'chapter_id',
        'order',
        'metadata',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'json',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(EntryTranslation::class);
    }

    public function translationByLanguage(string $languageCode)
    {
        return $this->translations()->where('language_code', $languageCode)->first();
    }
}
