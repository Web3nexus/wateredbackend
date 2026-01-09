<?php

namespace App\Filament\Resources\DailyWisdoms\Pages;

use App\Filament\Resources\DailyWisdoms\DailyWisdomResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDailyWisdom extends EditRecord
{
    protected static string $resource = DailyWisdomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
