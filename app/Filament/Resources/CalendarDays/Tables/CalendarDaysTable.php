<?php

namespace App\Filament\Resources\CalendarDays\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CalendarDaysTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('month')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('day')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('month_name')
                    ->searchable(),
                TextColumn::make('day_name')
                    ->searchable(),
                TextColumn::make('celebration_type')
                    ->searchable(),
                IconColumn::make('is_special_day')
                    ->boolean(),
                TextColumn::make('color')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
