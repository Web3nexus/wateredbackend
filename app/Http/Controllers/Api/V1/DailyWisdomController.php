<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DailyWisdom;
use Illuminate\Http\Request;

class DailyWisdomController extends Controller
{
    public function index()
    {
        // Get today's wisdom based on active_date
        $wisdom = DailyWisdom::where('is_active', true)
            ->whereDate('active_date', now())
            ->first();

        // Fallback: if no wisdom for today, get the most recent one
        if (!$wisdom) {
            $wisdom = DailyWisdom::where('is_active', true)
                ->whereDate('active_date', '<=', now())
                ->orderBy('active_date', 'desc')
                ->first();
        }

        // Last resort: random wisdom
        if (!$wisdom) {
            $wisdom = DailyWisdom::where('is_active', true)
                ->inRandomOrder()
                ->first();
        }

        return response()->json(['data' => $wisdom]);
    }
}
