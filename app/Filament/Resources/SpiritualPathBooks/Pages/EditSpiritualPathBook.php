<?php

namespace App\Filament\Resources\SpiritualPathBooks\Pages;

use App\Filament\Resources\SpiritualPathBooks\SpiritualPathBookResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSpiritualPathBook extends EditRecord
{
    protected static string $resource = SpiritualPathBookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
