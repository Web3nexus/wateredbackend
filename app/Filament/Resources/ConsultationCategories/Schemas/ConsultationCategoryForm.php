<?php

namespace App\Filament\Resources\ConsultationCategories\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ConsultationCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($set, $state) {
                        $set('slug', Str::slug($state));
                    }),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Toggle::make('is_free')
                    ->label('Free Category')
                    ->helperText('If enabled, appointments in this category can be booked without payment.'),
                Repeater::make('availability')
                    ->schema([
                        Select::make('days')
                            ->multiple()
                            ->options([
                                0 => 'Sunday',
                                1 => 'Monday',
                                2 => 'Tuesday',
                                3 => 'Wednesday',
                                4 => 'Thursday',
                                5 => 'Friday',
                                6 => 'Saturday',
                            ])
                            ->required(),
                        TimePicker::make('start')
                            ->required()
                            ->seconds(false),
                        TimePicker::make('end')
                            ->required()
                            ->seconds(false)
                            ->after('start'),
                    ])
                    ->columnSpanFull()
                    ->helperText('Set available day and time slots. At least one slot is required for bookings to be allowed.'),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
