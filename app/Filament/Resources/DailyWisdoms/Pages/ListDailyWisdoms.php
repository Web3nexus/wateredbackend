<?php

namespace App\Filament\Resources\DailyWisdoms\Pages;

use App\Filament\Resources\DailyWisdoms\DailyWisdomResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDailyWisdoms extends ListRecords
{
    protected static string $resource = DailyWisdomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
