<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
use App\Models\Subscription;
use App\Services\AppleService;
use App\Services\SubscriptionService;
use App\Services\GooglePlayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    protected SubscriptionService $subscriptionService;
    protected AppleService $appleService;
    protected GooglePlayService $googlePlayService;

    public function __construct(
        SubscriptionService $subscriptionService,
        AppleService $appleService,
        GooglePlayService $googlePlayService
    ) {
        $this->subscriptionService = $subscriptionService;
        $this->appleService = $appleService;
        $this->googlePlayService = $googlePlayService;
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
     * Verify subscription (Apple / Google only — Paystack deprecated)
     */
    public function verify(Request $request)
    {
        try {
            $request->validate([
                'plan_id' => 'required_if:provider,apple,google',
                'provider' => 'required|in:apple,paystack,google',
                'receipt_data' => 'required_if:provider,apple',
                'transaction_reference' => 'required_if:provider,paystack',
                'is_yearly' => 'required_if:provider,paystack|boolean',
                'purchase_token' => 'required_if:provider,google|string',
            ]);

            $user = $request->user();
            $provider = $request->provider;

            if ($provider === 'apple') {
                return $this->verifyAppleSubscription($request, $user);
            }

            if ($provider === 'google') {
                return $this->verifyGoogleSubscription($request, $user);
            }

            return response()->json(['message' => 'Provider not supported'], 400);

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
        $receiptData = trim($request->receipt_data);
        $clientPlanId = $request->plan_id;

        // Resolve plan from product ID
        $resolvedPlanId = $this->subscriptionService->resolveApplePlanId($clientPlanId);
        if (!$resolvedPlanId) {
            return response()->json(['message' => 'Invalid Apple product ID'], 422);
        }

        // Detect StoreKit 2 JWS format (starts with base64 of '{"alg":"ES256","x5c":[')
        if (str_starts_with($receiptData, 'eyJ')) {
            return $this->verifyAppleJWSSubscription($receiptData, $clientPlanId, $resolvedPlanId, $request, $user);
        }

        // Legacy PKCS7 receipt verification via Apple's /verifyReceipt endpoint
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

    protected function verifyAppleJWSSubscription(
        string $receiptData,
        string $clientPlanId,
        string $resolvedPlanId,
        $request,
        $user
    ) {
        Log::info('[Apple JWS] Verifying StoreKit 2 transaction JWS');

        $payload = $this->appleService->verifyTransactionJWS($receiptData);
        if (!$payload) {
            return response()->json([
                'message' => 'Invalid Apple StoreKit 2 transaction',
                'apple_status' => 21002,
            ], 422);
        }

        $transactionId = $payload->transactionId ?? null;
        $originalTransactionId = $payload->originalTransactionId ?? null;
        $productId = $payload->productId ?? null;
        $expiresDateMs = $payload->expiresDate ?? null;
        $environment = $payload->environment ?? 'Production';

        Log::info('[Apple JWS] Transaction decoded', [
            'transaction_id' => $transactionId,
            'product_id' => $productId,
            'environment' => $environment,
        ]);

        if (!$transactionId || !$productId) {
            return response()->json(['message' => 'Transaction payload missing required fields'], 422);
        }

        // Verify the product ID matches what the client claims
        if ($productId !== $clientPlanId) {
            Log::warning('[Apple JWS] Product ID mismatch', [
                'client_plan_id' => $clientPlanId,
                'jws_product_id' => $productId,
            ]);
            return response()->json(['message' => 'Product ID mismatch with receipt'], 422);
        }

        // For subscriptions, validate expiration
        $expiresAt = null;
        if ($expiresDateMs) {
            $expiresAt = \Carbon\Carbon::createFromTimestampMs((int) $expiresDateMs);
            if ($expiresAt->isPast()) {
                return response()->json([
                    'message' => 'Subscription found but it has expired.',
                    'expires_at' => $expiresAt,
                ], 422);
            }
        }

        if (!$expiresAt) {
            return response()->json(['message' => 'No expiration date in transaction'], 422);
        }

        // Prevent cross-user transaction reuse
        $existing = Subscription::where('provider_subscription_id', $transactionId)
            ->orWhere('original_transaction_id', $originalTransactionId)
            ->first();
        if ($existing && $existing->user_id !== $user->id) {
            Log::warning('[Apple JWS] Transaction belongs to another user', [
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
                    'verification_method' => 'jws',
                    'environment' => $environment,
                    'product_id' => $productId,
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
            Log::error('[Apple JWS] Subscription activation failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to activate subscription'], 500);
        }
    }

    protected function verifyGoogleSubscription($request, $user)
    {
        $settings      = GlobalSetting::first();
        $packageName   = $settings?->google_play_package_name;
        $purchaseToken = $request->purchase_token;
        $productId     = $request->plan_id;  // e.g. "com.watered.premium.monthly"

        if (!$packageName) {
            return response()->json(['message' => 'Google Play not configured'], 500);
        }

        // ── 1. Prevent cross-user token reuse ──────────────────────────────────
        $existing = Subscription::where('google_purchase_token', $purchaseToken)->first();
        if ($existing && $existing->user_id !== $user->id) {
            Log::warning('[Google] Purchase token belongs to another user', [
                'product_id'     => $productId,
                'claiming_user'  => $user->id,
                'actual_user'    => $existing->user_id,
            ]);
            return response()->json(['message' => 'Purchase token does not belong to this user'], 403);
        }

        // ── 2. Verify with Google Publisher API ───────────────────────────────
        $raw = $this->googlePlayService->verifySubscription($packageName, $productId, $purchaseToken);

        if (!$raw) {
            return response()->json(['message' => 'Google Play verification failed'], 422);
        }

        $parsed = $this->googlePlayService->parseSubscriptionData($raw);

        if (!$parsed['is_active']) {
            return response()->json([
                'message'            => 'Google Play subscription is not active',
                'subscription_state' => $parsed['state'],
                'is_premium'         => false,
            ], 422);
        }

        if (!$parsed['expires_at'] || $parsed['expires_at']->isPast()) {
            return response()->json(['message' => 'Subscription is expired'], 422);
        }

        // ── 3. Resolve internal plan ID from Play product ID ─────────────────
        $internalPlanId = $this->resolveGooglePlanId($productId, $settings);

        // ── 4. Acknowledge if not yet acknowledged (required by Google) ───────
        $ackResult = $this->googlePlayService->acknowledgeSubscription($packageName, $productId, $purchaseToken);
        if (!$ackResult) {
            Log::warning('[Google] Subscription acknowledge failed - may need retry', [
                'user_id'    => $user->id,
                'product_id' => $productId,
            ]);
        }

        // ── 5. Activate premium in DB ─────────────────────────────────────────
        try {
            $subscription = $this->subscriptionService->activatePremium(
                user:                    $user,
                provider:                'google',
                providerTransactionId:   $parsed['order_id'] ?? $purchaseToken,
                originalTransactionId:   null,
                planId:                  $internalPlanId,
                expiresAt:               $parsed['expires_at'],
                amount:                  null,
                platform:                'android',
                metadata:                [
                    'google_state'      => $parsed['state'],
                    'google_product_id' => $productId,
                    'auto_renews'       => $parsed['auto_renews'],
                ],
                deviceType: $request->device_type,
                osVersion:  $request->os_version,
            );

            // Persist the purchase token so RTDN webhooks can find this record later
            $subscription->update([
                'google_purchase_token' => $purchaseToken,
                'google_order_id'       => $parsed['order_id'],
                'google_product_id'     => $productId,
                'auto_renews'           => $parsed['auto_renews'],
            ]);

            Log::info('[Google] Subscription verified and activated', [
                'user_id'    => $user->id,
                'product_id' => $productId,
                'expires_at' => $parsed['expires_at'],
            ]);

            return response()->json([
                'message'                  => 'Google Play subscription verified successfully',
                'is_premium'               => true,
                'subscription_status'      => 'active',
                'subscription_provider'    => 'google',
                'subscription_plan'        => $internalPlanId,
                'subscription_expires_at'  => $subscription->expires_at->toIso8601String(),
                'auto_renews'              => $parsed['auto_renews'],
            ]);

        } catch (\Exception $e) {
            Log::error('[Google] Subscription activation failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to activate subscription'], 500);
        }
    }

    /**
     * Map a Google Play product ID (e.g. "com.watered.premium.monthly")
     * to the internal plan_id used in the DB.
     */
    private function resolveGooglePlanId(string $productId, $settings): string
    {
        // Check if the product ID matches the monthly or yearly Google product
        $monthlyId = $settings?->google_monthly_product_id ?? '';
        $yearlyId  = $settings?->google_yearly_product_id ?? '';

        if ($productId === $yearlyId || str_contains($productId, 'yearly') || str_contains($productId, 'annual')) {
            return 'google_yearly';
        }

        if ($productId === $monthlyId || str_contains($productId, 'monthly')) {
            return 'google_monthly';
        }

        // Fallback: treat as monthly
        return 'google_monthly';
    }
}
