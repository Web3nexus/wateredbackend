<?php

namespace App\Models;

use App\Models\BookPurchase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TextCollection extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'cover_image',
        'content',
        'tradition_id',
        'category_id',
        'order',
        'is_active',
        'is_premium',
        'price',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_premium' => 'boolean',
        'price' => 'decimal:2',
    ];

    protected $appends = ['cover_image_url', 'is_purchased'];

    public function isPurchased(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function () {
                $user = auth('sanctum')->user();
                if (!$user) return false;
                
                return BookPurchase::where('user_id', $user->id)
                    ->where('text_collection_id', $this->id)
                    ->where('status', 'completed')
                    ->exists();
            }
        );
    }

    public function coverImageUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function () {
                if (!$this->cover_image)
                    return null;
                if (str_starts_with($this->cover_image, 'http'))
                    return $this->cover_image;
                return \Illuminate\Support\Facades\Storage::url($this->cover_image);
            }
        );
    }

    public function tradition(): BelongsTo
    {
        return $this->belongsTo(Tradition::class)->withDefault();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class, 'collection_id')->orderBy('number');
    }
}
