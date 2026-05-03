<?php

namespace App\Filament\Resources\Rituals\Pages;

use App\Filament\Resources\Rituals\RitualResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditRitual extends EditRecord
{
    protected static string $resource = RitualResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $base = Storage::url('');
        if (!empty($data['media_urls']) && is_array($data['media_urls'])) {
            $data['media_urls'] = array_map(function ($url) use ($base) {
                return is_string($url) && str_starts_with($url, 'http')
                    ? str_replace($base, '', $url)
                    : $url;
            }, $data['media_urls']);
        }
        return $data;
    }
}
