<?php

namespace App\Filament\Resources\GlobalSettings\Pages;

use App\Filament\Resources\GlobalSettings\GlobalSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditGlobalSetting extends EditRecord
{
    protected static string $resource = GlobalSettingResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}