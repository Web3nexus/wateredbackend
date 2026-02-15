<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->helperText('Default price in cents'),
                TextInput::make('price_ngn')
                    ->numeric()
                    ->prefix('₦')
                    ->label('Price in Naira'),
                TextInput::make('price_usd')
                    ->numeric()
                    ->prefix('$')
                    ->label('Price in USD'),
                FileUpload::make('image_url')
                    ->image()
                    ->disk('public')
                    ->directory('products'),
                TextInput::make('audio_sample_url')
                    ->url(),
                Toggle::make('is_digital')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
