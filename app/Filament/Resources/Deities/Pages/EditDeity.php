<?php

namespace App\Filament\Resources\Deities\Pages;

use App\Filament\Resources\Deities\DeityResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditDeity extends EditRecord
{
    protected static string $resource = DeityResource::class;

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
