<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Deity;
use Illuminate\Http\Request;

class DeityController extends Controller
{
    /**
     * List all deities, optionally filtered by tradition_id
     */
    public function index(Request $request)
    {
        $query = Deity::with('tradition');

        if ($request->has('tradition_id')) {
            $query->where('tradition_id', $request->tradition_id);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $deities = $query->orderBy('sort_order')->latest()->paginate(20);

        return response()->json($deities);
    }

    /**
     * Show details
     */
    public function show(Deity $deity)
    {
        return response()->json($deity->load('tradition'));
    }
}
