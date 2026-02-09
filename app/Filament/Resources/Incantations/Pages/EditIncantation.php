<?php

namespace App\Filament\Resources\Incantations\Pages;

use App\Filament\Resources\Incantations\IncantationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditIncantation extends EditRecord
{
    protected static string $resource = IncantationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
