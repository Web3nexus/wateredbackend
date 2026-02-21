<?php

namespace App\Filament\Resources\OrderApplications\Pages;

use App\Filament\Resources\OrderApplications\OrderApplicationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageOrderApplications extends ManageRecords
{
    protected static string $resource = OrderApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
