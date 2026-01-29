<?php

namespace App\Filament\Resources\CalendarDays\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Schema;

class CalendarDayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('calendar_month_id')
                    ->relationship('month', 'custom_name')
                    ->required(),
                TextInput::make('day_number')
                    ->numeric()
                    ->required(),
                TextInput::make('custom_day_name')
                    ->label('Sacred Day Name'),
                Textarea::make('content')
                    ->label('Spiritual Significance')
                    ->rows(3)
                    ->columnSpanFull(),
                TagsInput::make('associated_deities'),
                TagsInput::make('activities')
                    ->label('Required Activities')
                    ->placeholder('Add activity'),
                TagsInput::make('restrictions')
                    ->label('Restrictions/Taboos')
                    ->placeholder('Add restriction'),
                Toggle::make('is_sacred')
                    ->label('Is Sacred Day')
                    ->default(false),
                ColorPicker::make('ui_color'),
            ]);
    }
}
