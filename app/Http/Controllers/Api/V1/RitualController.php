<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Ritual;
use Illuminate\Http\Request;

class RitualController extends Controller
{
    /**
     * List rituals
     */
    public function index()
    {
        $rituals = Ritual::latest()->paginate(10);
        return response()->json($rituals);
    }

    /**
     * Show ritual details
     */
    public function show(Ritual $ritual)
    {
        return response()->json($ritual);
    }
}
