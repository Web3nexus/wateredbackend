<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
use App\Models\Language;
use App\Models\Status;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'settings' => GlobalSetting::first(),
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
}