<?php

namespace App\Filament\Resources\Videos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Get;

class VideoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Video Details')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->rows(3)
                            ->maxLength(1000),
                        Select::make('video_type')
                            ->label('Video Source')
                            ->options([
                                'youtube' => 'YouTube',
                                'file' => 'Direct File (Bunny/R2)',
                            ])
                            ->default('youtube')
                            ->reactive()
                            ->required(),
                        TextInput::make('youtube_url')
                            ->label('YouTube URL')
                            ->url()
                            ->required(fn($get) => $get('video_type') === 'youtube')
                            ->visible(fn($get) => $get('video_type') === 'youtube')
                            ->helperText('Paste the full YouTube video URL.'),
                        TextInput::make('storage_url')
                            ->label('File URL')
                            ->url()
                            ->required(fn($get) => $get('video_type') === 'file')
                            ->visible(fn($get) => $get('video_type') === 'file')
                            ->helperText('Direct link to MP4 or HLS stream (e.g. from Bunny.net or R2).'),
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
                        Toggle::make('is_featured')
                            ->label('Featured Content')
                            ->default(false),
                    ]),
            ]);
    }
}
