<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    /**
     * Get current subscription status
     */
    public function index(Request $request)
    {
        $subscription = $request->user()->subscription;

        return response()->json([
            'is_premium' => $request->user()->is_premium,
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

            $user->subscriptions()->updateOrCreate(
                ['provider_subscription_id' => $latestReceipt['transaction_id']],
                [
                    'plan_id' => $request->plan_id,
                    'provider' => 'apple',
                    'status' => 'active',
                    'starts_at' => now(),
                    'expires_at' => isset($latestReceipt['expires_date_ms'])
                        ? \Carbon\Carbon::createFromTimestampMs($latestReceipt['expires_date_ms'])
                        : now()->addMonth(),
                ]
            );

            $user->is_premium = true;
            $user->save();

            return response()->json([
                'message' => 'Apple subscription verified successfully',
                'is_premium' => true,
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
            $user->subscriptions()->create([
                'plan_id' => $request->plan_id,
                'provider' => 'paystack',
                'provider_subscription_id' => $reference,
                'amount' => $data['data']['amount'] / 100, // Paystack is in kobo/cents
                'status' => 'active',
                'starts_at' => now(),
                'expires_at' => now()->addMonth(), // Assuming monthly for now
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
}
