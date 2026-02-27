<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Order extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'description',
        'status',
        'cta_text',
        'cta_link',
        'action_type',
        'image_url',
        'order_level',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order_level' => 'integer',
    ];

    protected function imageUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn(?string $value) => $value ? (str_starts_with($value, 'http') ? $value : \Illuminate\Support\Facades\Storage::url($value)) : null,
        );
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('order_images')
            ->singleFile();
    }

    public function formFields()
    {
        return $this->hasMany(OrderFormField::class)->orderBy('sort_order');
    }

    public function applications()
    {
        return $this->hasMany(OrderApplication::class);
    }
}
