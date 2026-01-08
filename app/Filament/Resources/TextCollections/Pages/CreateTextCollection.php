<?php

namespace App\Filament\Resources\TextCollections\Pages;

use App\Filament\Resources\TextCollections\TextCollectionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTextCollection extends CreateRecord
{
    protected static string $resource = TextCollectionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
