<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('consultation_type_id')
                    ->relationship('consultationType', 'name')
                    ->required(),
                DateTimePicker::make('scheduled_at')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
