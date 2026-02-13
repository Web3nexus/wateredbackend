<?php

namespace App\Filament\Resources\Incantations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
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
                FileUpload::make('audio_url')
                    ->label('Audio Recitation')
                    ->disk('public')
                    ->directory('incantations')
                    ->maxSize(51200), // 50MB
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
