<?php

namespace App\Filament\Resources\Audios\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;

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
                        FileUpload::make('audio_url')
                            ->label('Audio File')
                            ->required()
                            ->disk('public')
                            ->directory('audios')
                            ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/aac'])
                            ->maxSize(51200) // 50MB
                            ->helperText('Upload the mp3/audio file.'),
                        FileUpload::make('thumbnail_url')
                            ->label('Artwork Image')
                            ->disk('public')
                            ->directory('thumbnails/audios')
                            ->image()
                            ->imageEditor()
                            ->helperText('Optional image for the player.'),
                        TextInput::make('author')
                            ->placeholder('e.g. Recitor Name, Speaker Name')
                            ->maxLength(255),
                        TextInput::make('duration')
                            ->placeholder('e.g. 15:30')
                            ->maxLength(50),
                    ]),

                Section::make('Publishing')
                    ->schema([
                        Select::make('category_id')
                            ->relationship('contentCategory', 'name', fn($query) => $query->whereIn('type', ['audio', 'both']))
                            ->label('Category')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('type')
                                    ->options([
                                        'video' => 'Video',
                                        'audio' => 'Audio',
                                        'both' => 'Both',
                                    ])
                                    ->default('audio')
                                    ->required(),
                            ]),
                        DateTimePicker::make('published_at')
                            ->required()
                            ->default(now()),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Toggle::make('is_featured')
                            ->label('Featured Content')
                            ->default(false),
                    ]),
            ]);
    }
}
