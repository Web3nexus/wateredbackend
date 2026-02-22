<?php

namespace App\Filament\Resources\Rituals\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
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
                Select::make('category')
                    ->options([
                        'cleansing' => 'Cleansing',
                        'ancestral' => 'Ancestral',
                        'protection' => 'Protection',
                        'healing' => 'Healing',
                        'prosperity' => 'Prosperity',
                        'initiation' => 'Initiation',
                        'general' => 'General',
                    ])
                    ->required()
                    ->helperText('Used for filtering and grouping.'),
                Toggle::make('is_sacred_daily')
                    ->label('Is Sacred Daily?')
                    ->helperText('If enabled, this ritual will be permanent on the Home Screen and excluded from the general rituals list.')
                    ->default(false),
                TimePicker::make('time_of_day')
                    ->required()
                    ->seconds(false)
                    ->helperText('What time does this ritual typically occur? (Used for the countdown)'),
                RichEditor::make('description')
                    ->placeholder('A short summary...')
                    ->columnSpanFull(),
                RichEditor::make('content')
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
                RichEditor::make('symbolic_meaning')
                    ->placeholder('What does this ritual represent?')
                    ->columnSpanFull(),
            ]);
    }
}
