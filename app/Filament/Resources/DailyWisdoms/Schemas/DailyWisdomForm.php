<?php

namespace App\Filament\Resources\DailyWisdoms\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DailyWisdomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('quote')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('author'),
                FileUpload::make('background_image_url')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                    ->disk('public')
                    ->directory('daily-wisdoms'),
                DatePicker::make('publish_date')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
