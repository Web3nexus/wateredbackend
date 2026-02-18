<?php

namespace App\Filament\Resources\TextCollections;

use App\Filament\Resources\TextCollections\RelationManagers;

use App\Filament\Resources\TextCollections\Pages\CreateTextCollection;
use App\Filament\Resources\TextCollections\Pages\EditTextCollection;
use App\Filament\Resources\TextCollections\Pages\ListTextCollections;
use App\Filament\Resources\TextCollections\Schemas\TextCollectionForm;
use App\Filament\Resources\TextCollections\Tables\TextCollectionsTable;
use App\Models\TextCollection;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use BackedEnum;
use UnitEnum;

class TextCollectionResource extends Resource
{
    protected static ?string $model = TextCollection::class;

    protected static ?string $navigationLabel = 'Nima Sedani';

    protected static ?string $pluralLabel = 'Books';

    protected static ?string $modelLabel = 'Book';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static UnitEnum|string|null $navigationGroup = 'Nima Sedani';

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
        return parent::getEloquentQuery()->whereHas('tradition', function ($query) {
            $query->where('slug', 'nima-sedani');
        })->orWhereNull('tradition_id');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTextCollections::route('/'),
            'create' => CreateTextCollection::route('/create'),
            'edit' => EditTextCollection::route('/{record}/edit'),
        ];
    }
}
