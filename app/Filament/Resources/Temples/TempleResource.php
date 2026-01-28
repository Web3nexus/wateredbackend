<?php

namespace App\Filament\Resources\Temples;

use App\Filament\Resources\Temples\Pages\CreateTemple;
use App\Filament\Resources\Temples\Pages\EditTemple;
use App\Filament\Resources\Temples\Pages\ListTemples;
use App\Filament\Resources\Temples\Schemas\TempleForm;
use App\Filament\Resources\Temples\Tables\TemplesTable;
use App\Models\Temple;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TempleResource extends Resource
{
    protected static ?string $model = Temple::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static UnitEnum|string|null $navigationGroup = 'Discovery';

    public static function form(Schema $schema): Schema
    {
        return TempleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TemplesTable::configure($table);
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
            'index' => ListTemples::route('/'),
            'create' => CreateTemple::route('/create'),
            'edit' => EditTemple::route('/{record}/edit'),
        ];
    }
}
