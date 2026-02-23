<?php

namespace App\Filament\Resources\Teachings;

use App\Filament\Resources\Teachings\Pages\CreateTeaching;
use App\Filament\Resources\Teachings\Pages\EditTeaching;
use App\Filament\Resources\Teachings\Pages\ListTeachings;
use App\Filament\Resources\Teachings\Schemas\TeachingForm;
use App\Filament\Resources\Teachings\Tables\TeachingsTable;
use App\Models\Teaching;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TeachingResource extends Resource
{
    protected static ?string $model = Teaching::class;

    protected static UnitEnum|string|null $navigationGroup = 'Community';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    public static function form(Schema $schema): Schema
    {
        return TeachingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TeachingsTable::configure($table);
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
            'index' => ListTeachings::route('/'),
            'create' => CreateTeaching::route('/create'),
            'edit' => EditTeaching::route('/{record}/edit'),
        ];
    }
}
