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
        \Illuminate\Support\Facades\Log::info('AudioResource - Processing Audio ID: ' . $this->id, [
            'original_url' => $this->audio_url,
            'full_url' => $this->full_audio_url,
            'title' => $this->title
        ]);
        return [
            'id' => $this->id,
            'title' => $this->title ?? 'Unknown Title',
            'description' => $this->description,
            'audio_url' => $this->full_audio_url ?: null,
            'thumbnail_url' => $this->full_thumbnail_url,
            'duration' => $this->duration,
            'author' => $this->author,
            'published_at' => $this->published_at?->toIso8601String(),
            'tradition_id' => $this->tradition_id,
            'is_active' => (bool) $this->is_active,
            'is_featured' => (bool) $this->is_featured,
            'category' => $this->contentCategory?->name,
            'is_liked' => (bool) $this->isLikedBy($request->user()),
            'likes_count' => (int) $this->likes_count,
            'comments_count' => (int) $this->comments_count,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
