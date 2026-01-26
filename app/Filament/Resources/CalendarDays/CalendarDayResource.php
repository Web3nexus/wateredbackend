<?php

namespace App\Filament\Resources\CalendarDays;

use App\Filament\Resources\CalendarDays\Pages\CreateCalendarDay;
use App\Filament\Resources\CalendarDays\Pages\EditCalendarDay;
use App\Filament\Resources\CalendarDays\Pages\ListCalendarDays;
use App\Models\CalendarDay;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CalendarDayResource extends Resource
{
    protected static ?string $model = CalendarDay::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Watered Calendar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Calendar Day Information')
                    ->schema([
                        Forms\Components\Select::make('month')
                            ->label('Month')
                            ->options(CalendarDay::getMonthNames())
                            ->required()
                            ->reactive()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('day')
                            ->label('Day')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(30)
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('day_name')
                            ->label('Day Name')
                            ->maxLength(255)
                            ->placeholder('e.g., Festival of Thoth')
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Details')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TagsInput::make('associated_deities')
                            ->label('Associated Deities')
                            ->placeholder('Add deity names')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('celebration_type')
                            ->label('Celebration Type')
                            ->options([
                                'Festival' => 'Festival',
                                'Sacred Day' => 'Sacred Day',
                                'Offering Day' => 'Offering Day',
                                'Feast Day' => 'Feast Day',
                            ])
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('is_special_day')
                            ->label('Mark as Special Day')
                            ->default(false)
                            ->columnSpan(1),

                        Forms\Components\ColorPicker::make('color')
                            ->label('Color (for UI)')
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('month')
                    ->label('Month')
                    ->formatStateUsing(fn($state) => CalendarDay::getMonthNames()[$state] ?? $state)
                    ->sortable(),

                Tables\Columns\TextColumn::make('day')
                    ->label('Day')
                    ->sortable(),

                Tables\Columns\TextColumn::make('day_name')
                    ->label('Day Name')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\IconColumn::make('is_special_day')
                    ->label('Special')
                    ->boolean(),

                Tables\Columns\ColorColumn::make('color')
                    ->label('Color'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_special_day')
                    ->label('Special Days Only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('month')
            ->defaultSort('day');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCalendarDays::route('/'),
            'create' => CreateCalendarDay::route('/create'),
            'edit' => EditCalendarDay::route('/{record}/edit'),
        ];
    }
}
