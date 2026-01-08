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
            'primaryColor' => $this->primary_color ?? '#6366f1',
            'secondaryColor' => $this->secondary_color ?? '#8b5cf6',
            'defaultLanguage' => $this->default_language ?? 'en',
            'supportedLanguages' => $this->supported_languages ?? ['en'],
            'maintenanceMode' => $this->maintenance_mode ?? false,
            'siteName' => $this->site_name,
            'siteDescription' => $this->site_description,
            'logoPath' => $this->logo_path,
        ];
    }
}
