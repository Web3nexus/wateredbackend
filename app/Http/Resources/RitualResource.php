<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RitualResource extends JsonResource
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
            'content' => $this->content,
            'media_urls' => $this->media_urls ?? [],
            'category' => $this->category,
            'steps' => $this->steps ?? [],
            'time_of_day' => $this->time_of_day,
            'symbolic_meaning' => $this->symbolic_meaning,
            'is_sacred_daily' => (bool) $this->is_sacred_daily,
            'tradition_id' => $this->tradition_id,
            'is_active' => (bool) ($this->is_active ?? true),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
