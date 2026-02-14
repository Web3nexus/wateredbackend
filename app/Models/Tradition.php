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

    protected function deityImageUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn(?string $value) => $value
            ? (str_starts_with($value, 'http') ? $value : \Illuminate\Support\Facades\Storage::url($value))
            : null,
        );
    }

    protected function backgroundImage(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn() => $this->deity_image_url,
        );
    }

    /**
     * Overriding toArray to provide background_image for Flutter
     */
    public function toArray()
    {
        $array = parent::toArray();
        $array['background_image'] = $this->background_image;
        return $array;
    }
}
