<?php

namespace App\Filament\Resources\Deities\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Schema;

class DeityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                RichEditor::make('description')
                    ->columnSpanFull(),
                FileUpload::make('image_url')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                    ->disk('public')
                    ->directory('deities'),
                Select::make('tradition_id')
                    ->relationship('tradition', 'name')
                    ->required(),
                TextInput::make('origin'),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(999)
                    ->required(),
                RichEditor::make('mythology_story')
                    ->columnSpanFull(),
                RichEditor::make('symbols')
                    ->columnSpanFull(),
                RichEditor::make('domains')
                    ->columnSpanFull(),
                RichEditor::make('sacred_elements')
                    ->columnSpanFull(),
            ]);
    }
}
