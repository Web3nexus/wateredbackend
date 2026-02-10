<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Audio extends Model
{
    use HasFactory;

    protected $table = 'audios';

    protected $fillable = [
        'title',
        'description',
        'audio_url',
        'thumbnail_url',
        'author',
        'duration',
        'published_at',
        'tradition_id',
        'category_id',
        'is_active',
        'is_featured',
        // 'category',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected $with = ['category'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ContentCategory::class);
    }

    protected function audioUrl(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? (str_starts_with($value, 'http') ? $value : asset('storage/' . $value)) : null,
        );
    }

    protected function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? (str_starts_with($value, 'http') ? $value : asset('storage/' . $value)) : null,
        );
    }

    public function tradition(): BelongsTo
    {
        return $this->belongsTo(Tradition::class)->withDefault();
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function isLikedBy(?User $user): bool
    {
        if (!$user)
            return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Scope a query to search by title, description or author.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%")
                ->orWhere('author', 'LIKE', "%{$search}%");
        });
    }
}
