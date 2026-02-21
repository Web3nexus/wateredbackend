<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Models\OrderFormField;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class FormFieldsRelationManager extends RelationManager
{
    protected static string $relationship = 'formFields';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->required()
                    ->maxLength(255),
                Select::make('field_type')
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
                TextInput::make('placeholder')
                    ->maxLength(255),
                Repeater::make('options')
                    ->schema([
                        TextInput::make('label')->required(),
                        TextInput::make('value')->required(),
                    ])
                    ->visible(fn(Get $get) => $get('field_type') === 'select')
                    ->columnSpanFull(),
                Toggle::make('is_required')
                    ->default(false),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
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
                ToggleColumn::make('is_required'),
                TextColumn::make('sort_order')
                    ->sortable(),
                ToggleColumn::make('is_active'),
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
