<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DailyWisdom;
use Illuminate\Http\Request;

class DailyWisdomController extends Controller
{
    public function index()
    {
        // Get today's wisdom, or random strict if not set, or latest
        $wisdom = DailyWisdom::where('is_active', true)
            ->whereDate('publish_date', now())
            ->first();

        if (!$wisdom) {
            $wisdom = DailyWisdom::where('is_active', true)
                ->inRandomOrder()
                ->first();
        }

        return response()->json(['data' => $wisdom]);
    }
}
