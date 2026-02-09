<?php

namespace App\Filament\Resources\Incantations\Pages;

use App\Filament\Resources\Incantations\IncantationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIncantations extends ListRecords
{
    protected static string $resource = IncantationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
