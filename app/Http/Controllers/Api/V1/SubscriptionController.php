<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Get current subscription status
     */
    public function index(Request $request)
    {
        $subscription = $request->user()->subscription; // Assuming hasOne relationship

        return response()->json([
            'is_premium' => $request->user()->is_premium,
            'subscription' => $subscription,
        ]);
    }

    /**
     * Verify subscription (Mock for now)
     */
    public function verify(Request $request)
    {
        try {
            $request->validate([
                'plan_id' => 'required',
                'provider' => 'required',
                'provider_subscription_id' => 'required',
            ]);

            $user = $request->user();

            // Create Subscription record
            $user->subscriptions()->create([
                'plan_id' => $request->plan_id,
                'provider' => $request->provider,
                'provider_subscription_id' => $request->provider_subscription_id,
                'status' => 'active',
                'starts_at' => now(),
                'expires_at' => now()->addMonth(),
            ]);

            // Update user premium status
            $user->is_premium = true;
            $user->save();

            return response()->json([
                'message' => 'Subscription verified and user upgraded to premium',
                'is_premium' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to verify subscription',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
