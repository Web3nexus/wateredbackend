<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Incantation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'content',
        'audio_url',
        'category',
        'spoken_text',
        'intended_outcome',
    ];

    protected $casts = [
    ];

    protected $appends = [
        'full_audio_url',
        'is_premium',
    ];

    /**
     * Incantations are premium by default.
     */
    protected function isPremium(): Attribute
    {
        return Attribute::make(
            get: fn() => true,
        );
    }

    /**
     * Get the audio URL.
     */
    protected function fullAudioUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $value = $this->getRawOriginal('audio_url');
                if (!$value)
                    return null;

                if (str_starts_with($value, 'http')) {
                    return $value;
                }

                return Storage::url($value);
            },
        );
    }

    public function isBookmarkedBy(?User $user): bool
    {
        if (!$user)
            return false;
        return $user->bookmarks()
            ->where('bookmarkable_id', $this->id)
            ->where('bookmarkable_type', get_class($this))
            ->exists();
    }
}
