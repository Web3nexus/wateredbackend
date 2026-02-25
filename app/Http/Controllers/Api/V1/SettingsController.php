<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
use App\Models\Language;
use App\Models\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = GlobalSetting::first();
        $isNigeria = true;

        if ($settings) {
            $ip = request()->ip();
            $isNigeria = $this->checkIfNigeria($ip);

            if (!$isNigeria) {
                // If the user is outside Nigeria, use USD fields if they are set
                $settings->premium_monthly_price = $settings->premium_monthly_price_usd ?? $settings->premium_monthly_price;
                $settings->premium_monthly_amount = $settings->premium_monthly_amount_usd ?? $settings->premium_monthly_amount;
                $settings->premium_yearly_price = $settings->premium_yearly_price_usd ?? $settings->premium_yearly_price;
                $settings->premium_yearly_amount = $settings->premium_yearly_amount_usd ?? $settings->premium_yearly_amount;

                // Override currency symbols for USD display
                $settings->currency_symbol = '$';
                $settings->system_currency = 'USD';
            }
        }

        return response()->json([
            'settings' => $settings,
            'is_nigeria' => $isNigeria,
            'languages' => Language::where('is_active', true)->get(),
            'statuses' => Status::all(),
        ]);
    }

    protected function checkIfNigeria($ip): bool
    {
        // Skip local IPs
        if (in_array($ip, ['127.0.0.1', '::1'])) {
            return true;
        }

        return Cache::remember('geoip_' . $ip, 86400, function () use ($ip) {
            try {
                $response = Http::timeout(2)->get("http://ip-api.com/json/{$ip}?fields=countryCode");
                if ($response->successful()) {
                    $countryCode = $response->json('countryCode');
                    return $countryCode === 'NG';
                }
            } catch (\Exception $e) {
                Log::warning("GeoIP check failed for IP {$ip}: " . $e->getMessage());
            }
            return true; // Default to Nigeria if check fails
        });
    }

    public function legalDocuments(): JsonResponse
    {
        $settings = GlobalSetting::select(['privacy_policy', 'terms_of_service'])->first();
        return response()->json([
            'privacy_policy' => $settings?->privacy_policy,
            'terms_of_service' => $settings?->terms_of_service,
        ]);
    }

    /**
     * Return payment gateway keys to authenticated users only.
     * The secret keys are hidden from the public settings endpoint for security,
     * but the flutter_paystack_plus plugin requires the secret key client-side.
     */
    public function paymentKeys(): JsonResponse
    {
        $settings = GlobalSetting::first();

        if (!$settings) {
            return response()->json(['message' => 'Settings not configured'], 404);
        }

        // makeVisible temporarily bypasses the $hidden array for this response only
        $keys = $settings->makeVisible([
            'paystack_secret_key',
        ])->only([
                    'paystack_public_key',
                    'paystack_secret_key',
                    'paystack_monthly_plan_code',
                    'paystack_yearly_plan_code',
                    'stripe_public_key',
                    'flutterwave_public_key',
                ]);

        return response()->json($keys);
    }
}