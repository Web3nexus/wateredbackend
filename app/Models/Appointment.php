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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($appointment) {
            $hasAppointmentCode = $appointment->getConnection()->getSchemaBuilder()->hasColumn($appointment->getTable(), 'appointment_code');
            if ($hasAppointmentCode && empty($appointment->appointment_code)) {
                $appointment->appointment_code = static::generateBookingCode('CON');
            }
        });
    }

    public static function generateBookingCode($prefix = 'CON')
    {
        $year = date('y');
        $month = date('m');

        // Check if column exists to avoid SQL errors on legacy tables
        $hasColumn = (new static)->getConnection()->getSchemaBuilder()->hasColumn((new static)->getTable(), 'appointment_code');
        if (!$hasColumn) {
            return null;
        }

        // Get the last booking code for this month
        $lastBooking = static::where('appointment_code', 'LIKE', "{$prefix}-{$year}-{$month}-%")
            ->orderBy('appointment_code', 'desc')
            ->first();

        if ($lastBooking) {
            // Extract index and increment
            $lastIndex = (int) substr($lastBooking->appointment_code, -3);
            $newIndex = $lastIndex + 1;
        } else {
            $newIndex = 1;
        }

        return sprintf('%s-%s-%s-%03d', $prefix, $year, $month, $newIndex);
    }
}
