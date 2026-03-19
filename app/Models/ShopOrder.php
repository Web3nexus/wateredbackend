<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopOrder extends Model
{
    protected $fillable = [
        'user_id',
        'reference',
        'amount_kobo',
        'currency',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(ShopOrderItem::class);
    }
}
