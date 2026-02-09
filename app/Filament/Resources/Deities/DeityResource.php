<?php

namespace App\Filament\Resources\Deities;

use App\Filament\Resources\Deities\Pages\CreateDeity;
use App\Filament\Resources\Deities\Pages\EditDeity;
use App\Filament\Resources\Deities\Pages\ListDeities;
use App\Filament\Resources\Deities\Schemas\DeityForm;
use App\Filament\Resources\Deities\Tables\DeitiesTable;
use App\Models\Deity;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DeityResource extends Resource
{
    protected static ?string $model = Deity::class;

    protected static ?string $navigationLabel = 'The Gods';

    protected static ?string $pluralLabel = 'The Gods';

    protected static ?string $modelLabel = 'Deity';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static UnitEnum|string|null $navigationGroup = 'The Gods';

    public static function form(Schema $schema): Schema
    {
        return DeityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeitiesTable::configure($table);
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
            'index' => ListDeities::route('/'),
            'create' => CreateDeity::route('/create'),
            'edit' => EditDeity::route('/{record}/edit'),
        ];
    }
}
