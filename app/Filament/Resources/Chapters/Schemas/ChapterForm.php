<?php

namespace App\Filament\Resources\Chapters\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ChapterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('collection_id')
                    ->label('Book')
                    ->relationship('collection', 'name')
                    ->required()
                    ->searchable(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('number')
                    ->required()
                    ->numeric(),
                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('order')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->default(true),
                Section::make('Bulk Verses Import')
                    ->description('Use this to import multiple verses at once. Note: This is an optional alternative to the entries table below.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Textarea::make('verses_content')
                            ->label('Verses Content')
                            ->rows(15)
                            ->helperText('Write verses in format: "1. First verse text\n2. Second verse text" - Each line starting with a number will become a verse.')
                            ->dehydrated(false),
                    ]),
            ]);
    }
}
