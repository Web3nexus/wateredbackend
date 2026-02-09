<?php

namespace App\Filament\Resources\LandingPageFeatures\Pages;

use App\Filament\Resources\LandingPageFeatures\LandingPageFeatureResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLandingPageFeatures extends ListRecords
{
    protected static string $resource = LandingPageFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
