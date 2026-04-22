<?php

namespace App\Filament\Resources\TextCollections\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
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
                        RichEditor::make('description')
                            ->columnSpanFull()
                            ->maxLength(1000),
                        FileUpload::make('cover_image')
                            ->image()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->disk('public')
                            ->directory('collections'),
                        RichEditor::make('content')
                            ->label('Rich Content (MAIN)')
                            ->columnSpanFull(),
                    ]),

                Section::make('Classification (Optional)')
                    ->description('Optionally link this book to a spiritual path or topic. Leave blank for standalone texts like Nima Sedani.')
                    ->schema([
                        Select::make('tradition_id')
                            ->label('Spiritual Path')
                            ->relationship('tradition', 'name')
                            ->searchable()
                            ->preload(),
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
                        Toggle::make('is_premium')
                            ->label('Is Premium?')
                            ->default(false),
                        TextInput::make('price')
                            ->label('Price (NGN)')
                            ->numeric()
                            ->prefix('₦')
                            ->default(0),
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
