<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AudioResource extends JsonResource
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
            'audio_url' => $this->audio_url,
            'thumbnail_url' => $this->thumbnail_url,
            'duration' => $this->duration,
            'author' => $this->author,
            'published_at' => $this->published_at?->toIso8601String(),
            'tradition_id' => $this->tradition_id,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'category' => $this->contentCategory?->name,
            'is_liked' => $this->isLikedBy($request->user()),
            'likes_count' => (int) $this->likes_count,
            'comments_count' => (int) $this->comments_count,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
