<?php

namespace App\Filament\Resources\ShopOrders;

use App\Filament\Resources\ShopOrders\Pages\EditShopOrder;
use App\Filament\Resources\ShopOrders\Pages\ListShopOrders;
use App\Filament\Resources\ShopOrders\Schemas\ShopOrderForm;
use App\Filament\Resources\ShopOrders\Tables\ShopOrdersTable;
use App\Models\ShopOrder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

use App\Traits\HasModuleAccess;

class ShopOrderResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = ShopOrder::class;

    public static function getPermissionName(): ?string
    {
        return 'access_shop';
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static UnitEnum|string|null $navigationGroup = 'Commerce';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ShopOrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShopOrdersTable::configure($table);
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
            'index' => ListShopOrders::route('/'),
            'edit' => EditShopOrder::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
