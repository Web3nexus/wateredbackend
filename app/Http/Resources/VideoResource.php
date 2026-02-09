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
            'youtubeUrl' => $this->youtube_url,
            'storageUrl' => $this->storage_url,
            'videoType' => $this->video_type,
            'thumbnailUrl' => $this->thumbnail_url,
            'duration' => $this->duration,
            'publishedAt' => $this->published_at?->toIso8601String(),
            'traditionId' => $this->tradition_id,
            'isActive' => $this->is_active,
            'isFeatured' => $this->is_featured,
            'is_liked' => $this->isLikedBy($request->user()),
            'likes_count' => (int) $this->likes_count,
            'comments_count' => (int) $this->comments_count,
            'createdAt' => $this->created_at?->toIso8601String(),
            'updatedAt' => $this->updated_at?->toIso8601String(),
        ];
    }
}
