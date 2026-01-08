<?php

namespace App\Filament\Resources\TextCollections\Pages;

use App\Filament\Resources\TextCollections\TextCollectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTextCollections extends ListRecords
{
    protected static string $resource = TextCollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
