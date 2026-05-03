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
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                    ->disk('public')
                    ->directory('products')
                    ->formatStateUsing(function ($state) {
                        if (is_string($state) && str_starts_with($state, 'http')) {
                            return str_replace(\Illuminate\Support\Facades\Storage::url(''), '', $state);
                        }
                        return $state;
                    }),
                TextInput::make('audio_sample_url')
                    ->url(),
                Toggle::make('is_digital')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
