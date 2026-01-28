<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'price_ngn',
        'price_usd',
        'image_url',
        'audio_sample_url',
        'is_digital',
        'is_active',
    ];

    protected $casts = [
        'price' => 'integer', // Stored in cents
        'price_ngn' => 'decimal:2',
        'price_usd' => 'decimal:2',
        'is_digital' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? (str_starts_with($value, 'http') ? $value : asset('storage/' . $value)) : null,
        );
    }
}
