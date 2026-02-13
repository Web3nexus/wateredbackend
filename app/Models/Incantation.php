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
        'is_paid',
        'category',
        'spoken_text',
        'intended_outcome',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
    ];

    /**
     * Get the audio URL.
     */
    protected function audioUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value)
                    return null;
                if (str_starts_with($value, 'http'))
                    return $value;

                return Storage::disk('public')->url($value);
            },
        );
    }
}
