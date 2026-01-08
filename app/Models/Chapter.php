<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chapter extends Model
{
    protected $fillable = [
        'name',
        'number',
        'description',
        'collection_id',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function collection(): BelongsTo
    {
        return $this->belongsTo(TextCollection::class, 'collection_id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class)->orderBy('number');
    }
}
