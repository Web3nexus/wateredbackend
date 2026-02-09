<?php

namespace App\Filament\Resources\LandingPageFeatures\Pages;

use App\Filament\Resources\LandingPageFeatures\LandingPageFeatureResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLandingPageFeature extends EditRecord
{
    protected static string $resource = LandingPageFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
