<?php

namespace App\Filament\Resources\TextCollections\Pages;

use App\Filament\Resources\TextCollections\TextCollectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTextCollection extends EditRecord
{
    protected static string $resource = TextCollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
