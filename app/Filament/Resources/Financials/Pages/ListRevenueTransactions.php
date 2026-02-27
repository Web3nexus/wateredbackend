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
            \App\Filament\Widgets\SubscriptionRevenueChart::class,
            \App\Filament\Widgets\EventRevenueChart::class,
            \App\Filament\Widgets\AppointmentRevenueChart::class,
        ];
    }
}
