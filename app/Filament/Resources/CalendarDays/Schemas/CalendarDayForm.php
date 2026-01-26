<?php

namespace App\Filament\Resources\CalendarDays\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CalendarDayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('month')
                    ->required()
                    ->numeric(),
                TextInput::make('day')
                    ->required()
                    ->numeric(),
                TextInput::make('month_name')
                    ->required(),
                TextInput::make('day_name'),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('associated_deities'),
                TextInput::make('celebration_type'),
                Toggle::make('is_special_day')
                    ->required(),
                TextInput::make('color'),
            ]);
    }
}
