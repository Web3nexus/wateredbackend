<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopOrderItem extends Model
{
    protected $fillable = [
        'shop_order_id',
        'product_id',
        'quantity',
        'unit_price_kobo',
    ];

    public function shopOrder()
    {
        return $this->belongsTo(ShopOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
