<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Teaching;
use Illuminate\Http\JsonResponse;

class TeachingController extends Controller
{
    public function index(): JsonResponse
    {
        $teachings = Teaching::where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->paginate(15);

        // Map featured_image to full URL
        $teachings->getCollection()->transform(function ($teaching) {
            return [
                'id' => $teaching->id,
                'title' => $teaching->title,
                'slug' => $teaching->slug,
                'summary' => $teaching->summary,
                'featured_image' => $teaching->featured_image_url,
                'published_at' => $teaching->published_at,
            ];
        });

        return response()->json($teachings);
    }

    public function show($idOrSlug): JsonResponse
    {
        $teaching = Teaching::where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->firstOrFail();

        return response()->json([
            'id' => $teaching->id,
            'title' => $teaching->title,
            'slug' => $teaching->slug,
            'summary' => $teaching->summary,
            'content' => $teaching->content,
            'featured_image' => $teaching->featured_image_url,
            'published_at' => $teaching->published_at,
        ]);
    }
}
