<?php

namespace App\Filament\Resources\Temples\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TempleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('address'),
                TextInput::make('latitude')
                    ->numeric(),
                TextInput::make('longitude')
                    ->numeric(),
                FileUpload::make('image_url')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                    ->disk('public')
                    ->directory('temples')
                    ->visibility('public')
                    ->afterStateHydrated(fn (FileUpload $component, $record) => $component->state($record?->getRawOriginal('image_url'))),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
