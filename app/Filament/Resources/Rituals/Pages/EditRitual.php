<?php

namespace App\Filament\Resources\Rituals\Pages;

use App\Filament\Resources\Rituals\RitualResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRitual extends EditRecord
{
    protected static string $resource = RitualResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
