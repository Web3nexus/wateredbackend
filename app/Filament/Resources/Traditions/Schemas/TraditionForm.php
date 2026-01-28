<?php

namespace App\Filament\Resources\Traditions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class TraditionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('name.en')
                            ->label('Name (English)')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('URL-friendly identifier'),

                        Textarea::make('description.en')
                            ->label('Description (English)')
                            ->rows(4)
                            ->maxLength(1000),

                        Select::make('language_id')
                            ->relationship('language', 'name')
                            ->required()
                            ->default(fn() => \App\Models\Language::where('code', 'en')->first()?->id),

                        FileUpload::make('image_url')
                            ->label('Cover Image')
                            ->image()
                            ->disk('public')
                            ->directory('traditions')
                            ->visibility('public')
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2) // Make section 2 columns for inputs
                    ->columnSpanFull(), // Make section take full width
            ]);
    }
}
