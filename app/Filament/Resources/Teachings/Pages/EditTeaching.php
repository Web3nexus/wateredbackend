<?php

namespace App\Filament\Resources\Teachings\Pages;

use App\Filament\Resources\Teachings\TeachingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTeaching extends EditRecord
{
    protected static string $resource = TeachingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
