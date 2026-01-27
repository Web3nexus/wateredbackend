<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Video::where('is_active', true);

        // Filter by tradition
        if ($request->has('tradition_id')) {
            $query->where('tradition_id', $request->tradition_id);
        }

        // Search by title or description
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Filter by featured
        if ($request->has('featured')) {
            $query->where('is_featured', true);
        }

        $perPage = $request->query('per_page', 20);
        $videos = $query->latest('published_at')->paginate($perPage);

        return response()->json([
            'data' => VideoResource::collection($videos->items()),
            'current_page' => $videos->currentPage(),
            'last_page' => $videos->lastPage(),
            'per_page' => $videos->perPage(),
            'total' => $videos->total(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Video $video): JsonResponse
    {
        if (!$video->is_active) {
            return response()->json(['message' => 'Video not found'], 404);
        }

        return response()->json(new VideoResource($video));
    }
}
