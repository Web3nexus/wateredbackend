<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'is_free',
        'availability',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'availability' => 'array',
        'sort_order' => 'integer',
    ];

    public function consultationTypes()
    {
        return $this->hasMany(ConsultationType::class, 'category', 'slug');
    }
}
