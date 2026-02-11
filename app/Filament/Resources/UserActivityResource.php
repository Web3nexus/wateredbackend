<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserActivityResource\Pages;
use App\Models\UserActivity;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class UserActivityResource extends Resource
{
    protected static ?string $model = UserActivity::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static UnitEnum|string|null $navigationGroup = 'Analytics';

    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('page')
                    ->label('Feature/Page')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'ritual' => 'success',
                        'incantation' => 'warning',
                        'sacred_book' => 'primary',
                        'deity' => 'info',
                        'audio' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('duration_seconds')
                    ->label('Time Spent')
                    ->formatStateUsing(
                        fn(int $state): string =>
                        $state >= 60
                        ? floor($state / 60) . 'm ' . ($state % 60) . 's'
                        : $state . 's'
                    )
                    ->sortable(),
                TextColumn::make('visited_at')
                    ->label('Visited At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('page')
                    ->options([
                        'ritual' => 'Rituals',
                        'incantation' => 'Incantations',
                        'sacred_book' => 'Sacred Book',
                        'deity' => 'Deities',
                        'audio' => 'Audio',
                    ]),
                Filter::make('visited_at')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('visited_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('visited_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('visited_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserActivities::route('/'),
        ];
    }
}
