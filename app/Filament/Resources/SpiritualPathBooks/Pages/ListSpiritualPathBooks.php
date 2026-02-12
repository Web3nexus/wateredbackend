<?php

namespace App\Filament\Resources\SpiritualPathBooks\Pages;

use App\Filament\Resources\SpiritualPathBooks\SpiritualPathBookResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSpiritualPathBooks extends ListRecords
{
    protected static string $resource = SpiritualPathBookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
