<?php

namespace App\Filament\Resources\CalendarDays;

use App\Filament\Resources\CalendarDays\Pages;
use App\Models\CalendarDay;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Schemas\Components\Section;
use Filament\Actions;
use BackedEnum;
use UnitEnum;

class CalendarDayResource extends Resource
{
    protected static ?string $model = CalendarDay::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sun';

    protected static ?string $navigationLabel = 'Days & Celebrations';

    protected static string|UnitEnum|null $navigationGroup = 'Watered Calendar';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Day Details')
                    ->schema([
                        Placeholder::make('month_display')
                            ->label('Month')
                            ->content(fn($record) => $record?->month?->display_name),

                        Placeholder::make('day_number')
                            ->label('Day Number')
                            ->content(fn($record) => $record?->day_number),

                        Placeholder::make('gregorian_day')
                            ->label('Gregorian Reference')
                            ->content(fn($record) => $record?->gregorian_day),

                        TextInput::make('custom_day_name')
                            ->label('Special Name for this Day')
                            ->placeholder('e.g. Day of the Great Awakening'),

                        TextInput::make('celebration_type')
                            ->label('Celebration Type')
                            ->placeholder('e.g. Festival, Sacred Day'),

                        Toggle::make('is_sacred')
                            ->label('Is this a Sacred Day?')
                            ->default(false),
                    ])->columns(2),

                Section::make('Sacred Content')
                    ->schema([
                        TagsInput::make('associated_deities')
                            ->label('Associated Gods/Deities')
                            ->placeholder('Add deity names'),

                        MarkdownEditor::make('content')
                            ->label('Description/Teachings for this day')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('month.standard_name')
                    ->label('Month')
                    ->sortable(),
                Tables\Columns\TextColumn::make('day_number')
                    ->label('Day')
                    ->sortable(),
                Tables\Columns\TextColumn::make('gregorian_day')
                    ->label('Gregorian Ref')
                    ->color('gray'),
                Tables\Columns\TextColumn::make('custom_day_name')
                    ->label('Name')
                    ->placeholder('Regular Day')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_sacred')
                    ->label('Sacred')
                    ->boolean(),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->groups([
                Tables\Grouping\Group::make('month.standard_name')
                    ->label('Month')
                    ->collapsible(),
            ])
            ->defaultGroup('month.standard_name')
            ->defaultSort('day_number');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCalendarDays::route('/'),
            'edit' => Pages\EditCalendarDay::route('/{record}/edit'),
        ];
    }
}
