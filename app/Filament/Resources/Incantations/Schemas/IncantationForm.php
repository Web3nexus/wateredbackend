<?php

namespace App\Filament\Resources\Incantations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Schemas\Schema;

class IncantationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('content')
                    ->columnSpanFull(),
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
                    ->label('Audio Recitation (Upload)')
                    ->disk('public')
                    ->directory('incantations')
                    ->maxSize(51200) // 50MB
                    ->visible(fn($get) => $get('audio_source_type') === 'upload')
                    ->required(fn($get) => $get('audio_source_type') === 'upload')
                    ->dehydrateStateUsing(function ($state, $get) {
                        if ($get('audio_source_type') === 'link') {
                            return $get('external_audio_url');
                        }
                        return $state;
                    }),
                TextInput::make('external_audio_url')
                    ->label('External Audio Link')
                    ->placeholder('https://example.com/audio.mp3')
                    ->visible(fn($get) => $get('audio_source_type') === 'link')
                    ->required(fn($get) => $get('audio_source_type') === 'link')
                    ->dehydrated(false)
                    ->helperText('Provide a direct link to the audio file.'),
                Toggle::make('is_paid')
                    ->required(),
                TextInput::make('category'),
                Textarea::make('spoken_text')
                    ->columnSpanFull(),
                Textarea::make('intended_outcome')
                    ->columnSpanFull(),
            ]);
    }
}
