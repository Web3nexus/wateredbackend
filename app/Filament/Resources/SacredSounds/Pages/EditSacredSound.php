<?php

namespace App\Filament\Resources\SacredSounds\Pages;

use App\Filament\Resources\SacredSounds\SacredSoundResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSacredSound extends EditRecord
{
    protected static string $resource = SacredSoundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
