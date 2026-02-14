<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_time',
        'end_time',
        'location',
        'image_url',
        'is_paid',
        'price',
        'recurrence',
        'cultural_origin',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_paid' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(EventReminder::class);
    }

    public function hasReminder(?User $user): bool
    {
        if (!$user)
            return false;

        return $this->reminders()
            ->where('user_id', $user->id)
            ->where('reminder_status', 'active')
            ->exists();
    }

    public function isRegistered(?User $user): bool
    {
        if (!$user)
            return false;

        return $this->registrations()
            ->where('user_id', $user->id)
            ->where('status', 'registered')
            ->exists();
    }
}
