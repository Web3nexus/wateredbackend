<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FormFieldsRelationManager extends RelationManager
{
    protected static string $relationship = 'formFields';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('label')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\Select::make('field_type')
                    ->options([
                        'text' => 'Short Text',
                        'textarea' => 'Long Text',
                        'email' => 'Email',
                        'phone' => 'Phone',
                        'select' => 'Dropdown Selection',
                        'checkbox' => 'Checkbox',
                    ])
                    ->required()
                    ->live(),
                \Filament\Forms\Components\TextInput::make('placeholder')
                    ->maxLength(255),
                \Filament\Forms\Components\Repeater::make('options')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('label')->required(),
                        \Filament\Forms\Components\TextInput::make('value')->required(),
                    ])
                    ->visible(fn(\Filament\Forms\Get $get) => $get('field_type') === 'select')
                    ->columnSpanFull(),
                \Filament\Forms\Components\Toggle::make('is_required')
                    ->default(false),
                \Filament\Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
                \Filament\Forms\Components\Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('label')
                    ->searchable(),
                TextColumn::make('field_type'),
                \Filament\Tables\Columns\ToggleColumn::make('is_required'),
                TextColumn::make('sort_order')
                    ->sortable(),
                \Filament\Tables\Columns\ToggleColumn::make('is_active'),
            ])
            ->defaultSort('sort_order')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
