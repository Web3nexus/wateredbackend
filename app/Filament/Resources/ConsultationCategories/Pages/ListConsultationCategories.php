<?php

namespace App\Filament\Resources\ConsultationCategories\Pages;

use App\Filament\Resources\ConsultationCategories\ConsultationCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListConsultationCategories extends ListRecords
{
    protected static string $resource = ConsultationCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
