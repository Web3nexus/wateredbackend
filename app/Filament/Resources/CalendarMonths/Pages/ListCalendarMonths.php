<?php

namespace App\Filament\Resources\CalendarMonths\Pages;

use App\Filament\Resources\CalendarMonths\CalendarMonthResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCalendarMonths extends ListRecords
{
    protected static string $resource = CalendarMonthResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
