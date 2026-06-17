<?php

namespace App\Filament\Resources\ShopOrders\Pages;

use App\Filament\Resources\ShopOrders\ShopOrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Carbon;

class EditShopOrder extends EditRecord
{
    protected static string $resource = ShopOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['status'])) {
            if ($data['status'] === 'shipped' && !$this->record->shipped_at) {
                $data['shipped_at'] = Carbon::now();
            }
            if ($data['status'] === 'delivered' && !$this->record->delivered_at) {
                $data['delivered_at'] = Carbon::now();
            }
            if ($data['status'] !== 'shipped') {
                $data['shipped_at'] = $this->record->shipped_at;
            }
            if ($data['status'] !== 'delivered') {
                $data['delivered_at'] = $this->record->delivered_at;
            }
        }
        return $data;
    }
}
