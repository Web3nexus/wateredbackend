<?php

namespace App\Filament\Resources\Traditions;

use App\Filament\Resources\Traditions\Pages\CreateTradition;
use App\Filament\Resources\Traditions\Pages\EditTradition;
use App\Filament\Resources\Traditions\Pages\ListTraditions;
use App\Filament\Resources\Traditions\Schemas\TraditionForm;
use App\Filament\Resources\Traditions\Tables\TraditionsTable;
use App\Models\Tradition;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TraditionResource extends Resource
{
    protected static ?string $model = Tradition::class;

    protected static ?string $navigationLabel = 'Spiritual Paths';

    protected static ?string $pluralLabel = 'Spiritual Paths';

    protected static ?string $modelLabel = 'Spiritual Path';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|UnitEnum|null $navigationGroup = 'Library';

    public static function form(Schema $schema): Schema
    {
        return TraditionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TraditionsTable::configure($table);
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
            'index' => ListTraditions::route('/'),
            'create' => CreateTradition::route('/create'),
            'edit' => EditTradition::route('/{record}/edit'),
        ];
    }
}
