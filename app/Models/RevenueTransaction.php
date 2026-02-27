<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevenueTransaction extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $table = 'revenue_transactions';

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];
}
