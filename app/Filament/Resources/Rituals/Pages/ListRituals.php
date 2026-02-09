<?php

namespace App\Filament\Resources\Rituals\Pages;

use App\Filament\Resources\Rituals\RitualResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRituals extends ListRecords
{
    protected static string $resource = RitualResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
