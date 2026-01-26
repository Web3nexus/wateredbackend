<?php

namespace App\Filament\Resources\CalendarMonths\Pages;

use App\Filament\Resources\CalendarMonths\CalendarMonthResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCalendarMonth extends EditRecord
{
    protected static string $resource = CalendarMonthResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
