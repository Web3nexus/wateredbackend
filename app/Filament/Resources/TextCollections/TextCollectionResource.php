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

    protected static ?string $navigationLabel = 'Books & Scriptures';

    protected static ?string $pluralLabel = 'Books & Scriptures';

    protected static ?string $modelLabel = 'Book/Scripture';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|UnitEnum|null $navigationGroup = 'Library';

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

    public static function getPages(): array
    {
        return [
            'index' => ListTextCollections::route('/'),
            'create' => CreateTextCollection::route('/create'),
            'edit' => EditTextCollection::route('/{record}/edit'),
        ];
    }
}
