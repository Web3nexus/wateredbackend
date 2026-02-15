<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Ritual;
use Illuminate\Http\Request;

use App\Http\Resources\RitualResource;

class RitualController extends Controller
{
    /**
     * List rituals
     */
    public function index(Request $request)
    {
        $rituals = Ritual::when($request->category, function ($query, $category) {
            return $query->where('category', $category);
        })->latest()->paginate(20);

        return RitualResource::collection($rituals);
    }

    /**
     * Show ritual details
     */
    public function show(Ritual $ritual)
    {
        return new RitualResource($ritual);
    }
}
