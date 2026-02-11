<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'youtube_url' => $this->youtube_url,
            'storage_url' => $this->storage_url,
            'video_type' => $this->video_type,
            'thumbnail_url' => $this->thumbnail_url,
            'duration' => $this->duration,
            'published_at' => $this->published_at?->toIso8601String(),
            'tradition_id' => $this->tradition_id,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'is_liked' => $this->isLikedBy($request->user()),
            'likes_count' => (int) $this->likes_count,
            'comments_count' => (int) $this->comments_count,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
