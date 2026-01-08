<?php

namespace App\Filament\Resources\Audios\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class AudioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Audio Details')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->rows(3)
                            ->maxLength(1000),
                        TextInput::make('audio_url')
                            ->label('Audio URL')
                            ->required()
                            ->url()
                            ->helperText('Paste the URL to the mp3/audio file.'),
                        TextInput::make('thumbnail_url')
                            ->label('Artwork URL')
                            ->url()
                            ->helperText('Optional image URL for the player.'),
                        TextInput::make('author')
                            ->placeholder('e.g. Recitor Name, Speaker Name')
                            ->maxLength(255),
                        TextInput::make('duration')
                            ->placeholder('e.g. 15:30')
                            ->maxLength(50),
                    ]),

                Section::make('Publishing')
                    ->schema([
                        Select::make('tradition_id')
                            ->relationship('tradition', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        DateTimePicker::make('published_at')
                            ->required()
                            ->default(now()),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),
            ]);
    }
}
