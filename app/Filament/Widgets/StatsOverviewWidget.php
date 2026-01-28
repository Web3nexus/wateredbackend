<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Total Users
        $totalUsers = User::count();

        // Total Subscribed Users (assuming isPremium field or subscription relationship)
        $subscribedUsers = User::where('is_premium', true)->count();

        // Total Earnings (from subscriptions - assuming a subscriptions table or payment records)
        // This is a placeholder - adjust based on your actual payment/subscription structure
        $totalEarnings = DB::table('subscriptions')
            ->where('status', 'active')
            ->sum('amount') ?? 0;

        // Total Products
        $totalProducts = Product::count();

        return [
            Stat::make('Total Users', number_format($totalUsers))
                ->description('Registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Subscribed Users', number_format($subscribedUsers))
                ->description('Premium members')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),

            Stat::make('Total Earnings', '$' . number_format($totalEarnings, 2))
                ->description('From subscriptions')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Total Products', number_format($totalProducts))
                ->description('Available in shop')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),
        ];
    }
}
