<?php

namespace App\Filament\Resources\CalendarDays\Schemas;

use Filament\Forms;
use Filament\Forms\Form;
use App\Models\CalendarDay;

class CalendarDayForm
{
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
}
