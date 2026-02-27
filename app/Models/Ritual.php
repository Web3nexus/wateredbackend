<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Ritual extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'content',
        'media_urls',
        'category',
        'steps',
        'time_of_day',
        'symbolic_meaning',
        'is_sacred_daily',
        'is_premium',
    ];

    protected $casts = [
        'media_urls' => 'array',
        'steps' => 'array',
        'is_sacred_daily' => 'boolean',
        'is_premium' => 'boolean',
    ];

    /**
     * Get the media URLs.
     */
    protected function mediaUrls(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value)
                    return [];
                $urls = is_array($value) ? $value : json_decode($value, true);

                return array_map(function ($url) {
                    if (str_starts_with($url, 'http'))
                        return $url;
                    return Storage::url($url);
                }, $urls);
            },
        );
    }
}
