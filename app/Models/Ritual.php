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
        'tradition_id',
    ];

    public function tradition()
    {
        return $this->belongsTo(Tradition::class);
    }

    protected $casts = [
        'is_sacred_daily' => 'boolean',
        'is_premium' => 'boolean',
        'steps' => 'array',
        'media_urls' => 'array',
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

                // If the value is already an array (due to casting), use it.
                // Otherwise, try to decode it.
                $urls = is_array($value) ? $value : json_decode($value ?? '[]', true);
                if (!is_array($urls))
                    $urls = [];

                return array_map(function ($url) {
                    if (str_starts_with($url, 'http'))
                        return $url;
                    return Storage::url($url);
                }, $urls);
            },
            set: function ($value) {
                if (!$value)
                    return json_encode([]);

                $urls = is_array($value) ? $value : [$value];

                // Strip the base URL if it's already there to avoid double prefixing
                $baseUrl = rtrim(config('app.url'), '/') . '/storage/';
                $cleanedUrls = array_map(function ($url) use ($baseUrl) {
                    return str_replace($baseUrl, '', $url);
                }, $urls);

                return json_encode($cleanedUrls);
            }
        );
    }

    /**
     * Ensure steps is always an array for the Repeater component.
     */
    protected function steps(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value)
                    return [];
                if (is_array($value))
                    return $value;

                $decoded = json_decode($value, true);
                return is_array($decoded) ? $decoded : [];
            },
            set: fn($value) => is_array($value) ? json_encode($value) : $value,
        );
    }
}
