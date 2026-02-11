<?php

namespace App\Filament\Resources\Holidays\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class HolidayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->placeholder('e.g. Festival of Waters')
                    ->helperText('The display name of the holy day.'),
                TextInput::make('theme')
                    ->placeholder('e.g. Spiritual Renewal'),
                DatePicker::make('date')
                    ->required()
                    ->native(false)
                    ->displayFormat('M d, Y')
                    ->helperText('The date this holiday occurs on.'),
                Textarea::make('description')
                    ->helperText('Briefly describe the significance of this day.')
                    ->columnSpanFull(),
            ]);
    }
}
