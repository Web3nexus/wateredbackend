<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Temple;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TempleController extends Controller
{
    public function index()
    {
        $temples = Temple::where('is_active', true)
            ->latest()
            ->get();

        return response()->json(['data' => $temples]);
    }

    public function nearMe(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1', // Radius in km
        ]);

        $lat = $request->latitude;
        $lng = $request->longitude;
        $radius = $request->radius ?? 50;

        // Haversine formula for distance
        $temples = Temple::select('*')
            ->selectRaw(
                '( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance',
                [$lat, $lng, $lat]
            )
            ->having('distance', '<', $radius)
            ->orderBy('distance')
            ->where('is_active', true)
            ->get();

        return response()->json(['data' => $temples]);
    }

    public function show(Temple $temple)
    {
        return response()->json(['data' => $temple]);
    }
}
