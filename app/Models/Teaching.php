<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Teaching extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'content',
        'featured_image',
        'is_published',
        'published_at',
        'is_premium',
    ];

    protected $appends = ['featured_image_url'];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'is_premium' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    protected function featuredImageUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function () {
                if (!$this->featured_image)
                    return null;
                if (str_starts_with($this->featured_image, 'http'))
                    return $this->featured_image;
                return \Illuminate\Support\Facades\Storage::url($this->featured_image);
            }
        );
    }
}
