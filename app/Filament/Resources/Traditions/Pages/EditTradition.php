<?php

namespace App\Filament\Resources\Traditions\Pages;

use App\Filament\Resources\Traditions\TraditionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTradition extends EditRecord
{
    protected static string $resource = TraditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
