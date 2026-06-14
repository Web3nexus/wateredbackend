<?php

namespace App\Services;

use App\Models\GlobalSetting;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\Middleware\AuthTokenMiddleware;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Log;

/**
 * Verifies Google Play purchase tokens against the
 * Google Publisher API using the service-account JSON
 * stored in GlobalSettings.
 */
class GooglePlayService
{
    private const SCOPE = 'https://www.googleapis.com/auth/androidpublisher';
    private const BASE_URL = 'https://androidpublisher.googleapis.com/androidpublisher/v3';

    /**
     * Verify a subscription purchase token.
     *
     * Returns an array with normalised data on success,
     * or null if verification fails.
     */
    public function verifySubscription(
        string $packageName,
        string $subscriptionId,
        string $purchaseToken
    ): ?array {
        try {
            $client = $this->buildClient();
            if (!$client) {
                Log::error('[GooglePlay] Service account not configured');
                return null;
            }

            $url = sprintf(
                '%s/applications/%s/purchases/subscriptionsv2/tokens/%s',
                self::BASE_URL,
                urlencode($packageName),
                urlencode($purchaseToken)
            );

            $response = $client->get($url);
            $data = json_decode($response->getBody()->getContents(), true);

            Log::info('[GooglePlay] verifySubscription raw response', [
                'subscriptionState' => $data['subscriptionState'] ?? 'unknown',
                'productId'         => $subscriptionId,
            ]);

            return $data;
        } catch (\Exception $e) {
            Log::error('[GooglePlay] verifySubscription failed: ' . $e->getMessage(), [
                'packageName'   => $packageName,
                'subscriptionId'=> $subscriptionId,
            ]);
            return null;
        }
    }

    /**
     * Acknowledge a subscription purchase.
     * Google requires acknowledgement within 3 days or it auto-refunds.
     */
    public function acknowledgeSubscription(
        string $packageName,
        string $subscriptionId,
        string $purchaseToken
    ): bool {
        try {
            $client = $this->buildClient();
            if (!$client) return false;

            $url = sprintf(
                '%s/applications/%s/purchases/subscriptions/%s/tokens/%s:acknowledge',
                self::BASE_URL,
                urlencode($packageName),
                urlencode($subscriptionId),
                urlencode($purchaseToken)
            );

            $client->post($url, ['json' => ['developerPayload' => '']]);
            return true;
        } catch (\Exception $e) {
            Log::error('[GooglePlay] acknowledgeSubscription failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Build an authenticated Guzzle client using the service account JSON
     * stored in GlobalSetting::google_play_service_account_json.
     */
    private function buildClient(): ?Client
    {
        $settings = GlobalSetting::first();
        $jsonString = $settings?->google_play_service_account_json ?? null;

        if (!$jsonString) return null;

        $keyData = is_array($jsonString)
            ? $jsonString
            : json_decode($jsonString, true);

        if (!$keyData || !isset($keyData['type'])) {
            Log::error('[GooglePlay] Invalid service account JSON in GlobalSettings');
            return null;
        }

        $credentials = new ServiceAccountCredentials(self::SCOPE, $keyData);
        $middleware   = new AuthTokenMiddleware($credentials);
        $stack        = HandlerStack::create();
        $stack->push($middleware);

        return new Client([
            'handler' => $stack,
            'auth'    => 'google_auth',
        ]);
    }

    /**
     * Parse a SubscriptionPurchase V2 response into normalised fields.
     *
     * subscriptionState values from Google:
     *   SUBSCRIPTION_STATE_ACTIVE, SUBSCRIPTION_STATE_CANCELED,
     *   SUBSCRIPTION_STATE_IN_GRACE_PERIOD, SUBSCRIPTION_STATE_ON_HOLD,
     *   SUBSCRIPTION_STATE_PAUSED, SUBSCRIPTION_STATE_EXPIRED
     */
    public function parseSubscriptionData(array $data): array
    {
        $state         = $data['subscriptionState'] ?? '';
        $isActive      = in_array($state, [
            'SUBSCRIPTION_STATE_ACTIVE',
            'SUBSCRIPTION_STATE_CANCELED',   // still has access until period end
            'SUBSCRIPTION_STATE_IN_GRACE_PERIOD',
        ]);

        // lineItems[0] holds the current recurring line
        $lineItem  = ($data['lineItems'] ?? [])[0] ?? [];
        $expiryMs  = $lineItem['expiryTime'] ?? null;
        $expiresAt = $expiryMs
            ? \Carbon\Carbon::createFromTimestampMs((int) $expiryMs)
            : null;

        $orderId = $data['latestOrderId'] ?? null;
        $autoRenews = ($lineItem['autoRenewingPlan']['autoRenewEnabled'] ?? false) === true;

        return [
            'is_active'   => $isActive,
            'expires_at'  => $expiresAt,
            'order_id'    => $orderId,
            'auto_renews' => $autoRenews,
            'state'       => $state,
            'raw'         => $data,
        ];
    }
}
