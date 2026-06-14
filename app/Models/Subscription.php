<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'provider',
        'platform',
        'device_type',
        'os_version',
        'provider_subscription_id',
        'original_transaction_id',
        'paystack_subscription_code',
        'paystack_email_token',
        'google_purchase_token',
        'google_order_id',
        'google_product_id',
        'amount',
        'failure_reason',
        'auto_renews',
        'status',
        'starts_at',
        'expires_at',
        'raw_provider_event',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'auto_renews' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
