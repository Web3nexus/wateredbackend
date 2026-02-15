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
        'slug',
        'description',
        'start_time',
        'end_time',
        'event_date',
        'event_time',
        'location',
        'banner_image',
        'image_url',
        'is_paid',
        'price',
        'recurrence',
        'cultural_origin',
        'tradition_id',
        'category',
    ];

    protected $appends = ['banner_image_url'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'event_date' => 'date',
        'is_paid' => 'boolean',
        'price' => 'decimal:2',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = \Illuminate\Support\Str::slug($event->title) . '-' . uniqid();
            }
        });
    }

    public function getBannerImageUrlAttribute(): ?string
    {
        $path = $this->banner_image ?? $this->image_url;

        if (!$path)
            return null;

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return asset(\Illuminate\Support\Facades\Storage::url($path));
    }

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
