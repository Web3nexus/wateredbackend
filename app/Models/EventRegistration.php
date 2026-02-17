<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'event_id',
        'user_id',
        'full_name',
        'email',
        'phone',
        'status',
        'payment_reference',
        'amount',
        'payment_status',
        'payment_method',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($registration) {
            $hasBookingCode = $registration->getConnection()->getSchemaBuilder()->hasColumn($registration->getTable(), 'booking_code');
            if ($hasBookingCode && empty($registration->booking_code)) {
                $registration->booking_code = static::generateBookingCode('EVT');
            }
        });
    }

    public static function generateBookingCode($prefix = 'EVT')
    {
        $year = date('y');
        $month = date('m');

        // Check if column exists to avoid SQL errors on legacy tables
        $hasColumn = (new static)->getConnection()->getSchemaBuilder()->hasColumn((new static)->getTable(), 'booking_code');
        if (!$hasColumn) {
            return null;
        }

        // Get the last booking code for this month
        $lastBooking = static::where('booking_code', 'LIKE', "{$prefix}-{$year}-{$month}-%")
            ->orderBy('booking_code', 'desc')
            ->first();

        if ($lastBooking) {
            // Extract index and increment
            $lastIndex = (int) substr($lastBooking->booking_code, -3);
            $newIndex = $lastIndex + 1;
        } else {
            $newIndex = 1;
        }

        return sprintf('%s-%s-%s-%03d', $prefix, $year, $month, $newIndex);
    }
}
