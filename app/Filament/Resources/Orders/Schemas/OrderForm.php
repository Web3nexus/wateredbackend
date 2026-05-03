<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Details')->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255),
                    Select::make('status')
                        ->options([
                            'open' => 'Open',
                            'closed' => 'Closed',
                            'invite_only' => 'Invite Only',
                        ])
                        ->required()
                        ->default('open'),
                    Textarea::make('description')
                        ->maxLength(65535)
                        ->columnSpanFull(),
                    FileUpload::make('image_url')
                        ->label('Image')
                        ->image()
                        ->disk('public')
                        ->directory('orders')
                        ->columnSpanFull(),
                ])->columns(2),

                Section::make('Call to Action')->schema([
                    Select::make('action_type')
                        ->options([
                            'external_link' => 'External Link',
                            'internal_route' => 'Internal Application',
                            'application_form' => 'Application Form',
                        ])
                        ->required()
                        ->default('application_form'),
                    TextInput::make('cta_text')
                        ->required()
                        ->default('Apply Now')
                        ->maxLength(255),
                    TextInput::make('cta_link')
                        ->maxLength(255)
                        ->helperText('Required if Action Type is External Link'),
                ])->columns(3),

                Section::make('Settings')->schema([
                    TextInput::make('order_level')
                        ->required()
                        ->numeric()
                        ->default(1)
                        ->helperText('Lower numbers appear first'),
                    Toggle::make('is_active')
                        ->required()
                        ->default(true),
                ])->columns(2),
            ]);
    }
}
