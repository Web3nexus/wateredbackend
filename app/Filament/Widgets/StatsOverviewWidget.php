<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Get Currency Symbol
        $settings = \App\Models\GlobalSetting::first();
        $currency = $settings->currency_symbol ?? '$';

        // Total Users
        $totalUsers = User::count();

        // Total Subscribed Users
        $subscribedUsers = User::where('is_premium', true)->count();

        // Subscription Earnings
        $subEarnings = 0;
        if (Schema::hasTable('subscriptions') && Schema::hasColumn('subscriptions', 'amount')) {
            $subEarnings = DB::table('subscriptions')
                ->where('status', 'active')
                ->sum('amount');
        }

        // Event Earnings
        $eventEarnings = 0;
        if (Schema::hasTable('event_registrations') && Schema::hasColumn('event_registrations', 'amount')) {
            $eventEarnings = DB::table('event_registrations')
                ->where('payment_status', 'completed')
                ->sum('amount');
        }

        // Appointment Earnings
        $appointmentEarnings = 0;
        if (Schema::hasTable('appointments') && Schema::hasColumn('appointments', 'amount')) {
            $appointmentEarnings = DB::table('appointments')
                ->where('payment_status', 'paid')
                ->sum('amount');
        }

        $totalEarnings = $subEarnings + $eventEarnings + $appointmentEarnings;

        return [
            Stat::make('Total Users', number_format($totalUsers))
                ->description('Registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Subscribed Users', number_format($subscribedUsers))
                ->description('Premium members')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),

            Stat::make('Total Revenue', $currency . number_format($totalEarnings, 2))
                ->description('All payment sources')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Subscription Revenue', $currency . number_format($subEarnings, 2))
                ->description('From active plans')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('warning'),

            Stat::make('Event Revenue', $currency . number_format($eventEarnings, 2))
                ->description('From registrations')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),

            Stat::make('Appointment Revenue', $currency . number_format($appointmentEarnings, 2))
                ->description('From consultations')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
        ];
    }
}
