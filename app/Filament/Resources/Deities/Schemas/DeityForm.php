<?php

namespace App\Filament\Resources\Deities\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class DeityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('image_url')
                    ->image(),
                Select::make('tradition_id')
                    ->relationship('tradition', 'name')
                    ->required(),
                TextInput::make('origin'),
                Textarea::make('mythology_story')
                    ->columnSpanFull(),
                Textarea::make('symbols')
                    ->columnSpanFull(),
                Textarea::make('domains')
                    ->columnSpanFull(),
                Textarea::make('sacred_elements')
                    ->columnSpanFull(),
            ]);
    }
}
