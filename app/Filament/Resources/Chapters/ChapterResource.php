<?php

namespace App\Filament\Resources\Chapters;

use App\Filament\Resources\Chapters\Pages\CreateChapter;
use App\Filament\Resources\Chapters\Pages\EditChapter;
use App\Filament\Resources\Chapters\Pages\ListChapters;
use App\Filament\Resources\Chapters\Schemas\ChapterForm;
use App\Filament\Resources\Chapters\Tables\ChaptersTable;
use App\Filament\Resources\Chapters\RelationManagers\EntriesRelationManager;
use App\Models\Chapter;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use BackedEnum;
use UnitEnum;

class ChapterResource extends Resource
{
    protected static ?string $model = Chapter::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static UnitEnum|string|null $navigationGroup = 'Sacred Texts';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return ChapterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChaptersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            EntriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChapters::route('/'),
            'create' => CreateChapter::route('/create'),
            'edit' => EditChapter::route('/{record}/edit'),
        ];
    }
}
