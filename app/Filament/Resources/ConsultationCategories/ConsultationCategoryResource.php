<?php

namespace App\Filament\Resources\ConsultationCategories;

use App\Filament\Resources\ConsultationCategories\Pages\CreateConsultationCategory;
use App\Filament\Resources\ConsultationCategories\Pages\EditConsultationCategory;
use App\Filament\Resources\ConsultationCategories\Pages\ListConsultationCategories;
use App\Filament\Resources\ConsultationCategories\RelationManagers\TypesRelationManager;
use App\Filament\Resources\ConsultationCategories\Schemas\ConsultationCategoryForm;
use App\Filament\Resources\ConsultationCategories\Tables\ConsultationCategoriesTable;
use App\Models\ConsultationCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use App\Traits\HasModuleAccess;

class ConsultationCategoryResource extends Resource
{
    use HasModuleAccess;

    public static function getPermissionName(): ?string
    {
        return 'access_appointments';
    }

    protected static ?string $model = ConsultationCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static UnitEnum|string|null $navigationGroup = 'Consultation';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ConsultationCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConsultationCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TypesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConsultationCategories::route('/'),
            'create' => CreateConsultationCategory::route('/create'),
            'edit' => EditConsultationCategory::route('/{record}/edit'),
        ];
    }
}
