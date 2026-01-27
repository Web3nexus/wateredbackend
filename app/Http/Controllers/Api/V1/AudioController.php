<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AudioResource;
use App\Models\Audio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AudioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Audio::where('is_active', true);

        // Filter by tradition
        if ($request->has('tradition_id')) {
            $query->where('tradition_id', $request->tradition_id);
        }

        // Search by title, description or author
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Filter by featured
        if ($request->has('featured')) {
            $query->where('is_featured', true);
        }

        $perPage = $request->query('per_page', 20);
        $audios = $query->latest('published_at')->paginate($perPage);

        return response()->json([
            'data' => AudioResource::collection($audios->items()),
            'current_page' => $audios->currentPage(),
            'last_page' => $audios->lastPage(),
            'per_page' => $audios->perPage(),
            'total' => $audios->total(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Audio $audio): JsonResponse
    {
        if (!$audio->is_active) {
            return response()->json(['message' => 'Audio not found'], 404);
        }

        return response()->json(new AudioResource($audio));
    }
}
