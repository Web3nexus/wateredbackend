<?php

namespace App\Filament\Resources\Audios\Pages;

use App\Filament\Resources\Audios\AudioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAudios extends ListRecords
{
    protected static string $resource = AudioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
