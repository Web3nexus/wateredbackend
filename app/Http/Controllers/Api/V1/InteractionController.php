<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Audio;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    /**
     * Toggle like on any content type
     */
    public function toggleLike(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:post,audio',
            'id' => 'required|integer',
        ]);

        $model = $this->getModel($request->type, $request->id);
        if (!$model)
            return response()->json(['message' => 'Not found'], 404);

        $user = $request->user();
        $like = $model->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            $model->likes()->create(['user_id' => $user->id]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $model->likes()->count(),
        ]);
    }

    /**
     * Add comment to any content type
     */
    public function storeComment(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:post,audio',
            'id' => 'required|integer',
            'content' => 'required|string|max:1000',
        ]);

        $model = $this->getModel($request->type, $request->id);
        if (!$model)
            return response()->json(['message' => 'Not found'], 404);

        $commentData = [
            'user_id' => $request->user()->id,
            'content' => $request->content,
        ];

        // If it's a post, we still support the post_id column for legacy or compatibility
        if ($request->type === 'post') {
            $commentData['post_id'] = $request->id;
        }

        $comment = $model->comments()->create($commentData);

        return response()->json($comment->load('user:id,name'), 201);
    }

    /**
     * List comments for any content type
     */
    public function indexComments(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:post,audio',
            'id' => 'required|integer',
        ]);

        $model = $this->getModel($request->type, $request->id);
        if (!$model)
            return response()->json(['message' => 'Not found'], 404);

        $comments = $model->comments()
            ->with('user:id,name')
            ->latest()
            ->paginate(20);

        return response()->json($comments);
    }

    protected function getModel(string $type, int $id)
    {
        switch ($type) {
            case 'post':
                return Post::find($id);
            case 'audio':
                return Audio::find($id);
        }
        return null;
    }
}
