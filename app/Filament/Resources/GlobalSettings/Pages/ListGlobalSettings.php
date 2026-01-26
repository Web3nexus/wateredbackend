<?php

namespace App\Filament\Resources\GlobalSettings\Pages;

use App\Filament\Resources\GlobalSettings\GlobalSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListGlobalSettings extends ListRecords
{
    protected static string $resource = GlobalSettingResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }

    public function mount(): void
    {
        $record = \App\Models\GlobalSetting::first();

        if ($record) {
            $this->redirect($this->getResource()::getUrl('edit', ['record' => $record]));
        } else {
            // Create one if it doesn't exist
            $record = \App\Models\GlobalSetting::create(['site_name' => 'Watered']);
            $this->redirect($this->getResource()::getUrl('edit', ['record' => $record]));
        }
    }
}
