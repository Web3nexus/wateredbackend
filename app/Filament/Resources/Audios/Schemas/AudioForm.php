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
use Filament\Forms\Components\Radio;

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
                        Radio::make('audio_source_type')
                            ->label('Audio Source')
                            ->options([
                                'upload' => 'File Upload',
                                'link' => 'External Link',
                            ])
                            ->default('upload')
                            ->reactive()
                            ->afterStateHydrated(function ($set, $state, $record) {
                                if ($record && $record->audio_url && str_starts_with($record->getRawOriginal('audio_url'), 'http')) {
                                    $set('audio_source_type', 'link');
                                    $set('external_audio_url', $record->getRawOriginal('audio_url'));
                                }
                            }),
                        FileUpload::make('audio_url')
                            ->label('Audio File')
                            ->disk('public')
                            ->directory('audios')
                            ->maxSize(102400) // 100MB
                            ->visible(fn($get) => $get('audio_source_type') === 'upload')
                            ->required(fn($get) => $get('audio_source_type') === 'upload')
                            ->dehydrateStateUsing(function ($state, $get) {
                                if ($get('audio_source_type') === 'link') {
                                    return $get('external_audio_url');
                                }
                                return $state;
                            })
                            ->helperText('Upload the audio recording.'),
                        TextInput::make('external_audio_url')
                            ->label('External Audio Link')
                            ->placeholder('https://example.com/audio.mp3')
                            ->visible(fn($get) => $get('audio_source_type') === 'link')
                            ->required(fn($get) => $get('audio_source_type') === 'link')
                            ->dehydrated(false)
                            ->helperText('Provide a direct link to the audio file.'),
                        TextInput::make('thumbnail_url')
                            ->label('Artwork Image URL')
                            ->url()
                            ->placeholder('https://assets.audiomack.com/...')
                            ->helperText('Paste an image link from Audiomack or other sources.'),
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
                            ->label('Tradition (Optional)')
                            ->searchable()
                            ->preload(),
                        Select::make('category_id')
                            ->relationship('contentCategory', 'name', fn($query) => $query->whereIn('type', ['audio', 'both']))
                            ->label('Category (Optional)')
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
                                        'audio' => 'Audio',
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
                        Toggle::make('is_premium')
                            ->label('Premium Content')
                            ->default(false),
                    ]),
            ]);
    }
}
