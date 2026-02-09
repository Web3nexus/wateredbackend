<?php

namespace App\Filament\Resources\LandingPageFeatures;

use App\Filament\Resources\LandingPageFeatures\Pages\CreateLandingPageFeature;
use App\Filament\Resources\LandingPageFeatures\Pages\EditLandingPageFeature;
use App\Filament\Resources\LandingPageFeatures\Pages\ListLandingPageFeatures;
use App\Filament\Resources\LandingPageFeatures\Schemas\LandingPageFeatureForm;
use App\Filament\Resources\LandingPageFeatures\Tables\LandingPageFeaturesTable;
use App\Models\LandingPageFeature;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use UnitEnum;

class LandingPageFeatureResource extends Resource
{
    protected static ?string $model = LandingPageFeature::class;

    protected static UnitEnum|string|null $navigationGroup = 'Settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    public static function form(Schema $schema): Schema
    {
        return LandingPageFeatureForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LandingPageFeaturesTable::configure($table);
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
            'index' => ListLandingPageFeatures::route('/'),
            'create' => CreateLandingPageFeature::route('/create'),
            'edit' => EditLandingPageFeature::route('/{record}/edit'),
        ];
    }
}
