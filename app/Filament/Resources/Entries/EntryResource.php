<?php

namespace App\Filament\Resources\Entries;

use App\Filament\Resources\Entries\Pages\CreateEntry;
use App\Filament\Resources\Entries\Pages\EditEntry;
use App\Filament\Resources\Entries\Pages\ListEntries;
use App\Filament\Resources\Entries\Schemas\EntryForm;
use App\Filament\Resources\Entries\Tables\EntriesTable;
use App\Models\Entry;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use BackedEnum;
use UnitEnum;

class EntryResource extends Resource
{
    protected static ?string $model = Entry::class;

    protected static ?string $navigationLabel = 'Verses';

    protected static ?string $pluralLabel = 'Verses';

    protected static ?string $modelLabel = 'Verse';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static UnitEnum|string|null $navigationGroup = 'Sacred Texts (Nimasedani)';

    protected static ?int $navigationSort = 4;

    public static function shouldRegisterNavigation(): bool
    {
        return false; // Hide from navigation - verses are managed via Chapters
    }

    public static function form(Schema $schema): Schema
    {
        return EntryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EntriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEntries::route('/'),
            'create' => CreateEntry::route('/create'),
            'edit' => EditEntry::route('/{record}/edit'),
        ];
    }
}
