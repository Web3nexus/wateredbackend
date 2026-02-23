<?php

namespace App\Filament\Resources\Teachings\Pages;

use App\Filament\Resources\Teachings\TeachingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTeachings extends ListRecords
{
    protected static string $resource = TeachingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
