<?php

namespace App\Observers;

use App\Models\Comment;

class CommentObserver
{
    /**
     * Handle the Comment "created" event.
     */
    public function created(Comment $comment): void
    {
        // Increment comment count on the commentable model
        if ($comment->commentable) {
            $comment->commentable->increment('comments_count');
        }
    }

    /**
     * Handle the Comment "deleted" event.
     */
    public function deleted(Comment $comment): void
    {
        // Decrement comment count on the commentable model
        if ($comment->commentable) {
            $comment->commentable->decrement('comments_count');
        }
    }
}
