<?php

namespace App\Filament\Resources\Incantations;

use App\Filament\Resources\Incantations\Pages\CreateIncantation;
use App\Filament\Resources\Incantations\Pages\EditIncantation;
use App\Filament\Resources\Incantations\Pages\ListIncantations;
use App\Filament\Resources\Incantations\Schemas\IncantationForm;
use App\Filament\Resources\Incantations\Tables\IncantationsTable;
use App\Models\Incantation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use App\Traits\HasModuleAccess;

class IncantationResource extends Resource
{
    use HasModuleAccess;

    public static function getPermissionName(): ?string
    {
        return 'access_spiritual_practices';
    }

    protected static ?string $model = Incantation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static UnitEnum|string|null $navigationGroup = 'Spiritual Practices';

    public static function form(Schema $schema): Schema
    {
        return IncantationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IncantationsTable::configure($table);
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
            'index' => ListIncantations::route('/'),
            'create' => CreateIncantation::route('/create'),
            'edit' => EditIncantation::route('/{record}/edit'),
        ];
    }
}
