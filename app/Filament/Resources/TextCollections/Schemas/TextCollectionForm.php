<?php

namespace App\Filament\Resources\TextCollections\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Str;

class TextCollectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Collection Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                        TextInput::make('slug')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->rows(3)
                            ->maxLength(1000),
                    ]),

                Section::make('Classification')
                    ->description('Link this book to a spiritual path or topic.')
                    ->schema([
                        Select::make('tradition_id')
                            ->label('Spiritual Path')
                            ->relationship('tradition', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn($record) => $record !== null && str_contains(strtolower($record->tradition?->slug ?? ''), 'nima-sedani'))
                            ->dehydrated(),
                        Select::make('category_id')
                            ->label('Topic')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('order')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label('Visible on App')
                            ->default(true),
                    ]),

                Section::make('Chapters & Verses')
                    ->description('Manage chapters and verses.')
                    ->visible(fn($record) => $record !== null)
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('note')
                            ->label('How to add content')
                            ->content('To add chapters, please save this book first, then use the "Chapters" list found at the bottom of this page.'),
                    ]),
            ]);
    }
}
