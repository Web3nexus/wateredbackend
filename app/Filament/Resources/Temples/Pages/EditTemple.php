<?php

namespace App\Filament\Resources\Temples\Pages;

use App\Filament\Resources\Temples\TempleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditTemple extends EditRecord
{
    protected static string $resource = TempleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $base = Storage::url('');
        if (!empty($data['image_url']) && str_starts_with($data['image_url'], 'http')) {
            $data['image_url'] = str_replace($base, '', $data['image_url']);
        }
        return $data;
    }
}
