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

    protected static UnitEnum|string|null $navigationGroup = 'Watered Calendar';

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

                        Placeholder::make('weekday')
                            ->label('Day of Week')
                            ->content(function ($record) {
                                if (!$record)
                                    return '-';
                                try {
                                    $year = $record->month?->year ?? date('Y');
                                    $dateStr = $record->gregorian_day . ' ' . $year;
                                    return \Carbon\Carbon::parse($dateStr)->format('l');
                                } catch (\Exception $e) {
                                    return 'Unknown';
                                }
                            }),

                        TextInput::make('custom_day_name')
                            ->label('Specific Spiritual Name')
                            ->placeholder(function ($record) {
                                if (!$record)
                                    return 'e.g. Day of Awakening';
                                try {
                                    $year = $record->month?->year ?? date('Y');
                                    $dateStr = $record->gregorian_day . ' ' . $year;
                                    $date = \Carbon\Carbon::parse($dateStr);
                                    if ($date->isThursday())
                                        return 'Ancestral Day (Automatic)';
                                    if ($date->isSaturday())
                                        return 'Day of the Gods (Automatic)';
                                } catch (\Exception $e) {
                                }
                                return 'Regular Day (Automatic)';
                            })
                            ->helperText('Leave empty to use the automatic designation based on the day of the week.'),

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
                Tables\Columns\TextColumn::make('weekday')
                    ->label('Day of Week')
                    ->getStateUsing(function ($record) {
                        try {
                            $year = $record->month?->year ?? date('Y');
                            $dateStr = $record->gregorian_day . ' ' . $year;
                            return \Carbon\Carbon::parse($dateStr)->format('l');
                        } catch (\Exception $e) {
                            return 'Unknown';
                        }
                    }),
                Tables\Columns\TextColumn::make('custom_day_name')
                    ->label('Spiritual Designation')
                    ->getStateUsing(function ($record) {
                        if ($record->custom_day_name) {
                            return $record->custom_day_name;
                        }

                        try {
                            $year = $record->month?->year ?? date('Y');
                            $dateStr = $record->gregorian_day . ' ' . $year;
                            $date = \Carbon\Carbon::parse($dateStr);

                            if ($date->isThursday()) {
                                return 'Ancestral Day';
                            }

                            if ($date->isSaturday()) {
                                return 'Day of the Gods';
                            }
                        } catch (\Exception $e) {
                            // Fallback
                        }

                        return 'Regular Day';
                    })
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
