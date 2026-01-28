<?php

namespace App\Filament\Resources\CalendarMonths;

use App\Filament\Resources\CalendarMonths\Pages;
use App\Models\CalendarMonth;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Schemas\Components\Section;
use Filament\Actions;
use BackedEnum;
use UnitEnum;

class CalendarMonthResource extends Resource
{
    protected static ?string $model = CalendarMonth::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Months (Custom Naming)';

    protected static UnitEnum|string|null $navigationGroup = 'Watered Calendar';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Month Branding')
                    ->schema([
                        Placeholder::make('number')
                            ->label('Month Number')
                            ->content(fn($record) => $record?->number),

                        Placeholder::make('standard_name')
                            ->label('Standard Kemetic Name')
                            ->content(fn($record) => $record?->standard_name),

                        Placeholder::make('season')
                            ->label('Egyptian Season')
                            ->content(fn($record) => $record?->season),

                        Placeholder::make('gregorian_reference')
                            ->label('Gregorian Range (Estimated)')
                            ->content(fn($record) => $record?->gregorian_reference),

                        TextInput::make('custom_name')
                            ->label('Your Custom Name')
                            ->placeholder('e.g. Month of Wisdom')
                            ->columnSpan(1),

                        TextInput::make('year')
                            ->label('Calendar Year')
                            ->placeholder('e.g. 6262')
                            ->columnSpan(1),

                        Textarea::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('standard_name')
                    ->label('Standard Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('custom_name')
                    ->label('Custom Name')
                    ->placeholder('None set')
                    ->searchable(),
                Tables\Columns\TextColumn::make('season')
                    ->label('Season')
                    ->badge(),
                Tables\Columns\TextColumn::make('gregorian_reference')
                    ->label('Gregorian Ref'),
                Tables\Columns\TextColumn::make('year')
                    ->label('Year')
                    ->searchable(),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->defaultSort('number');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCalendarMonths::route('/'),
            'edit' => Pages\EditCalendarMonth::route('/{record}/edit'),
        ];
    }
}
