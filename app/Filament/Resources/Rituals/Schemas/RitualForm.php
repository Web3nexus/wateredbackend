<?php

namespace App\Filament\Resources\Rituals\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class RitualForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->placeholder('e.g. Morning Water Ritual')
                    ->helperText('The title that will appear in the Sacred Schedule.'),
                TextInput::make('category')
                    ->placeholder('e.g. Cleansing, Ancestral')
                    ->helperText('Used for filtering and grouping.'),
                TimePicker::make('time_of_day')
                    ->required()
                    ->seconds(false)
                    ->helperText('What time does this ritual typically occur? (Used for the countdown)'),
                Textarea::make('description')
                    ->placeholder('A short summary...')
                    ->columnSpanFull(),
                Textarea::make('content')
                    ->placeholder('The full ritual text or instructions...')
                    ->columnSpanFull(),
                FileUpload::make('media_urls')
                    ->label('Ritual Media (Audio)')
                    ->multiple()
                    ->disk('public')
                    ->directory('rituals')
                    ->maxSize(102400), // 100MB
                TextInput::make('steps')
                    ->placeholder('["Step 1", "Step 2", ...]')
                    ->helperText('Enter steps as a JSON array string.'),
                Textarea::make('symbolic_meaning')
                    ->placeholder('What does this ritual represent?')
                    ->columnSpanFull(),
            ]);
    }
}
