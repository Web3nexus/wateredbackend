<?php

namespace App\Filament\Resources\EmailTemplates\Pages;

use App\Filament\Resources\EmailTemplates\EmailTemplateResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateEmailTemplate extends CreateRecord
{
    protected static string $resource = EmailTemplateResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;
}
