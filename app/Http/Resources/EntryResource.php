<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'number' => $this->number,
            'text' => $this->text,
            'chapterId' => $this->chapter_id,
            'order' => $this->order,
            'metadata' => $this->metadata,
            'isActive' => $this->is_active,
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];

        // Include translations if loaded
        if ($this->relationLoaded('translations')) {
            $data['translations'] = EntryTranslationResource::collection($this->translations);
        }

        return $data;
    }
}
