<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Book extends Model
{
    use HasTranslations;

    protected $fillable = [
        'tradition_id',
        'name',
        'slug',
        'order',
    ];

    public array $translatable = ['name'];

    public function tradition(): BelongsTo
    {
        return $this->belongsTo(Tradition::class);
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('number');
    }
}
