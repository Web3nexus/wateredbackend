<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GlobalSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'primary_color' => $this->primary_color ?? '#6366f1',
            'secondary_color' => $this->secondary_color ?? '#8b5cf6',
            'default_language' => $this->default_language ?? 'en',
            'supported_languages' => $this->supported_languages ?? ['en'],
            'maintenance_mode' => (bool) ($this->maintenance_mode ?? false),
            'site_name' => $this->site_name,
            'site_description' => $this->site_description,
            'logo_path' => $this->logo_path,
        ];
    }
}
