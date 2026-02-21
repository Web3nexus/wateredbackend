<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Section::make('Order Details')->schema([
                    \Filament\Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255),
                    \Filament\Forms\Components\Select::make('status')
                        ->options([
                            'open' => 'Open',
                            'closed' => 'Closed',
                            'invite_only' => 'Invite Only',
                        ])
                        ->required()
                        ->default('open'),
                    \Filament\Forms\Components\Textarea::make('description')
                        ->maxLength(65535)
                        ->columnSpanFull(),
                    \Filament\Forms\Components\SpatieMediaLibraryFileUpload::make('image')
                        ->collection('order_images')
                        ->image()
                        ->columnSpanFull(),
                ])->columns(2),

                \Filament\Forms\Components\Section::make('Call to Action')->schema([
                    \Filament\Forms\Components\Select::make('action_type')
                        ->options([
                            'external_link' => 'External Link',
                            'internal_route' => 'Internal Application',
                            'application_form' => 'Application Form',
                        ])
                        ->required()
                        ->default('application_form'),
                    \Filament\Forms\Components\TextInput::make('cta_text')
                        ->required()
                        ->default('Apply Now')
                        ->maxLength(255),
                    \Filament\Forms\Components\TextInput::make('cta_link')
                        ->maxLength(255)
                        ->url(fn(callable $get) => $get('action_type') === 'external_link')
                        ->helperText('Required if Action Type is External Link'),
                ])->columns(3),

                \Filament\Forms\Components\Section::make('Settings')->schema([
                    \Filament\Forms\Components\TextInput::make('order_level')
                        ->required()
                        ->numeric()
                        ->default(1)
                        ->helperText('Lower numbers appear first'),
                    \Filament\Forms\Components\Toggle::make('is_active')
                        ->required()
                        ->default(true),
                ])->columns(2),
            ]);
    }
}
