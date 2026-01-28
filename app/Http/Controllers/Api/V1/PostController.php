<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Post::with(['user', 'comments', 'likes'])
            ->where('is_active', true)
            ->latest();

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $posts = $query->paginate(20);

        return response()->json($posts);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'media_urls' => 'array',
            'media_urls.*' => 'string',
        ]);

        $post = $request->user()->posts()->create([
            'content' => $validated['content'],
            'media_urls' => $validated['media_urls'] ?? [],
            'is_active' => true, // Auto-approve for now, or false if moderation needed
            'status' => 'published',
        ]);

        return response()->json($post->load('user'), 201);
    }

    public function toggleLike(Post $post, Request $request): JsonResponse
    {
        $user = $request->user();

        if ($post->isLikedBy($user)) {
            $post->likes()->where('user_id', $user->id)->delete();
            $message = 'Unliked';
        } else {
            $post->likes()->create(['user_id' => $user->id]);
            $message = 'Liked';
        }

        return response()->json(['message' => $message]);
    }

    public function share(Post $post, Request $request): JsonResponse
    {
        // Track share event (could log to analytics, increment share count, etc.)
        // For now, just return success
        return response()->json([
            'message' => 'Shared successfully',
            'post_id' => $post->id,
        ]);
    }
}
