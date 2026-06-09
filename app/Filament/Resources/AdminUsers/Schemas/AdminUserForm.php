<?php

namespace App\Filament\Resources\AdminUsers\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Tabs;

class AdminUserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                    ]),
                \Filament\Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $context): bool => $context === 'create'),

                Section::make('Role')
                    ->description('Assign a role to this staff member. Roles grant preset permission sets.')
                    ->schema([
                        Select::make('role')
                            ->label('Admin Role')
                            ->relationship('roles', 'name', fn($query) => $query->where('name', '!=', 'Developer'))
                            ->multiple()
                            ->preload()
                            ->native(false),
                    ]),

                Section::make('Feature Access by Sidebar Group')
                    ->description('Select which sidebar groups and modules this staff member can access.')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->label('Sidebar Group Access')
                            ->helperText('Select which sidebar sections this admin can see. Organized by navigation group.')
                            ->relationship('permissions', 'name', fn($query) => $query->where('guard_name', 'admin'))
                            ->columns(2)
                            ->gridDirection('row'),
                    ]),
            ]);
    }
}
