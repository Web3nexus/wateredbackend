<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookPurchase extends Model
{
    protected $fillable = [
        'user_id',
        'text_collection_id',
        'amount_paid',
        'reference',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function textCollection(): BelongsTo
    {
        return $this->belongsTo(TextCollection::class);
    }
}
