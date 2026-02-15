<?php

namespace App\Filament\Resources\ConsultationTypes\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ConsultationTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('price')
                    ->numeric()
                    ->prefix('$') // Assuming USD for now
                    ->required()
                    ->step(0.01),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('duration_minutes')
                    ->required()
                    ->numeric()
                    ->default(60),
                FileUpload::make('image_url')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                    ->disk('public')
                    ->directory('consultation-types'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
