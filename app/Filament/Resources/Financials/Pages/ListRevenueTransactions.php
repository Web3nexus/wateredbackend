<?php

namespace App\Filament\Resources\Financials\Pages;

use App\Filament\Resources\Financials\RevenueResource;
use Filament\Resources\Pages\ListRecords;

class ListRevenueTransactions extends ListRecords
{
    protected static string $resource = RevenueResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            // Potentially add revenue-specific widgets here later
        ];
    }
}
