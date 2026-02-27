<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriptionStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $settings = \App\Models\GlobalSetting::first();
        $currency = $settings->currency_symbol ?? '$';

        return [
            Stat::make('Total Revenue', $currency . number_format(\App\Models\Subscription::where('status', 'active')->sum('amount'), 2))
                ->description('Total from active subscriptions')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Active Subscribers', \App\Models\Subscription::where('status', 'active')->count())
                ->description('Current paying users')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('iOS Revenue', $currency . number_format(\App\Models\Subscription::where('status', 'active')->where('platform', 'ios')->sum('amount'), 2))
                ->description('Revenue from iPhone users')
                ->descriptionIcon('heroicon-m-device-phone-mobile')
                ->color('slate'),

            Stat::make('Android Revenue', $currency . number_format(\App\Models\Subscription::where('status', 'active')->where('platform', 'android')->sum('amount'), 2))
                ->description('Revenue from Android users')
                ->descriptionIcon('heroicon-m-device-phone-mobile')
                ->color('success'),
        ];
    }
}
