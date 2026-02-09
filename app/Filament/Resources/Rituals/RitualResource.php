<?php

namespace App\Filament\Resources\Rituals;

use App\Filament\Resources\Rituals\Pages\CreateRitual;
use App\Filament\Resources\Rituals\Pages\EditRitual;
use App\Filament\Resources\Rituals\Pages\ListRituals;
use App\Filament\Resources\Rituals\Schemas\RitualForm;
use App\Filament\Resources\Rituals\Tables\RitualsTable;
use App\Models\Ritual;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class RitualResource extends Resource
{
    protected static ?string $model = Ritual::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFire;

    protected static UnitEnum|string|null $navigationGroup = 'Spiritual Practices';

    public static function form(Schema $schema): Schema
    {
        return RitualForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RitualsTable::configure($table);
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
            'index' => ListRituals::route('/'),
            'create' => CreateRitual::route('/create'),
            'edit' => EditRitual::route('/{record}/edit'),
        ];
    }
}
