<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntryTranslation extends Model
{
    protected $fillable = [
        'entry_id',
        'language_code',
        'text',
        'translator_name',
        'notes',
    ];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class);
    }
}
