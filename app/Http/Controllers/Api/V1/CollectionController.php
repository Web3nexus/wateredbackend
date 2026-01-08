<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TextCollectionResource;
use App\Http\Resources\ChapterResource;
use App\Models\TextCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CollectionController extends Controller
{
    /**
     * Get a single collection
     */
    public function show(TextCollection $collection): JsonResponse
    {
        return response()->json(new TextCollectionResource($collection));
    }

    /**
     * Get chapters for a collection
     */
    public function chapters(Request $request, TextCollection $collection): JsonResponse
    {
        $perPage = $request->query('per_page', 50);

        $chapters = $collection->chapters()
            ->where('is_active', true)
            ->orderBy('number')
            ->paginate($perPage);

        return response()->json([
            'data' => ChapterResource::collection($chapters->items()),
            'current_page' => $chapters->currentPage(),
            'last_page' => $chapters->lastPage(),
            'per_page' => $chapters->perPage(),
            'total' => $chapters->total(),
        ]);
    }
}
