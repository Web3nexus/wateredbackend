<?php

namespace App\Filament\Resources\ConsultationTypes\Pages;

use App\Filament\Resources\ConsultationTypes\ConsultationTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateConsultationType extends CreateRecord
{
    protected static string $resource = ConsultationTypeResource::class;
}
