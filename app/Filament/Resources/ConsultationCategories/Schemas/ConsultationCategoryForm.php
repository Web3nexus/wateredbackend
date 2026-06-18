<?php

namespace App\Filament\Resources\ConsultationCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
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
                    ->helperText('If enabled, appointments in this category can be booked without payment.')
                    ->live()
                    ->afterStateUpdated(function ($set, $state) {
                        if ($state) {
                            $set('price', null);
                        }
                    }),
                TextInput::make('price')
                    ->label('Price (₦)')
                    ->numeric()
                    ->prefix('₦')
                    ->step(0.01)
                    ->placeholder('e.g. 50000.00')
                    ->visible(fn($get) => !$get('is_free')),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
