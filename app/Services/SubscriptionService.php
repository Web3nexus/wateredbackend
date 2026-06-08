<?php

namespace App\Services;

use App\Models\GlobalSetting;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    /**
     * Activate or extend a user's premium subscription using a shared internal service.
     *
     * Both Paystack and Apple flows must call this after provider-side verification.
     *
     * @param User $user
     * @param string $provider 'paystack' or 'apple'
     * @param string $providerTransactionId Unique transaction ID from provider
     * @param string|null $originalTransactionId Apple original_transaction_id (for renewals)
     * @param string $planId
     * @param \DateTime $expiresAt
     * @param float|null $amount
     * @param string|null $platform 'android', 'ios', 'web'
     * @param array $metadata Additional data to log
     * @return Subscription
     */
    public function activatePremium(
        User $user,
        string $provider,
        string $providerTransactionId,
        ?string $originalTransactionId,
        string $planId,
        \DateTime $expiresAt,
        ?float $amount = null,
        ?string $platform = null,
        array $metadata = [],
        ?string $deviceType = null,
        ?string $osVersion = null,
    ): Subscription {
        return DB::transaction(function () use (
            $user, $provider, $providerTransactionId, $originalTransactionId,
            $planId, $expiresAt, $amount, $platform, $metadata, $deviceType, $osVersion
        ) {
            // 1. Check if already processed (idempotency)
            $existing = $this->findExisting($provider, $providerTransactionId, $originalTransactionId, $user->id);
            if ($existing && $existing->status === 'active') {
                Log::info("SubscriptionService: Duplicate event suppressed", [
                    'provider' => $provider,
                    'transaction_id' => $providerTransactionId,
                    'subscription_id' => $existing->id,
                ]);
                // Still ensure user is premium
                if (!$user->is_premium) {
                    $user->update(['is_premium' => true]);
                }
                return $existing;
            }

            // 2. If record exists but is expired, reactivate
            if ($existing && $existing->status !== 'active') {
                $existing->update([
                    'status' => 'active',
                    'expires_at' => $expiresAt,
                    'starts_at' => now(),
                    'amount' => $amount ?? $existing->amount,
                ]);
                $user->update(['is_premium' => true]);
                Log::info("SubscriptionService: Reactivated expired subscription", [
                    'subscription_id' => $existing->id,
                    'user_id' => $user->id,
                ]);
                return $existing->fresh();
            }

            // 3. Detect existing active subscription for extension
            $activeSub = $user->subscription;
            $startsAt = now();
            $finalExpiresAt = $expiresAt;

            if ($activeSub && $activeSub->expires_at && $activeSub->expires_at->isFuture()) {
                $diffDays = abs($expiresAt->diffInDays($startsAt, true));
                $finalExpiresAt = $activeSub->expires_at->copy()->addDays((int) round($diffDays));
                Log::info("SubscriptionService: Extending existing subscription", [
                    'user_id' => $user->id,
                    'old_expires_at' => $activeSub->expires_at,
                    'new_expires_at' => $finalExpiresAt,
                ]);
            }

            // 4. Create or update the subscription record
            $subscriptionData = [
                'user_id' => $user->id,
                'plan_id' => $planId,
                'provider' => $provider,
                'platform' => $platform ?? $this->resolvePlatform($provider),
                'device_type' => $deviceType,
                'os_version' => $osVersion,
                'provider_subscription_id' => $providerTransactionId,
                'original_transaction_id' => $originalTransactionId,
                'amount' => $amount ?? 0,
                'status' => 'active',
                'starts_at' => $startsAt,
                'expires_at' => $finalExpiresAt,
            ];

            if ($existing) {
                $existing->update($subscriptionData);
                $subscription = $existing->fresh();
            } else {
                $subscription = Subscription::create($subscriptionData);
            }

            // 5. Update user premium flag
            $user->update(['is_premium' => true]);

            Log::info("SubscriptionService: Premium activated", [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'provider' => $provider,
                'transaction_id' => $providerTransactionId,
                'expires_at' => $finalExpiresAt->toIso8601String(),
            ]);

            return $subscription;
        });
    }

    /**
     * Deactivate a user's premium access (for cancellations, expirations, refunds).
     */
    public function deactivatePremium(User $user, string $providerTransactionId): void
    {
        DB::transaction(function () use ($user, $providerTransactionId) {
            $subscription = Subscription::where('provider_subscription_id', $providerTransactionId)
                ->orWhere('original_transaction_id', $providerTransactionId)
                ->first();

            if ($subscription && $subscription->status === 'active') {
                $subscription->update(['status' => 'expired']);
                Log::info("SubscriptionService: Subscription deactivated", [
                    'subscription_id' => $subscription->id,
                    'user_id' => $user->id,
                ]);
            }

            // Only downgrade user if no other active subscriptions exist
            if (!$user->subscriptions()->where('status', 'active')->exists()) {
                $user->update(['is_premium' => false]);
                Log::info("SubscriptionService: User downgraded from premium", [
                    'user_id' => $user->id,
                ]);
            }
        });
    }

    /**
     * Verify Paystack transaction amount matches expected plan price.
     */
    public function verifyPaystackAmount(string $planId, float $amountPaidKobo, string $currency): bool
    {
        $settings = GlobalSetting::first();
        if (!$settings) {
            return false;
        }

        $isYearly = str_contains($planId, 'yearly');
        $expectedAmount = $isYearly
            ? ($settings->premium_yearly_amount ?? 0) * 100
            : ($settings->premium_monthly_amount ?? 0) * 100;

        $expectedCurrency = $settings->system_currency ?? 'NGN';

        if ($currency !== $expectedCurrency) {
            Log::warning("Paystack currency mismatch", [
                'expected' => $expectedCurrency,
                'got' => $currency,
            ]);
            return false;
        }

        if ((int)$amountPaidKobo < (int)$expectedAmount) {
            Log::warning("Paystack amount mismatch", [
                'expected_kobo' => $expectedAmount,
                'got_kobo' => $amountPaidKobo,
            ]);
            return false;
        }

        return true;
    }

    /**
     * Verify Apple product ID maps to a valid premium plan.
     */
    public function resolveApplePlanId(string $productId): ?string
    {
        $settings = GlobalSetting::first();
        if (!$settings) {
            return null;
        }

        if ($productId === $settings->premium_monthly_id) {
            return 'apple_monthly';
        }
        if ($productId === $settings->premium_yearly_id) {
            return 'apple_yearly';
        }
        if ($productId === $settings->premium_monthly_id_usd ?? null) {
            return 'apple_monthly';
        }
        if ($productId === $settings->premium_yearly_id_usd ?? null) {
            return 'apple_yearly';
        }

        Log::warning("Unknown Apple product ID, rejecting", [
            'product_id' => $productId,
        ]);
        return null;
    }

    /**
     * Find existing subscription by provider transaction IDs.
     */
    protected function findExisting(string $provider, string $transactionId, ?string $originalTransactionId, ?int $userId = null): ?Subscription
    {
        $query = Subscription::where('provider_subscription_id', $transactionId);

        if ($originalTransactionId) {
            $query->orWhere('original_transaction_id', $originalTransactionId);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->first();
    }

    /**
     * Resolve platform from provider name.
     */
    protected function resolvePlatform(string $provider): string
    {
        return match ($provider) {
            'apple' => 'ios',
            'paystack' => 'android',
            default => 'web',
        };
    }
}
