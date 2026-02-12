<?php

namespace App\Filament\Resources\SpiritualPathBooks;

use App\Filament\Resources\SpiritualPathBooks\Pages;
use App\Filament\Resources\TextCollections\Schemas\TextCollectionForm;
use App\Filament\Resources\TextCollections\Tables\TextCollectionsTable;
use App\Filament\Resources\TextCollections\RelationManagers;
use App\Models\TextCollection;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use BackedEnum;
use UnitEnum;

class SpiritualPathBookResource extends Resource
{
    protected static ?string $model = TextCollection::class;

    protected static ?string $navigationLabel = 'Spiritual Path Books';

    protected static ?string $pluralLabel = 'Spiritual Path Books';

    protected static ?string $modelLabel = 'Spiritual Path Book';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static UnitEnum|string|null $navigationGroup = 'Spiritual Paths';

    public static function form(Schema $schema): Schema
    {
        return TextCollectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TextCollectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ChaptersRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->whereNotNull('tradition_id');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpiritualPathBooks::route('/'),
            'create' => Pages\CreateSpiritualPathBook::route('/create'),
            'edit' => Pages\EditSpiritualPathBook::route('/{record}/edit'),
        ];
    }
}
