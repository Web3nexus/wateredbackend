<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChapterResource;
use App\Http\Resources\EntryResource;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChapterController extends Controller
{
    /**
     * Get a single chapter
     */
    public function show(Chapter $chapter): JsonResponse
    {
        return response()->json(new ChapterResource($chapter));
    }

    /**
     * Get entries for a chapter
     */
    public function entries(Request $request, Chapter $chapter): JsonResponse
    {
        $perPage = $request->query('per_page', 50);
        $language = $request->query('language');

        $query = $chapter->entries()->where('is_active', true);

        // Load translations if language is specified
        if ($language) {
            $query->with([
                'translations' => function ($q) use ($language) {
                    $q->where('language_code', $language);
                }
            ]);
        }

        $entries = $query->orderBy('number')->paginate($perPage);

        return response()->json([
            'data' => EntryResource::collection($entries->items()),
            'current_page' => $entries->currentPage(),
            'last_page' => $entries->lastPage(),
            'per_page' => $entries->perPage(),
            'total' => $entries->total(),
        ]);
    }
}
