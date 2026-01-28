<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Tradition extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'language_id',
        'is_active',
        'tradition_type',
        'african_origin',
        'deity_image_url',
    ];

    public array $translatable = ['name', 'description'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function textCollections(): HasMany
    {
        return $this->hasMany(TextCollection::class)->orderBy('order');
    }

    // Legacy support
    public function books(): HasMany
    {
        return $this->textCollections();
    }
}
