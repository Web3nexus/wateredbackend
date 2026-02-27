<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;

use App\Notifications\AppVerifyEmail;
use App\Notifications\AppResetPassword;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public function sendEmailVerificationNotification()
    {
        try {
            $firebaseService = new \App\Services\FirebaseService();
            $verificationUrl = $firebaseService->generateEmailVerificationLink($this->email);

            if (!$verificationUrl) {
                // Fallback to Laravel signed route if Firebase fails
                $verificationUrl = URL::temporarySignedRoute(
                    'verification.verify',
                    now()->addMinutes(config('auth.verification.expire', 60)),
                    [
                        'id' => $this->getKey(),
                        'hash' => sha1($this->getEmailForVerification()),
                    ]
                );
            }

            $this->notify(new \App\Notifications\AppVerifyEmail($verificationUrl));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send verification email: " . $e->getMessage());
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AppResetPassword($token));
    }


    protected $appends = [
        'is_verified',
        'verified',
        'email_verified',
    ];

    public function getIsVerifiedAttribute()
    {
        return $this->hasVerifiedEmail();
    }

    public function getVerifiedAttribute()
    {
        return $this->hasVerifiedEmail();
    }

    public function getEmailVerifiedAttribute()
    {
        return $this->hasVerifiedEmail();
    }

    /**
     * Overriding toArray to provide both snake_case and CamelCase for Flutter
     * without creating accessor conflicts.
     */
    public function toArray()
    {
        $array = parent::toArray();
        // Provide CamelCase aliases for Flutter models that haven't been rebuilt
        $array['emailVerifiedAt'] = $this->email_verified_at ? $this->email_verified_at->toIso8601String() : null;
        $array['createdAt'] = $this->created_at ? $this->created_at->toIso8601String() : null;
        $array['updatedAt'] = $this->updated_at ? $this->updated_at->toIso8601String() : null;

        // Also ensure snake_case for the rebuilt models
        $array['email_verified_at'] = $this->email_verified_at ? $this->email_verified_at->toIso8601String() : null;
        $array['created_at'] = $this->created_at ? $this->created_at->toIso8601String() : null;
        $array['updated_at'] = $this->updated_at ? $this->updated_at->toIso8601String() : null;

        return $array;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'provider',
        'provider_id',
        'profile_image',
        'is_premium',
        'push_notifications',
        'ritual_reminders',
        'event_updates',
        'community_activity',
        'email_verified_at',
    ];

    protected function profileImage(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? (str_starts_with($value, 'http') ? $value : \Illuminate\Support\Facades\Storage::url($value)) : null,
        );
    }

    public function posts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PostLike::class);
    }

    public function appointments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function fcmTokens()
    {
        return $this->hasMany(FcmToken::class);
    }

    public function notifications()
    {
        return $this->hasMany(InAppNotification::class);
    }

    public function subscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    // Helper to get active subscription
    public function getSubscriptionAttribute()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }

    /**
     * Check if the user has an active premium subscription.
     * 
     * Priority:
     *  1. Active subscription record → user is premium, sync is_premium = true
     *  2. No subscription but is_premium is manually set to true → treat as admin override (keep it)
     *  3. No subscription AND is_premium is false → not premium
     * 
     * This prevents admin-granted manual premium from being auto-revoked.
     */
    public function hasActivePremium(): bool
    {
        $activeSub = $this->subscription;

        if ($activeSub !== null) {
            // Active subscription found — ensure flag is synced
            if (!$this->is_premium) {
                $this->is_premium = true;
                $this->save();
            }
            return true;
        }

        // No active subscription. Check if premium was manually granted by admin.
        // We only auto-revoke if a subscription existed before AND has now expired.
        $hadExpiredSub = $this->subscriptions()
            ->where('status', '!=', 'active')
            ->orWhere('expires_at', '<=', now())
            ->exists();

        if ($hadExpiredSub && $this->is_premium) {
            // Subscription expired — revoke the flag
            $this->is_premium = false;
            $this->save();
            return false;
        }

        // No subscription history — is_premium flag is the source of truth (admin grant)
        return (bool) $this->is_premium;
    }

    public function reminders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    public function userActivities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserActivity::class);
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_premium' => 'boolean',
            'push_notifications' => 'boolean',
            'ritual_reminders' => 'boolean',
            'event_updates' => 'boolean',
            'community_activity' => 'boolean',
        ];
    }
}
