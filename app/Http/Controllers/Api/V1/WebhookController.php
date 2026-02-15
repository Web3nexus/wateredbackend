<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Appointment;
use App\Mail\UserAppointmentConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WebhookController extends Controller
{
    /**
     * Apple Server-to-Server Notifications
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

        $transactionId = $latestReceipt['original_transaction_id'] ?? $latestReceipt['transaction_id'];
        $subscription = Subscription::where('provider_subscription_id', $transactionId)
            ->orWhere('provider_subscription_id', $latestReceipt['transaction_id'])
            ->first();

        if (!$subscription) {
            Log::warning("Subscription not found for Apple transaction: " . $transactionId);
            return response()->json(['message' => 'Subscription not found'], 200);
        }

        $user = $subscription->user;

        switch ($notificationType) {
            case 'DID_RENEW':
            case 'INTERACTIVE_RENEWAL':
            case 'SUBSCRIBED':
                $subscription->update([
                    'status' => 'active',
                    'platform' => 'ios',
                    'expires_at' => \Carbon\Carbon::createFromTimestampMs($latestReceipt['expires_date_ms']),
                ]);
                $user->update(['is_premium' => true]);
                break;

            case 'CANCEL':
            case 'DID_FAIL_TO_RENEW':
            case 'EXPIRED':
                $subscription->update(['status' => 'expired']);
                // Check if user has other active subscriptions before downgrading
                if (!$user->subscriptions()->where('status', 'active')->exists()) {
                    $user->update(['is_premium' => false]);
                }
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

        $signature = $request->header('x-paystack-signature');
        if (!$signature || $signature !== hash_hmac('sha512', $request->getContent(), $paystackSecret)) {
            Log::error('Invalid Paystack Signature');
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $event = $request->input('event');
        $data = $request->input('data');

        Log::info("Paystack Webhook: {$event}", $data);

        switch ($event) {
            case 'charge.success':
                $this->handlePaystackChargeSuccess($data);
                break;
            case 'subscription.create':
            case 'subscription.enable':
                // Handled in verification or here
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

        // 1. Handle Appointment Payment
        if (isset($metadata['type']) && $metadata['type'] === 'appointment') {
            $appointmentId = $metadata['appointment_id'] ?? null;
            $appointment = Appointment::find($appointmentId);

            if ($appointment) {
                $appointment->update([
                    'payment_status' => 'paid',
                    'appointment_status' => 'confirmed',
                    'payment_reference' => $reference,
                ]);

                // Send Confirmation Email to User
                try {
                    Mail::to($appointment->email)->send(new UserAppointmentConfirmationMail($appointment));
                } catch (\Exception $e) {
                    Log::error('Failed to send user appointment confirmation: ' . $e->getMessage());
                }

                return;
            }
        }

        // 2. Handle Subscription Payment (Default behavior)
        $subscription = Subscription::where('provider_subscription_id', $reference)->first();

        if ($subscription) {
            $subscription->update(['status' => 'active']);
            $subscription->user->update(['is_premium' => true]);
        }
    }

    protected function handlePaystackSubscriptionDisabled($data)
    {
        $subscriptionCode = $data['subscription_code'];
        $subscription = Subscription::where('provider_subscription_id', $subscriptionCode)->first();

        if ($subscription) {
            $subscription->update(['status' => 'expired']);
            $user = $subscription->user;
            if (!$user->subscriptions()->where('status', 'active')->exists()) {
                $user->update(['is_premium' => false]);
            }
        }
    }
}
