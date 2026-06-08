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
        $paystackSecret = $settings->paystack_secret_key;

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
            case 'subscription.create':
            case 'subscription.enable':
                // Handled via charge.success verification
                break;
            case 'subscription.disable':
                $this->handlePaystackSubscriptionDisabled($data);
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

        // 3. Handle Subscription Payment
        $this->handleSubscriptionPayment($data, $metadata, $reference, $amount, $currency);
    }

    protected function handleSubscriptionPayment($data, $metadata, string $reference, int $amount, string $currency)
    {
        // Try to find existing subscription by reference
        $subscription = Subscription::where('provider_subscription_id', $reference)->first();

        if ($subscription) {
            // Subscription record exists - verify and activate
            $userId = $metadata['user_id'] ?? $subscription->user_id;
            $user = User::find($userId);

            if (!$user) {
                Log::error('Paystack webhook: user not found', [
                    'reference' => $reference,
                    'user_id' => $userId,
                ]);
                return;
            }

            $planId = $metadata['plan_id'] ?? $subscription->plan_id;

            // Verify amount against plan configuration
            if (!$this->subscriptionService->verifyPaystackAmount($planId, $amount, $currency)) {
                Log::warning('Paystack webhook: amount mismatch for subscription', [
                    'reference' => $reference,
                    'plan_id' => $planId,
                    'amount_kobo' => $amount,
                    'currency' => $currency,
                ]);
                // Still activate if user already has a pending record (amount might vary by region)
                if ($subscription->status !== 'pending') {
                    return;
                }
            }

            $isYearly = str_contains($planId, 'yearly');
            $expiresAt = $isYearly ? now()->addYear() : now()->addMonth();

            // If subscription was pending (created by initialize endpoint), use its pre-set expiry
            if ($subscription->status === 'pending' && $subscription->expires_at) {
                $expiresAt = $subscription->expires_at;
            }

            try {
                $sub = $this->subscriptionService->activatePremium(
                    user: $user,
                    provider: 'paystack',
                    providerTransactionId: $reference,
                    originalTransactionId: null,
                    planId: $planId,
                    expiresAt: $expiresAt,
                    amount: $amount / 100,
                    platform: $metadata['platform'] ?? 'android',
                );
                if ($sub && !empty($data['subscription_code'])) {
                    $sub->update(['raw_provider_event' => json_encode([
                        'subscription_code' => $data['subscription_code'],
                    ])]);
                }
                Log::info('Paystack webhook: subscription activated from existing record', [
                    'reference' => $reference,
                    'user_id' => $user->id,
                ]);
            } catch (\Exception $e) {
                Log::error('Paystack webhook: activation failed', [
                    'reference' => $reference,
                    'error' => $e->getMessage(),
                ]);
            }
            return;
        }

        // No existing subscription record - try to create from metadata
        if (isset($metadata['user_id']) && isset($metadata['plan_id'])) {
            $user = User::find($metadata['user_id']);
            if (!$user) {
                Log::error('Paystack webhook: user not found from metadata', [
                    'reference' => $reference,
                    'user_id' => $metadata['user_id'],
                ]);
                return;
            }

            $planId = $metadata['plan_id'];

            if (!$this->subscriptionService->verifyPaystackAmount($planId, $amount, $currency)) {
                Log::error('Paystack webhook: amount mismatch, rejecting', [
                    'reference' => $reference,
                    'plan_id' => $planId,
                    'amount_kobo' => $amount,
                ]);
                return;
            }

            $isYearly = str_contains($planId, 'yearly');
            $expiresAt = $isYearly ? now()->addYear() : now()->addMonth();

            try {
                $sub = $this->subscriptionService->activatePremium(
                    user: $user,
                    provider: 'paystack',
                    providerTransactionId: $reference,
                    originalTransactionId: null,
                    planId: $planId,
                    expiresAt: $expiresAt,
                    amount: $amount / 100,
                    platform: $metadata['platform'] ?? 'android',
                );
                if ($sub && !empty($data['subscription_code'])) {
                    $sub->update(['raw_provider_event' => json_encode([
                        'subscription_code' => $data['subscription_code'],
                    ])]);
                }
                Log::info('Paystack webhook: subscription created from metadata', [
                    'reference' => $reference,
                    'user_id' => $user->id,
                ]);
            } catch (\Exception $e) {
                Log::error('Paystack webhook: activation failed', [
                    'reference' => $reference,
                    'error' => $e->getMessage(),
                ]);
            }
            return;
        }

        // No metadata at all - try to find user by email from Paystack data
        $customerEmail = $data['customer']['email'] ?? null;
        if ($customerEmail) {
            $user = User::where('email', $customerEmail)->first();
            if ($user) {
                // Determine plan from amount or default
                $settings = GlobalSetting::first();
                $monthlyAmount = ($settings->premium_monthly_amount ?? 5000) * 100;
                $yearlyAmount = ($settings->premium_yearly_amount ?? 50000) * 100;

                $isYearly = abs($amount - $yearlyAmount) < abs($amount - $monthlyAmount);
                $planId = $isYearly ? 'paystack_yearly' : 'paystack_monthly';
                $expiresAt = $isYearly ? now()->addYear() : now()->addMonth();

                if ($this->subscriptionService->verifyPaystackAmount($planId, $amount, $currency)) {
                    try {
                        $this->subscriptionService->activatePremium(
                            user: $user,
                            provider: 'paystack',
                            providerTransactionId: $reference,
                            originalTransactionId: null,
                            planId: $planId,
                            expiresAt: $expiresAt,
                            amount: $amount / 100,
                            platform: 'android',
                        );
                        Log::info('Paystack webhook: subscription created from email lookup', [
                            'reference' => $reference,
                            'user_id' => $user->id,
                            'email' => $customerEmail,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Paystack webhook: activation failed via email', [
                            'reference' => $reference,
                            'error' => $e->getMessage(),
                        ]);
                    }
                    return;
                }
            }
        }

        Log::warning('Paystack webhook: could not process subscription payment', [
            'reference' => $reference,
            'has_metadata' => !empty($metadata),
            'has_customer_email' => isset($customerEmail),
        ]);
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

        // Mark any pending subscription as failed
        $subscription = Subscription::where('provider_subscription_id', $reference)
            ->where('status', 'pending')
            ->first();
        if ($subscription) {
            $subscription->update(['status' => 'failed']);
            Log::info('Paystack webhook: subscription marked as failed', [
                'reference' => $reference,
            ]);
        }

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

    protected function handlePaystackSubscriptionDisabled($data)
    {
        $subscriptionCode = $data['subscription_code'];

        // Search in provider_subscription_id, or raw_provider_event (JSON-stored subscription_code)
        $subscription = Subscription::where('provider_subscription_id', $subscriptionCode)
            ->orWhere('raw_provider_event', 'LIKE', '%' . $subscriptionCode . '%')
            ->first();

        if ($subscription) {
            $subscription->update(['status' => 'expired']);
            $user = $subscription->user;
            if ($user && !$user->subscriptions()->where('status', 'active')->exists()) {
                $user->update(['is_premium' => false]);
            }
            Log::info('Paystack webhook: subscription disabled', [
                'subscription_code' => $subscriptionCode,
                'user_id' => $subscription->user_id,
            ]);
        } else {
            Log::warning('Paystack webhook: subscription not found for disable', [
                'subscription_code' => $subscriptionCode,
            ]);
        }
    }
}
