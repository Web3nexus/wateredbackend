<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Appointment;
use App\Mail\UserAppointmentConfirmationMail;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WebhookController extends Controller
{
    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Apple Server-to-Server Notifications (App Store Server API v1 format)
     */
    public function apple(Request $request)
    {
        Log::info('Apple Webhook Received', $request->all());

        $data = $request->all();
        $notificationType = $data['notification_type'] ?? null;

        if (!$notificationType) {
            return response()->json(['message' => 'Invalid notification'], 400);
        }

        $latestReceipt = $data['latest_receipt_info'] ?? null;
        if (!$latestReceipt) {
            return response()->json(['message' => 'No receipt info'], 200);
        }

        $originalTransactionId = $latestReceipt['original_transaction_id'] ?? null;
        $transactionId = $latestReceipt['transaction_id'] ?? null;

        if (!$originalTransactionId && !$transactionId) {
            return response()->json(['message' => 'No transaction identifier'], 200);
        }

        // Find subscription by original_transaction_id or transaction_id
        $subscription = Subscription::where('original_transaction_id', $originalTransactionId)
            ->orWhere('provider_subscription_id', $transactionId)
            ->orWhere('provider_subscription_id', $originalTransactionId)
            ->first();

        if (!$subscription) {
            Log::warning("Apple notification: subscription not found for transaction", [
                'original_transaction_id' => $originalTransactionId,
                'transaction_id' => $transactionId,
            ]);
            return response()->json(['message' => 'Subscription not found'], 200);
        }

        $user = $subscription->user;
        if (!$user) {
            Log::error("Apple notification: user not found for subscription", [
                'subscription_id' => $subscription->id,
            ]);
            return response()->json(['message' => 'OK'], 200);
        }

        switch ($notificationType) {
            case 'DID_RENEW':
            case 'INTERACTIVE_RENEWAL':
            case 'SUBSCRIBED':
                $expiresAt = \Carbon\Carbon::createFromTimestampMs($latestReceipt['expires_date_ms']);
                try {
                    $this->subscriptionService->activatePremium(
                        user: $user,
                        provider: 'apple',
                        providerTransactionId: $transactionId ?? $originalTransactionId,
                        originalTransactionId: $originalTransactionId,
                        planId: $subscription->plan_id,
                        expiresAt: $expiresAt,
                        platform: 'ios',
                    );
                } catch (\Exception $e) {
                    Log::error("Apple notification: activation failed", [
                        'error' => $e->getMessage(),
                        'subscription_id' => $subscription->id,
                    ]);
                }
                break;

            case 'CANCEL':
            case 'DID_FAIL_TO_RENEW':
                // User cancelled or billing issue — still has access until period end
                Log::info("Apple notification: subscription will expire at period end", [
                    'notification_type' => $notificationType,
                    'subscription_id' => $subscription->id,
                    'user_id' => $user->id,
                    'expires_at' => $latestReceipt['expires_date_ms'] ?? 'unknown',
                ]);
                break;

            case 'EXPIRED':
            case 'REVOKE':
                try {
                    $this->subscriptionService->deactivatePremium(
                        user: $user,
                        providerTransactionId: $originalTransactionId ?? $transactionId,
                    );
                } catch (\Exception $e) {
                    Log::error("Apple notification: deactivation failed", [
                        'error' => $e->getMessage(),
                        'subscription_id' => $subscription->id,
                    ]);
                }
                break;

            case 'DID_CHANGE_RENEWAL_PREF':
            case 'DID_CHANGE_RENEWAL_STATUS':
                // User changed plan or turned off auto-renew - log it
                Log::info("Apple subscription status changed", [
                    'notification_type' => $notificationType,
                    'subscription_id' => $subscription->id,
                    'user_id' => $user->id,
                ]);
                break;
        }

        return response()->json(['message' => 'OK']);
    }

    /**
     * Paystack Webhook
     */
    public function paystack(Request $request)
    {
        // Verify Paystack Signature
        $settings = GlobalSetting::first();
        $paystackSecret = $settings?->paystack_secret_key
            ?? config('services.paystack.secret_key');

        if (!$paystackSecret) {
            Log::error('Paystack Webhook: Missing Secret Key in Global Settings');
            return response()->json(['message' => 'Internal configuration error'], 500);
        }

        $signature = $request->header('x-paystack-signature');
        if (!$signature) {
            Log::error('Paystack Webhook: Missing signature header');
            return response()->json(['message' => 'Missing signature'], 400);
        }

        $computedSignature = hash_hmac('sha512', $request->getContent(), $paystackSecret);
        if (!hash_equals($signature, $computedSignature)) {
            Log::error('Paystack Webhook: Invalid Signature');
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $event = $request->input('event');
        $data = $request->input('data');

        Log::info("Paystack Webhook: {$event}", [
            'reference' => $data['reference'] ?? null,
            'event' => $event,
        ]);

        switch ($event) {
            case 'charge.success':
                $this->handlePaystackChargeSuccess($data);
                break;
            case 'charge.failed':
                $this->handlePaystackChargeFailed($data);
                break;
        }

        return response()->json(['message' => 'OK']);
    }

    protected function handlePaystackChargeSuccess($data)
    {
        $reference = $data['reference'];
        $metadata = $data['metadata'] ?? [];
        $amount = $data['amount'] ?? 0;
        $currency = $data['currency'] ?? 'NGN';

        // 1. Handle Appointment Payment
        if (isset($metadata['type']) && $metadata['type'] === 'appointment') {
            $this->handleAppointmentPayment($data, $metadata, $reference);
            return;
        }

        // 2. Handle Event Payment
        if (isset($metadata['type']) && $metadata['type'] === 'event') {
            $this->handleEventPayment($data, $metadata, $reference);
            return;
        }
    }

    protected function handleAppointmentPayment($data, $metadata, string $reference)
    {
        $appointmentId = $metadata['appointment_id'] ?? null;
        $appointment = Appointment::find($appointmentId);

        if (!$appointment) {
            Log::warning('Paystack webhook: appointment not found', [
                'appointment_id' => $appointmentId,
            ]);
            return;
        }

        $paidAmount = $data['amount'] / 100;
        $expectedAmount = (float) $appointment->amount;
        $paidCurrency = $data['currency'] ?? 'NGN';

        if ($paidAmount < $expectedAmount || $paidCurrency !== 'NGN') {
            Log::error("Payment mismatch for Appointment #{$appointment->id}: Expected {$expectedAmount} NGN, Got {$paidAmount} {$paidCurrency}");
            $appointment->update([
                'payment_status' => 'failed',
                'notes' => 'Payment validation failed: Amount or Currency mismatch.',
            ]);
            return;
        }

        $appointment->update([
            'payment_status' => 'paid',
            'appointment_status' => 'confirmed',
            'payment_reference' => $reference,
        ]);

        try {
            Mail::to($appointment->email)->send(new UserAppointmentConfirmationMail($appointment));
        } catch (\Exception $e) {
            Log::error('Failed to send user appointment confirmation: ' . $e->getMessage());
        }
    }

    protected function handleEventPayment($data, $metadata, string $reference)
    {
        $eventId = $metadata['event_id'] ?? null;
        $event = \App\Models\Event::find($eventId);

        if (!$event) {
            Log::warning('Paystack webhook: event not found', [
                'event_id' => $eventId,
            ]);
            return;
        }

        $registration = $event->registrations()
            ->where(function ($q) use ($metadata) {
                if (isset($metadata['user_id'])) {
                    $q->where('user_id', $metadata['user_id']);
                } else {
                    $q->where('email', $metadata['email']);
                }
            })->first();

        if ($registration) {
            $registration->update([
                'payment_status' => 'completed',
                'payment_reference' => $reference,
                'amount' => $data['amount'] / 100,
                'payment_method' => $data['channel'],
                'status' => 'registered',
            ]);
        } else {
            // Verify amount before creating
            $paidAmount = $data['amount'] / 100;
            $expectedAmount = (float) ($event->price ?? 0);
            if ($paidAmount < $expectedAmount) {
                Log::warning('Paystack webhook: event payment amount mismatch', [
                    'paid' => $paidAmount,
                    'expected' => $expectedAmount,
                ]);
                return;
            }

            $event->registrations()->create([
                'user_id' => $metadata['user_id'] ?? null,
                'full_name' => $metadata['full_name'] ?? null,
                'email' => $metadata['email'] ?? null,
                'phone' => $metadata['phone'] ?? null,
                'status' => 'registered',
                'payment_reference' => $reference,
                'amount' => $data['amount'] / 100,
                'payment_status' => 'completed',
                'payment_method' => $data['channel'],
            ]);
        }
    }

    protected function handlePaystackChargeFailed($data)
    {
        $reference = $data['reference'];
        $metadata = $data['metadata'] ?? [];
        $gatewayResponse = $data['gateway_response'] ?? 'Payment failed';

        // Handle appointment payment
        if (isset($metadata['type']) && $metadata['type'] === 'appointment') {
            $appointmentId = $metadata['appointment_id'] ?? null;
            $appointment = Appointment::find($appointmentId);
            if ($appointment) {
                $appointment->update([
                    'payment_status' => 'failed',
                    'notes' => "Paystack Payment Failed: {$gatewayResponse}. Reference: {$reference}",
                ]);
            }
            return;
        }

        // Handle event payment
        if (isset($metadata['type']) && $metadata['type'] === 'event') {
            $eventId = $metadata['event_id'] ?? null;
            $event = \App\Models\Event::find($eventId);
            if ($event) {
                $registration = $event->registrations()
                    ->where(function ($q) use ($metadata) {
                        if (isset($metadata['user_id'])) {
                            $q->where('user_id', $metadata['user_id']);
                        } else {
                            $q->where('email', $metadata['email']);
                        }
                    })->first();
                if ($registration) {
                    $registration->update([
                        'payment_status' => 'failed',
                        'notes' => "Paystack Payment Failed: {$gatewayResponse}. Reference: {$reference}",
                    ]);
                }
            }
        }
    }

    /**
     * Google Play Real-Time Developer Notification (RTDN)
     * Delivered via Cloud Pub/Sub push subscription.
     *
     * Google sends a base64-encoded JSON message inside a Pub/Sub envelope.
     * The endpoint must return HTTP 200 to acknowledge receipt; any other
     * status causes Google to retry with exponential back-off.
     */
    public function google(Request $request)
    {
        // ── 0. Verify Pub/Sub push authentication ─────────────────────────────
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            Log::warning('[Google RTDN] Missing or invalid Authorization header');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // ── 1. Decode the Pub/Sub message ─────────────────────────────────────
        $envelope  = $request->all();
        $message   = $envelope['message'] ?? null;

        if (!$message || !isset($message['data'])) {
            Log::warning('[Google RTDN] Missing Pub/Sub message or data field');
            return response()->json(['message' => 'OK']); // always 200 to avoid retries
        }

        $decodedData = base64_decode($message['data']);
        $notification = json_decode($decodedData, true);

        Log::info('[Google RTDN] Received', ['notification' => $notification]);

        $packageName = $notification['packageName'] ?? null;

        // ── 2. Handle SubscriptionNotification ────────────────────────────────
        if (isset($notification['subscriptionNotification'])) {
            $sub = $notification['subscriptionNotification'];
            $this->handleGoogleSubscriptionNotification(
                purchaseToken:    $sub['purchaseToken'] ?? null,
                productId:        $sub['subscriptionId'] ?? null,
                notificationType: (int) ($sub['notificationType'] ?? 0),
                packageName:      $packageName,
            );
        }

        // ── 3. Handle OneTimeProductNotification (not needed for subscriptions) ─
        if (isset($notification['oneTimeProductNotification'])) {
            Log::info('[Google RTDN] OneTimeProduct notification — ignored for subscriptions');
        }

        // Always return 200 OK so Pub/Sub doesn't retry
        return response()->json(['message' => 'OK']);
    }

    /**
     * Google Subscription Notification Types:
     *  1  SUBSCRIPTION_RECOVERED        — recovered from account hold
     *  2  SUBSCRIPTION_RENEWED          — renewed successfully
     *  3  SUBSCRIPTION_CANCELED         — user cancelled (still active till period end)
     *  4  SUBSCRIPTION_PURCHASED        — new initial purchase
     *  5  SUBSCRIPTION_ON_HOLD          — payment failed, on hold
     *  6  SUBSCRIPTION_IN_GRACE_PERIOD  — billing retry in grace period
     *  7  SUBSCRIPTION_RESTARTED        — user reactivated from pause
     *  8  SUBSCRIPTION_PRICE_CHANGE     — user confirmed price change
     *  9  SUBSCRIPTION_DEFERRED         — renewal deferred
     * 12  SUBSCRIPTION_REVOKED          — refunded / revoked by Google
     * 13  SUBSCRIPTION_EXPIRED          — fully expired
     */
    protected function handleGoogleSubscriptionNotification(
        ?string $purchaseToken,
        ?string $productId,
        int $notificationType,
        ?string $packageName
    ): void {
        if (!$purchaseToken) {
            Log::warning('[Google RTDN] Missing purchaseToken');
            return;
        }

        // Find subscription by purchase token
        $subscription = Subscription::where('google_purchase_token', $purchaseToken)->first();

        Log::info('[Google RTDN] notificationType=' . $notificationType, [
            'purchase_token_found' => $subscription ? $subscription->id : 'not found',
            'product_id'           => $productId,
        ]);

        // ── ACTIVATION / RENEWAL events ───────────────────────────────────────
        if (in_array($notificationType, [1, 2, 4, 6, 7])) {
            // Re-verify with Google to get the fresh expiry date
            if ($packageName && $productId) {
                $googlePlayService = app(\App\Services\GooglePlayService::class);
                $raw    = $googlePlayService->verifySubscription($packageName, $productId, $purchaseToken);
                $parsed = $raw ? $googlePlayService->parseSubscriptionData($raw) : null;

                if ($parsed && $parsed['is_active'] && $parsed['expires_at']) {
                    if ($subscription) {
                        $user = $subscription->user;
                    } else {
                        // New purchase with no existing record — cannot map to user
                        // This happens when a purchase was made outside the app (e.g. Play Store website)
                        // We rely on the mobile client's verify call to create the record first.
                        Log::warning('[Google RTDN] Cannot map purchase token to user — awaiting client verify call');
                        return;
                    }

                    try {
                        $internalPlanId = str_contains($productId, 'yearly') ? 'google_yearly' : 'google_monthly';
                        $activatedSub = $this->subscriptionService->activatePremium(
                            user:                  $user,
                            provider:              'google',
                            providerTransactionId: $parsed['order_id'] ?? $purchaseToken,
                            originalTransactionId: null,
                            planId:                $internalPlanId,
                            expiresAt:             $parsed['expires_at'],
                            amount:                null,
                            platform:              'android',
                        );

                        $activatedSub->update([
                            'google_order_id' => $parsed['order_id'] ?? $subscription->google_order_id,
                            'auto_renews'     => $parsed['auto_renews'],
                        ]);

                        Log::info('[Google RTDN] Subscription activated/renewed', [
                            'user_id'         => $user->id,
                            'notification'    => $notificationType,
                            'expires_at'      => $parsed['expires_at'],
                        ]);
                    } catch (\Exception $e) {
                        Log::error('[Google RTDN] Activation failed: ' . $e->getMessage());
                    }
                }
            }
            return;
        }

        // ── CANCELLATION (still active until period end) ───────────────────────
        if ($notificationType === 3) {
            if ($subscription) {
                $subscription->update(['auto_renews' => false]);
                Log::info('[Google RTDN] Subscription cancelled (access until period end)', [
                    'subscription_id' => $subscription->id,
                ]);
            }
            return;
        }

        // ── EXPIRY / REVOCATION events ────────────────────────────────────────
        if (in_array($notificationType, [12, 13])) {
            if ($subscription && $subscription->user) {
                try {
                    $this->subscriptionService->deactivatePremium(
                        user:                    $subscription->user,
                        providerTransactionId:   $purchaseToken,
                    );
                    Log::info('[Google RTDN] Subscription expired/revoked', [
                        'subscription_id' => $subscription->id,
                        'type'            => $notificationType,
                    ]);
                } catch (\Exception $e) {
                    Log::error('[Google RTDN] Deactivation failed: ' . $e->getMessage());
                }
            }
            return;
        }

        // ── ON HOLD (payment failed — suspend without deactivating fully) ─────
        if ($notificationType === 5) {
            if ($subscription) {
                $subscription->update(['status' => 'on_hold', 'auto_renews' => false]);
                Log::info('[Google RTDN] Subscription on hold', ['subscription_id' => $subscription->id]);
            }
            return;
        }

        Log::info('[Google RTDN] Unhandled notification type', ['type' => $notificationType]);
    }
}
