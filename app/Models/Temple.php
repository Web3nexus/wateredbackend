<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Temple extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'address',
        'latitude',
        'longitude',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? (str_starts_with($value, 'http') ? $value : \Illuminate\Support\Facades\Storage::url($value)) : null,
        );
    }
}
