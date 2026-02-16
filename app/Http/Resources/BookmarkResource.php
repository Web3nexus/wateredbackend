<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookmarkResource extends JsonResource
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
            'user_id' => $this->user_id,
            'bookmarkable_id' => $this->bookmarkable_id,
            'bookmarkable_type' => $this->bookmarkable_type,
            'bookmarkable' => $this->whenLoaded('bookmarkable', function () {
                if ($this->bookmarkable instanceof \App\Models\Audio) {
                    return new \App\Http\Resources\AudioResource($this->bookmarkable);
                }
                if ($this->bookmarkable instanceof \App\Models\Incantation) {
                    return new \App\Http\Resources\IncantationResource($this->bookmarkable);
                }
                if ($this->bookmarkable instanceof \App\Models\Chapter) {
                    return new \App\Http\Resources\ChapterResource($this->bookmarkable);
                }
                if ($this->bookmarkable instanceof \App\Models\Ritual) {
                    return new \App\Http\Resources\RitualResource($this->bookmarkable);
                }
                return $this->bookmarkable;
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
