<?php

namespace App\Filament\Resources\DailyWisdoms;

use App\Filament\Resources\DailyWisdoms\Pages\CreateDailyWisdom;
use App\Filament\Resources\DailyWisdoms\Pages\EditDailyWisdom;
use App\Filament\Resources\DailyWisdoms\Pages\ListDailyWisdoms;
use App\Filament\Resources\DailyWisdoms\Schemas\DailyWisdomForm;
use App\Filament\Resources\DailyWisdoms\Tables\DailyWisdomsTable;
use App\Models\DailyWisdom;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DailyWisdomResource extends Resource
{
    protected static ?string $model = DailyWisdom::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static UnitEnum|string|null $navigationGroup = 'Wisdom';

    public static function form(Schema $schema): Schema
    {
        return DailyWisdomForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DailyWisdomsTable::configure($table);
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
            'index' => ListDailyWisdoms::route('/'),
            'create' => CreateDailyWisdom::route('/create'),
            'edit' => EditDailyWisdom::route('/{record}/edit'),
        ];
    }
}
