<?php

namespace App\Filament\Resources\Entries\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;

class EntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('chapter.collection.name')
                    ->label('Book')
                    ->sortable(),
                TextColumn::make('chapter.name')
                    ->label('Chapter')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('number')
                    ->label('Verse #')
                    ->sortable(),
                TextColumn::make('text')
                    ->limit(100)
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->filters([
                //
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
