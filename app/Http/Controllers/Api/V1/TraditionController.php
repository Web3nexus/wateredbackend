<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TraditionResource;
use App\Http\Resources\TextCollectionResource;
use App\Models\Tradition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TraditionController extends Controller
{
    /**
     * Get all traditions with pagination
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 20);

        $traditions = Tradition::where('is_active', true)
            ->orderBy('id')
            ->paginate($perPage);

        return response()->json([
            'data' => TraditionResource::collection($traditions->items()),
            'current_page' => $traditions->currentPage(),
            'last_page' => $traditions->lastPage(),
            'per_page' => $traditions->perPage(),
            'total' => $traditions->total(),
        ]);
    }

    /**
     * Get a single tradition
     */
    public function show(Tradition $tradition): JsonResponse
    {
        return response()->json(new TraditionResource($tradition));
    }

    /**
     * Get collections for a tradition
     */
    public function collections(Request $request, Tradition $tradition): JsonResponse
    {
        $perPage = $request->query('per_page', 20);
        $language = $request->query('language');

        $query = $tradition->textCollections()->where('is_active', true);

        // Language filtering would be applied here if text_collections had language support
        // For now, we'll return all active collections

        $collections = $query->orderBy('order')->paginate($perPage);

        return response()->json([
            'data' => TextCollectionResource::collection($collections->items()),
            'current_page' => $collections->currentPage(),
            'last_page' => $collections->lastPage(),
            'per_page' => $collections->perPage(),
            'total' => $collections->total(),
        ]);
    }
}
