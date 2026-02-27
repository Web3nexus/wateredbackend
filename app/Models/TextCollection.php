<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TextCollection extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'tradition_id',
        'category_id',
        'order',
        'is_active',
        'is_premium',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_premium' => 'boolean',
    ];

    public function tradition(): BelongsTo
    {
        return $this->belongsTo(Tradition::class)->withDefault();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class, 'collection_id')->orderBy('number');
    }
}
