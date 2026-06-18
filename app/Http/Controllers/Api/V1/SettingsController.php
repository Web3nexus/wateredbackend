<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
use App\Models\Language;
use App\Models\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = GlobalSetting::first();

        if ($settings) {
            $settings->paystack_public_key = $settings->paystack_public_key
                ?? config('services.paystack.public_key');
        }

        return response()->json([
            'settings' => $settings,
            'languages' => Language::where('is_active', true)->get(),
            'statuses' => Status::all(),
        ]);
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
     * Return payment gateway public keys for one-time commerce purchases.
     */
    public function paymentKeys(): JsonResponse
    {
        $settings = GlobalSetting::first();

        if (!$settings) {
            return response()->json(['message' => 'Settings not configured'], 404);
        }

        $keys = [
            'stripe_public_key' => $settings->stripe_public_key,
            'flutterwave_public_key' => $settings->flutterwave_public_key,
            'paystack_public_key' => $settings->paystack_public_key
                ?? config('services.paystack.public_key'),
        ];

        return response()->json($keys);
    }
}