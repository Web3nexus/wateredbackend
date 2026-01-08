<?php

namespace App\Filament\Resources\Audios\Pages;

use App\Filament\Resources\Audios\AudioResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAudio extends CreateRecord
{
    protected static string $resource = AudioResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
