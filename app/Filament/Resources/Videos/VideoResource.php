<?php

namespace App\Filament\Resources\Videos;

use App\Filament\Resources\Videos\Pages\CreateVideo;
use App\Filament\Resources\Videos\Pages\EditVideo;
use App\Filament\Resources\Videos\Pages\ListVideos;
use App\Filament\Resources\Videos\Schemas\VideoForm;
use App\Filament\Resources\Videos\Tables\VideosTable;
use App\Models\Video;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use BackedEnum;
use UnitEnum;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;

    protected static ?string $navigationLabel = 'Short Video Reels';

    protected static ?string $pluralLabel = 'Short Video Reels';

    protected static ?string $modelLabel = 'Video Reel';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-video-camera';

    protected static string|UnitEnum|null $navigationGroup = 'Teachings';

    public static function form(Schema $schema): Schema
    {
        return VideoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VideosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVideos::route('/'),
            'create' => CreateVideo::route('/create'),
            'edit' => EditVideo::route('/{record}/edit'),
        ];
    }
}
