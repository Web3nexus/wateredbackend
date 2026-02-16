<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

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
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected $appends = [
        'full_audio_url',
        'full_thumbnail_url',
    ];

    // protected $with = ['category'];

    public function contentCategory(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ContentCategory::class, 'category_id');
    }

    protected function fullAudioUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $value = $this->getRawOriginal('audio_url');
                \Illuminate\Support\Facades\Log::info('Audio Model - Raw URL: ' . ($value ?? 'NULL') . ' for Audio ID: ' . $this->id);

                if (!$value)
                    return null;

                if (str_starts_with($value, 'http')) {
                    \Illuminate\Support\Facades\Log::info('Audio Model - Returning direct URL: ' . $value);
                    return $value;
                }

                $url = Storage::disk('public')->url($value);
                \Illuminate\Support\Facades\Log::info('Audio Model - Generated Storage URL: ' . $url);
                return $url;
            },
        );
    }

    protected function fullThumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $value = $this->getRawOriginal('thumbnail_url');
                if (!$value)
                    return null;

                if (str_starts_with($value, 'http')) {
                    return $value;
                }

                return Storage::disk('public')->url($value);
            },
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
