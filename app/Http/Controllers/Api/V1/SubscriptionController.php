<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
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
            'subscription' => $subscription,
        ]);
    }

    /**
     * Verify subscription
     */
    public function verify(Request $request)
    {
        try {
            $request->validate([
                'plan_id' => 'required',
                'provider' => 'required|in:apple,paystack,google',
                'receipt_data' => 'required_if:provider,apple',
                'transaction_reference' => 'required_if:provider,paystack',
            ]);

            $user = $request->user();
            $provider = $request->provider;
            $settings = GlobalSetting::first();

            if ($provider === 'apple') {
                return $this->verifyAppleSubscription($request, $user, $settings);
            }

            if ($provider === 'paystack') {
                return $this->verifyPaystackSubscription($request, $user, $settings);
            }

            // Fallback for google if needed
            return response()->json(['message' => 'Provider not yet fully implemented'], 400);

        } catch (\Exception $e) {
            Log::error('Subscription verification failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to verify subscription',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    protected function verifyAppleSubscription($request, $user, $settings)
    {
        $sharedSecret = $settings->apple_shared_secret;
        $receiptData = $request->receipt_data;

        // Verify with Apple (Production first, then Sandbox if needed or vice versa)
        $url = 'https://buy.itunes.apple.com/verifyReceipt';
        $response = Http::post($url, [
            'receipt-data' => $receiptData,
            'password' => $sharedSecret,
            'exclude-old-transactions' => true,
        ]);

        $data = $response->json();

        // If status is 21007, it's a sandbox receipt sent to production
        if (isset($data['status']) && $data['status'] == 21007) {
            $url = 'https://sandbox.itunes.apple.com/verifyReceipt';
            $response = Http::post($url, [
                'receipt-data' => $receiptData,
                'password' => $sharedSecret,
                'exclude-old-transactions' => true,
            ]);
            $data = $response->json();
        }

        if (isset($data['status']) && $data['status'] == 0) {
            // Success
            $latestReceipt = collect($data['latest_receipt_info'] ?? [])->sortByDesc('expires_date_ms')->first();

            if (!$latestReceipt) {
                return response()->json(['message' => 'No active subscription found in receipt.'], 422);
            }

            // Check if it's actually active
            $expiresAt = \Carbon\Carbon::createFromTimestampMs($latestReceipt['expires_date_ms']);
            if ($expiresAt->isPast()) {
                return response()->json(['message' => 'Subscription found but it has expired.', 'expires_at' => $expiresAt], 422);
            }

            $user->subscriptions()->updateOrCreate(
                ['provider_subscription_id' => $latestReceipt['transaction_id']],
                [
                    'plan_id' => $request->plan_id ?? $latestReceipt['product_id'],
                    'provider' => 'apple',
                    'platform' => 'ios',
                    'status' => 'active',
                    'starts_at' => now(),
                    'expires_at' => $expiresAt,
                ]
            );

            $user->is_premium = true;
            $user->save();

            return response()->json([
                'message' => 'Apple subscription verified successfully',
                'is_premium' => true,
                'expires_at' => $expiresAt,
            ]);
        }

        return response()->json(['message' => 'Invalid Apple receipt', 'apple_status' => $data['status'] ?? 'unknown'], 422);
    }

    protected function verifyPaystackSubscription($request, $user, $settings)
    {
        $secretKey = $settings->paystack_secret_key;
        $reference = $request->transaction_reference;

        $response = Http::withToken($secretKey)
            ->get("https://api.paystack.co/transaction/verify/{$reference}");

        $data = $response->json();

        if ($response->successful() && isset($data['data']['status']) && $data['data']['status'] === 'success') {
            // Success
            // Determine duration based on plan_id in settings
            $isYearly = $request->plan_id == $settings->premium_yearly_id;
            $duration = $isYearly ? now()->addYear() : now()->addMonth();

            $user->subscriptions()->create([
                'plan_id' => $request->plan_id,
                'provider' => 'paystack',
                'platform' => 'android',
                'provider_subscription_id' => $reference,
                'amount' => $data['data']['amount'] / 100, // Paystack is in kobo/cents
                'status' => 'active',
                'starts_at' => now(),
                'expires_at' => $duration,
            ]);

            $user->is_premium = true;
            $user->save();

            return response()->json([
                'message' => 'Paystack payment verified successfully',
                'is_premium' => true,
            ]);
        }

        return response()->json(['message' => 'Paystack verification failed'], 422);
    }

    /**
     * Sync Paystack subscription status (for restoration/missed webhooks)
     */
    public function sync(Request $request)
    {
        $user = $request->user();
        if (!$user)
            return response()->json(['message' => 'Unauthorized'], 401);

        $settings = GlobalSetting::first();
        $secretKey = $settings->paystack_secret_key;

        // Fetch all transactions for this user/email from Paystack
        $response = Http::withToken($secretKey)
            ->get("https://api.paystack.co/transaction", [
                'customer' => $user->email,
                'status' => 'success'
            ]);

        $data = $response->json();

        if ($response->successful() && isset($data['data']) && count($data['data']) > 0) {
            $foundSubscription = false;

            foreach ($data['data'] as $transaction) {
                $metadata = $transaction['metadata'] ?? [];

                // If it was a subscription payment (indicated by reference or custom metadata)
                if (isset($metadata['plan_id']) || Str::startsWith($transaction['reference'], 'sub_')) {
                    // Check if already in our DB
                    $existing = Subscription::where('provider_subscription_id', $transaction['reference'])->first();

                    if (!$existing) {
                        $isYearly = ($metadata['plan_id'] ?? '') == $settings->premium_yearly_id;
                        // For sync, we assume the transaction date as start
                        $startsAt = \Carbon\Carbon::parse($transaction['paid_at']);
                        $expiresAt = $isYearly ? $startsAt->copy()->addYear() : $startsAt->copy()->addMonth();

                        if ($expiresAt->isFuture()) {
                            $user->subscriptions()->create([
                                'plan_id' => $metadata['plan_id'] ?? 'premium',
                                'provider' => 'paystack',
                                'platform' => 'android',
                                'provider_subscription_id' => $transaction['reference'],
                                'amount' => $transaction['amount'] / 100,
                                'status' => 'active',
                                'starts_at' => $startsAt,
                                'expires_at' => $expiresAt,
                            ]);
                            $foundSubscription = true;
                        }
                    } else if ($existing->status === 'active' && $existing->expires_at->isFuture()) {
                        $foundSubscription = true;
                    }
                }
            }

            if ($foundSubscription) {
                $user->is_premium = true;
                $user->save();
                return response()->json(['message' => 'Premium access restored.', 'is_premium' => true]);
            }
        }

        return response()->json(['message' => 'No active subscription found to restore.'], 404);
    }

    /**
     * Start a 7-day free trial for the user.
     */
    public function startTrial(Request $request)
    {
        $user = $request->user();

        // Check if user already has or had a trial
        $hasTrial = $user->subscriptions()->where('provider', 'trial')->exists();

        if ($hasTrial) {
            return response()->json(['message' => 'You have already used your free trial.'], 422);
        }

        // Create a 7-day trial subscription
        $user->subscriptions()->create([
            'plan_id' => 'free_trial',
            'provider' => 'trial',
            'platform' => 'android', // Conceptually applies to Android since iOS uses native trials
            'provider_subscription_id' => 'trial_' . Str::random(10),
            'amount' => 0,
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addDays(7),
        ]);

        $user->is_premium = true;
        $user->save();

        return response()->json([
            'message' => '7-day free trial started successfully.',
            'is_premium' => true,
        ]);
    }
}
