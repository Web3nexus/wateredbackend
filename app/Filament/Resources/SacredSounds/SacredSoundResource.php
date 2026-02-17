<?php

namespace App\Filament\Resources\SacredSounds;

use App\Filament\Resources\SacredSounds\Pages\CreateSacredSound;
use App\Filament\Resources\SacredSounds\Pages\EditSacredSound;
use App\Filament\Resources\SacredSounds\Pages\ListSacredSounds;
use App\Filament\Resources\SacredSounds\Schemas\SacredSoundForm;
use App\Filament\Resources\SacredSounds\Tables\SacredSoundsTable;
use App\Models\SacredSound;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SacredSoundResource extends Resource
{
    protected static ?string $model = SacredSound::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMusicalNote;
    protected static ?string $navigationGroup = 'Settings';

    public static function form(Schema $schema): Schema
    {
        return SacredSoundForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SacredSoundsTable::configure($table);
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
            'index' => ListSacredSounds::route('/'),
            'create' => CreateSacredSound::route('/create'),
            'edit' => EditSacredSound::route('/{record}/edit'),
        ];
    }
}
