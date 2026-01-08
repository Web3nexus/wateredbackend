<?php

namespace App\Filament\Resources\Audios;

use App\Filament\Resources\Audios\Pages\CreateAudio;
use App\Filament\Resources\Audios\Pages\EditAudio;
use App\Filament\Resources\Audios\Pages\ListAudios;
use App\Filament\Resources\Audios\Schemas\AudioForm;
use App\Filament\Resources\Audios\Tables\AudiosTable;
use App\Models\Audio;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use BackedEnum;
use UnitEnum;

class AudioResource extends Resource
{
    protected static ?string $model = Audio::class;

    protected static ?string $navigationLabel = 'Audio Teachings';

    protected static ?string $pluralLabel = 'Audio Teachings';

    protected static ?string $modelLabel = 'Audio Teaching';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-microphone';

    protected static string|UnitEnum|null $navigationGroup = 'Teachings';

    public static function form(Schema $schema): Schema
    {
        return AudioForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AudiosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAudios::route('/'),
            'create' => CreateAudio::route('/create'),
            'edit' => EditAudio::route('/{record}/edit'),
        ];
    }
}
