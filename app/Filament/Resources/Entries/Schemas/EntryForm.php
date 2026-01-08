<?php

namespace App\Filament\Resources\Entries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('chapter_id')
                    ->relationship('chapter', 'name')
                    ->required()
                    ->searchable(),
                TextInput::make('number')
                    ->required()
                    ->numeric()
                    ->label('Verse Number'),
                Textarea::make('text')
                    ->required()
                    ->rows(10)
                    ->columnSpanFull()
                    ->placeholder('Enter the sacred text here...'),
                TextInput::make('order')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
