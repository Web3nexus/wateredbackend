<?php

namespace App\Filament\Resources\AdminUsers\Schemas;

use Filament\Schemas\Schema;

class AdminUserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('name')
                    ->required(),
                \Filament\Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                \Filament\Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $context): bool => $context === 'create'),

                \Filament\Forms\Components\Section::make('Feature Access')
                    ->description('Select the modules this staff member can access.')
                    ->schema([
                        \Filament\Forms\Components\CheckboxList::make('permissions')
                            ->relationship('permissions', 'name')
                            ->options([
                                'access_library' => 'Spiritual Library',
                                'access_audio' => 'Audio & Sounds',
                                'access_shop' => 'Shop & Products',
                                'access_rituals' => 'Rituals & Teachings',
                                'access_events' => 'Events & News',
                                'access_appointments' => 'Appointments',
                                'access_users' => 'User Management',
                                'access_financials' => 'Financials & Revenue',
                                'access_settings' => 'System Settings',
                            ])
                            ->columns(2)
                            ->required(),
                    ]),
            ]);
    }
}
