<?php

namespace App\Filament\Resources\SacredSounds\Pages;

use App\Filament\Resources\SacredSounds\SacredSoundResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSacredSounds extends ListRecords
{
    protected static string $resource = SacredSoundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
