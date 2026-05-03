<?php

namespace App\Filament\Resources\Traditions\Pages;

use App\Filament\Resources\Traditions\TraditionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditTradition extends EditRecord
{
    protected static string $resource = TraditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $base = Storage::url('');
        if (!empty($data['deity_image_url']) && str_starts_with($data['deity_image_url'], 'http')) {
            $data['deity_image_url'] = str_replace($base, '', $data['deity_image_url']);
        }
        return $data;
    }
}
