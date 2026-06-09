<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Get current subscription status
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $isPremium = $user->hasActivePremium();
        $subscription = $user->subscription;

        return response()->json([
            'is_premium' => $isPremium,
            'subscription_status' => $subscription?->status ?? 'none',
            'subscription_provider' => $subscription?->provider,
            'subscription_plan' => $subscription?->plan_id,
            'subscription_expires_at' => $subscription?->expires_at?->toIso8601String(),
            'subscription' => $subscription,
        ]);
    }

    /**
     * Initialize a Paystack payment - creates a pending record before the mobile opens the popup.
     * This ensures the webhook can always find the transaction by reference.
     */
    public function initializePaystack(Request $request)
    {
        $request->validate([
            'is_yearly' => 'required|boolean',
        ]);

        $user = $request->user();
        $isYearly = $request->boolean('is_yearly');
        $settings = GlobalSetting::first();

        if (!$settings || !$settings->paystack_secret_key) {
            return response()->json(['message' => 'Payment gateway not configured'], 500);
        }

        $secretKey = $settings->paystack_secret_key;
        $planId = $isYearly ? 'paystack_yearly' : 'paystack_monthly';
        $amount = $isYearly
            ? ($settings->premium_yearly_amount ?? 50000) * 100
            : ($settings->premium_monthly_amount ?? 5000) * 100;
        $currency = $settings->system_currency ?? 'NGN';
        $planCode = $isYearly
            ? $settings->paystack_yearly_plan_code
            : $settings->paystack_monthly_plan_code;

        // 1. Initialize transaction on Paystack server-side
        $paystackResponse = Http::withToken($secretKey)
            ->post('https://api.paystack.co/transaction/initialize', [
                'email' => $user->email,
                'amount' => (int) $amount,
                'currency' => $currency,
                'plan' => $planCode,
                'metadata' => [
                    'user_id' => (string) $user->id,
                    'plan_id' => $planId,
                    'is_yearly' => $isYearly,
                    'platform' => $this->resolvePlatform($request),
                    'type' => 'subscription',
                ],
                'callback_url' => url('/payment/callback'),
            ]);

        if (!$paystackResponse->successful()) {
            Log::error('Paystack initialize failed', [
                'response' => $paystackResponse->body(),
                'user_id' => $user->id,
            ]);
            return response()->json([
                'message' => 'Failed to initialize payment',
                'error' => $paystackResponse->json('message'),
            ], 422);
        }

        $initData = $paystackResponse->json('data');
        $reference = $initData['reference'];
        $authorizationUrl = $initData['authorization_url'];

        // 2. Cancel any stale pending records for this user+plan, then create new one
        Subscription::where('user_id', $user->id)
            ->where('plan_id', $planId)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        $duration = $isYearly ? now()->addYear() : now()->addMonth();
        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $planId,
            'provider' => 'paystack',
            'platform' => $this->resolvePlatform($request),
            'provider_subscription_id' => $reference,
            'amount' => $amount / 100,
            'status' => 'pending',
            'starts_at' => now(),
            'expires_at' => $duration,
        ]);

        Log::info('Paystack payment initialized', [
            'user_id' => $user->id,
            'reference' => $reference,
            'amount' => $amount / 100,
            'plan_id' => $planId,
        ]);

        return response()->json([
            'reference' => $reference,
            'authorization_url' => $authorizationUrl,
            'amount' => $amount / 100,
        ]);
    }

    /**
     * Verify subscription
     */
    public function verify(Request $request)
    {
        try {
            $request->validate([
                'plan_id' => 'required_if:provider,apple',
                'provider' => 'required|in:apple,paystack,google',
                'receipt_data' => 'required_if:provider,apple',
                'transaction_reference' => 'required_if:provider,paystack',
                'is_yearly' => 'required_if:provider,paystack|boolean',
            ]);

            $user = $request->user();
            $provider = $request->provider;

            if ($provider === 'apple') {
                return $this->verifyAppleSubscription($request, $user);
            }

            if ($provider === 'paystack') {
                return $this->verifyPaystackSubscription($request, $user);
            }

            return response()->json(['message' => 'Provider not yet fully implemented'], 400);

        } catch (\Exception $e) {
            Log::error('Subscription verification failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to verify subscription',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    protected function verifyAppleSubscription($request, $user)
    {
        $settings = GlobalSetting::first();
        $sharedSecret = $settings->apple_shared_secret;
        $receiptData = $request->receipt_data;
        $clientPlanId = $request->plan_id;

        // Resolve plan from product ID
        $resolvedPlanId = $this->subscriptionService->resolveApplePlanId($clientPlanId);
        if (!$resolvedPlanId) {
            return response()->json(['message' => 'Invalid Apple product ID'], 422);
        }

        // Verify with Apple (Production first, then Sandbox fallback)
        $receiptData = trim($receiptData);

        if (empty($sharedSecret)) {
            Log::error('[Apple Verify] apple_shared_secret is empty in global_settings');
            return response()->json(['message' => 'Apple shared secret not configured'], 500);
        }

        Log::info('[Apple Verify] sending to production', [
            'receipt_length' => strlen($receiptData),
            'shared_secret_length' => strlen($sharedSecret),
        ]);

        $url = 'https://buy.itunes.apple.com/verifyReceipt';
        $response = Http::asJson()->post($url, [
            'receipt-data' => $receiptData,
            'password' => $sharedSecret,
            'exclude-old-transactions' => true,
        ]);

        $data = $response->json();

        Log::info('[Apple Verify] production response', [
            'status' => $data['status'] ?? 'unknown',
            'receipt_count' => count($data['latest_receipt_info'] ?? []),
        ]);

        if (isset($data['status']) && $data['status'] == 21007) {
            Log::info('[Apple Verify] sandbox fallback');
            $url = 'https://sandbox.itunes.apple.com/verifyReceipt';
            $response = Http::asJson()->post($url, [
                'receipt-data' => $receiptData,
                'password' => $sharedSecret,
                'exclude-old-transactions' => true,
            ]);
            $data = $response->json();
            Log::info('[Apple Verify] sandbox response', [
                'status' => $data['status'] ?? 'unknown',
                'receipt_count' => count($data['latest_receipt_info'] ?? []),
            ]);
        }

        if (!isset($data['status']) || $data['status'] !== 0) {
            return response()->json([
                'message' => 'Invalid Apple receipt',
                'apple_status' => $data['status'] ?? 'unknown',
            ], 422);
        }

        $latestReceipt = collect($data['latest_receipt_info'] ?? [])
            ->sortByDesc('expires_date_ms')
            ->first();

        if (!$latestReceipt) {
            return response()->json(['message' => 'No active subscription found in receipt.'], 422);
        }

        $expiresAt = \Carbon\Carbon::createFromTimestampMs($latestReceipt['expires_date_ms']);
        if ($expiresAt->isPast()) {
            return response()->json([
                'message' => 'Subscription found but it has expired.',
                'expires_at' => $expiresAt,
            ], 422);
        }

        // Verify the Apple product ID matches what the client claims
        $receiptProductId = $latestReceipt['product_id'];
        if ($receiptProductId !== $clientPlanId) {
            Log::warning('Apple product ID mismatch', [
                'client_plan_id' => $clientPlanId,
                'receipt_product_id' => $receiptProductId,
            ]);
            return response()->json(['message' => 'Product ID mismatch with receipt'], 422);
        }

        $transactionId = $latestReceipt['transaction_id'];
        $originalTransactionId = $latestReceipt['original_transaction_id'];

        // Prevent user from verifying another user's transaction
        $existing = Subscription::where('provider_subscription_id', $transactionId)
            ->orWhere('original_transaction_id', $originalTransactionId)
            ->first();
        if ($existing && $existing->user_id !== $user->id) {
            Log::warning('Apple transaction belongs to another user', [
                'transaction_id' => $transactionId,
                'claiming_user' => $user->id,
                'actual_user' => $existing->user_id,
            ]);
            return response()->json(['message' => 'Transaction does not belong to this user'], 403);
        }

        try {
            $subscription = $this->subscriptionService->activatePremium(
                user: $user,
                provider: 'apple',
                providerTransactionId: $transactionId,
                originalTransactionId: $originalTransactionId,
                planId: $resolvedPlanId,
                expiresAt: $expiresAt,
                amount: null,
                platform: 'ios',
                metadata: [
                    'receipt_status' => $data['status'],
                    'product_id' => $receiptProductId,
                ],
                deviceType: $request->device_type,
                osVersion: $request->os_version,
            );

            return response()->json([
                'message' => 'Apple subscription verified successfully',
                'is_premium' => true,
                'subscription_status' => 'active',
                'subscription_provider' => 'apple',
                'subscription_plan' => $resolvedPlanId,
                'subscription_expires_at' => $subscription->expires_at->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            Log::error('Apple subscription activation failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to activate subscription'], 500);
        }
    }

    protected function verifyPaystackSubscription($request, $user)
    {
        $settings = GlobalSetting::first();
        $secretKey = $settings->paystack_secret_key;
        $reference = $request->transaction_reference;

        // Prevent user from verifying another user's transaction
        $existing = Subscription::where('provider_subscription_id', $reference)->first();
        if ($existing && $existing->user_id !== $user->id) {
            Log::warning('Paystack reference belongs to another user', [
                'reference' => $reference,
                'claiming_user' => $user->id,
                'actual_user' => $existing->user_id,
            ]);
            return response()->json(['message' => 'Transaction does not belong to this user'], 403);
        }

        $response = Http::withToken($secretKey)
            ->get("https://api.paystack.co/transaction/verify/{$reference}");

        $data = $response->json();

        if (!$response->successful() || !isset($data['data']['status'])) {
            return response()->json([
                'message' => 'Paystack verification failed: ' . ($data['message'] ?? 'Unknown error'),
            ], 422);
        }

        $status = $data['data']['status'];
        $amount = $data['data']['amount'];
        $currency = $data['data']['currency'];

        if ($status !== 'success') {
            if (in_array($status, ['pending', 'processing', 'ongoing'])) {
                return response()->json([
                    'message' => 'Payment is still processing. Please wait a moment or check back later.',
                    'status' => $status,
                    'is_premium' => false,
                ], 200);
            }
            return response()->json([
                'message' => 'Payment status: ' . $status,
                'is_premium' => false,
            ], 422);
        }

        // Determine plan
        $isYearly = filter_var($request->is_yearly, FILTER_VALIDATE_BOOLEAN);
        $planId = $isYearly ? 'paystack_yearly' : 'paystack_monthly';

        // Verify amount against server-side plan configuration
        if (!$this->subscriptionService->verifyPaystackAmount($planId, $amount, $currency)) {
            Log::error('Paystack payment amount/currency mismatch', [
                'reference' => $reference,
                'user_id' => $user->id,
                'amount_kobo' => $amount,
                'currency' => $currency,
                'plan_id' => $planId,
            ]);
            return response()->json([
                'message' => 'Payment verification failed: amount or currency mismatch',
            ], 422);
        }

        $expiresAt = $isYearly ? now()->addYear() : now()->addMonth();

        try {
            $subscription = $this->subscriptionService->activatePremium(
                user: $user,
                provider: 'paystack',
                providerTransactionId: $reference,
                originalTransactionId: null,
                planId: $planId,
                expiresAt: $expiresAt,
                amount: $amount / 100,
                platform: $this->resolvePlatform($request),
                metadata: [
                    'paystack_status' => $status,
                    'channel' => $data['data']['channel'] ?? null,
                ],
                deviceType: $request->device_type,
                osVersion: $request->os_version,
            );

            return response()->json([
                'message' => 'Paystack payment verified successfully',
                'is_premium' => true,
                'subscription_status' => 'active',
                'subscription_provider' => 'paystack',
                'subscription_plan' => $planId,
                'subscription_expires_at' => $subscription->expires_at->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            Log::error('Paystack subscription activation failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to activate subscription'], 500);
        }
    }

    /**
     * Sync Paystack subscription status (for restoration/missed webhooks)
     */
    public function sync(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $settings = GlobalSetting::first();
        $secretKey = $settings->paystack_secret_key;

        $response = Http::withToken($secretKey)
            ->get("https://api.paystack.co/transaction", [
                'customer' => $user->email,
                'status' => 'success',
            ]);

        $data = $response->json();

        if ($response->successful() && isset($data['data']) && count($data['data']) > 0) {
            $foundSubscription = false;

            foreach ($data['data'] as $transaction) {
                $metadata = $transaction['metadata'] ?? [];
                $reference = $transaction['reference'];

                $existing = Subscription::where('provider_subscription_id', $reference)->first();

                if (!$existing && (isset($metadata['plan_id']) || isset($metadata['type']))) {
                    $isYearly = str_contains($metadata['plan_id'] ?? '', 'yearly');
                    $startsAt = \Carbon\Carbon::parse($transaction['paid_at']);
                    $expiresAt = $isYearly ? $startsAt->copy()->addYear() : $startsAt->copy()->addMonth();
                    $amount = ($transaction['amount'] ?? 0) / 100;
                    $paidStatus = $transaction['status'] ?? '';

                    if ($paidStatus !== 'success') {
                        continue;
                    }

                    if ($expiresAt->isFuture() && $amount > 0) {
                        $this->subscriptionService->activatePremium(
                            user: $user,
                            provider: 'paystack',
                            providerTransactionId: $reference,
                            originalTransactionId: null,
                            planId: $metadata['plan_id'] ?? 'premium',
                            expiresAt: $expiresAt,
                            amount: $amount,
                            platform: $metadata['platform'] ?? 'android',
                        );
                        $foundSubscription = true;
                    }
                } elseif ($existing && $existing->status === 'active' && $existing->expires_at->isFuture()) {
                    $foundSubscription = true;
                    if (!$user->is_premium) {
                        $user->update(['is_premium' => true]);
                    }
                }
            }

            if ($foundSubscription) {
                return response()->json([
                    'message' => 'Premium access restored.',
                    'is_premium' => true,
                    'subscription_status' => 'active',
                ]);
            }
        }

        return response()->json(['message' => 'No active subscription found to restore.'], 404);
    }

    protected function resolvePlatform(Request $request): string
    {
        $userAgent = $request->userAgent() ?? '';
        if (str_contains($userAgent, 'Android')) {
            return 'android';
        }
        if (str_contains($userAgent, 'iOS') || str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) {
            return 'ios';
        }
        // Check platform header or fallback
        return $request->header('X-Platform', 'android');
    }
}
