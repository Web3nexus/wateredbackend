<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Hotfix for production: fallback to 'bookings' if migration hasn't run
        if (!app()->runningInConsole()) {
            try {
                if (!$this->getConnection()->getSchemaBuilder()->hasTable('appointments') && $this->getConnection()->getSchemaBuilder()->hasTable('bookings')) {
                    $this->setTable('bookings');
                }
            } catch (\Exception $e) {
                // Fail silently and use default
            }
        }
    }

    protected $fillable = [
        'appointment_code',
        'user_id',
        'full_name',
        'email',
        'phone',
        'consultation_type_id',
        'service_type',
        'start_time',
        'appointment_status',
        'notes',
        'amount',
        'payment_status',
        'payment_reference',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function consultationType(): BelongsTo
    {
        return $this->belongsTo(ConsultationType::class);
    }
}
