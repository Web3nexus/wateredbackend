<?php

namespace App\Filament\Resources\Appointments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('appointment_code')
                    ->disabled()
                    ->dehydrated(false),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->placeholder('Select User (Optional for Guests)'),
                TextInput::make('full_name')
                    ->placeholder('Guest Full Name'),
                TextInput::make('email')
                    ->email()
                    ->placeholder('Guest Email'),
                TextInput::make('phone')
                    ->tel()
                    ->placeholder('Guest Phone'),
                Select::make('consultation_type_id')
                    ->relationship('consultationType', 'name')
                    ->required(),
                TextInput::make('service_type')
                    ->placeholder('Custom Service Type'),
                DateTimePicker::make('start_time')
                    ->required(),
                TextInput::make('appointment_status')
                    ->required()
                    ->default('pending'),
                TextInput::make('amount')
                    ->numeric()
                    ->prefix('₦')
                    ->default(0),
                TextInput::make('payment_status')
                    ->default('pending'),
                TextInput::make('payment_reference'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
