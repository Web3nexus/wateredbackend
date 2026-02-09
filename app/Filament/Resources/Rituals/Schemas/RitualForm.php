<?php

namespace App\Filament\Resources\Rituals\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class RitualForm
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
                TextInput::make('media_urls'),
                TextInput::make('category'),
                TextInput::make('steps'),
                TimePicker::make('time_of_day'),
                Textarea::make('symbolic_meaning')
                    ->columnSpanFull(),
            ]);
    }
}
