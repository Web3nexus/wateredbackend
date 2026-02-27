<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'time',
        'days',
        'is_active',
        'sound_path',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'days' => 'array',
    ];

    protected $appends = ['sound_url'];

    /**
     * Get the full URL for the sound file.
     */
    protected function soundUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function () {
                if (!$this->sound_path) {
                    return null;
                }

                if (str_starts_with($this->sound_path, 'http')) {
                    return $this->sound_path;
                }

                return \Illuminate\Support\Facades\Storage::url($this->sound_path);
            },
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
