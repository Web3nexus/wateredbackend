<?php

namespace App\Filament\Resources\CalendarMonths\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class CalendarMonthForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('number')
                    ->numeric()
                    ->required(),
                TextInput::make('standard_name')
                    ->required(),
                TextInput::make('custom_name')
                    ->label('Your Custom Name'),
                TextInput::make('year')
                    ->label('Calendar Year'),
                TextInput::make('season')
                    ->label('Season / Cycle'),
                TextInput::make('gregorian_reference')
                    ->label('Gregorian Range'),
                TextInput::make('deities')
                    ->label('Deity/Deities'),
                TextInput::make('meaning')
                    ->label('Spiritual Meaning'),
                Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }
}
