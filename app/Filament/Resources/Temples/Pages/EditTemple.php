<?php

namespace App\Filament\Resources\Temples\Pages;

use App\Filament\Resources\Temples\TempleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTemple extends EditRecord
{
    protected static string $resource = TempleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
