<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                TextInput::make('slug')
                    ->required()
                    ->unique('events', 'slug', ignoreRecord: true),
                \Filament\Forms\Components\Select::make('tradition_id')
                    ->relationship('tradition', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('category'),
                Textarea::make('description')
                    ->columnSpanFull(),
                DateTimePicker::make('start_time')
                    ->required(),
                DateTimePicker::make('end_time'),
                \Filament\Forms\Components\DatePicker::make('event_date'),
                \Filament\Forms\Components\TimePicker::make('event_time'),
                TextInput::make('location'),
                FileUpload::make('banner_image')
                    ->image()
                    ->directory('events'),
                FileUpload::make('image_url')
                    ->label('Legacy Image URL (Optional)')
                    ->image(),
                Toggle::make('is_paid')
                    ->required(),
                TextInput::make('price')
                    ->numeric()
                    ->prefix('$'),
            ]);
    }
}
