<?php

namespace App\Filament\Resources\ConsultationTypes;

use App\Filament\Resources\ConsultationTypes\Pages\CreateConsultationType;
use App\Filament\Resources\ConsultationTypes\Pages\EditConsultationType;
use App\Filament\Resources\ConsultationTypes\Pages\ListConsultationTypes;
use App\Filament\Resources\ConsultationTypes\Schemas\ConsultationTypeForm;
use App\Filament\Resources\ConsultationTypes\Tables\ConsultationTypesTable;
use App\Models\ConsultationType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ConsultationTypeResource extends Resource
{
    protected static ?string $model = ConsultationType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static UnitEnum|string|null $navigationGroup = 'Consultation';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ConsultationTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConsultationTypesTable::configure($table);
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
            'index' => ListConsultationTypes::route('/'),
            'create' => CreateConsultationType::route('/create'),
            'edit' => EditConsultationType::route('/{record}/edit'),
        ];
    }
}
