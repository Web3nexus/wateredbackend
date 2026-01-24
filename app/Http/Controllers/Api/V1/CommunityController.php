<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommunityController extends Controller
{
    /**
     * List posts (paginated)
     */
    public function index(Request $request)
    {
        $posts = Post::with(['user:id,name,profile_photo_path', 'likes']) // Eager load user and likes
            ->withCount(['likes', 'comments'])
            ->where('status', 'published')
            ->latest()
            ->paginate(10);

        // Append 'is_liked' attribute for the current user
        $posts->getCollection()->transform(function ($post) use ($request) {
            $post->is_liked = $post->isLikedBy($request->user());
            return $post;
        });

        return response()->json($posts);
    }

    /**
     * Create a new post
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'nullable|string|max:2000',
            'media_urls' => 'nullable|array',
            // Ensure at least one is present
        ]);

        if (!$request->content && !$request->media_urls) {
            return response()->json(['message' => 'Post cannot be empty.'], 422);
        }

        $post = $request->user()->posts()->create([
            'content' => $request->content,
            'media_urls' => $request->media_urls,
            'status' => 'published',
        ]);

        return response()->json($post->load('user:id,name,profile_photo_path'), 201);
    }

    /**
     * Delete a post
     */
    public function destroy(Request $request, Post $post)
    {
        // Simple authorization: only owner or super admin (later)
        if ($request->user()->id !== $post->user_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted.']);
    }

    /**
     * List comments for a post
     */
    public function comments(Post $post)
    {
        $comments = $post->comments()
            ->with('user:id,name,profile_photo_path')
            ->latest()
            ->paginate(20);

        return response()->json($comments);
    }

    /**
     * Add a comment to a post
     */
    public function storeComment(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $request->content,
        ]);

        return response()->json($comment->load('user:id,name,profile_photo_path'), 201);
    }

    /**
     * Toggle like on a post
     */
    public function toggleLike(Request $request, Post $post)
    {
        $user = $request->user();

        // check if already liked
        $like = $post->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            $post->likes()->create(['user_id' => $user->id]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $post->likes()->count(),
        ]);
    }
}
