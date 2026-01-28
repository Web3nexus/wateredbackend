<?php

namespace App\Filament\Resources\GlobalSettings;

use App\Filament\Resources\GlobalSettings\Pages\CreateGlobalSetting;
use App\Filament\Resources\GlobalSettings\Pages\EditGlobalSetting;
use App\Filament\Resources\GlobalSettings\Pages\ListGlobalSettings;
use App\Filament\Resources\GlobalSettings\Schemas\GlobalSettingForm;
use App\Filament\Resources\GlobalSettings\Tables\GlobalSettingsTable;
use App\Models\GlobalSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class GlobalSettingResource extends Resource
{
    protected static ?string $model = GlobalSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return GlobalSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GlobalSettingsTable::configure($table);
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
            'index' => ListGlobalSettings::route('/'),
            // 'create' => CreateGlobalSetting::route('/create'),
            'edit' => EditGlobalSetting::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
