<?php

namespace App\Filament\Widgets;

use App\Models\UserActivity;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ActivityStatsWidget extends BaseWidget
{
    protected static ?int $sort = 10;

    protected function getStats(): array
    {
        $totalSeconds = UserActivity::sum('duration_seconds');
        $totalHours = floor($totalSeconds / 3600);
        $totalMinutes = floor(($totalSeconds % 3600) / 60);

        $mostPopularPage = UserActivity::select('page', DB::raw('count(*) as count'))
            ->groupBy('page')
            ->orderBy('count', 'desc')
            ->first();

        $pageLabels = [
            'ritual' => 'Rituals',
            'incantation' => 'Incantations',
            'sacred_book' => 'Sacred Book',
            'deity' => 'Deities',
            'audio' => 'Audio',
        ];

        return [
            Stat::make('Total Engagement Time', "{$totalHours}h {$totalMinutes}m")
                ->description('Combined time spent in app')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),

            Stat::make('Most Active Section', $mostPopularPage ? ($pageLabels[$mostPopularPage->page] ?? $mostPopularPage->page) : 'N/A')
                ->description('Based on visit frequency')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),

            Stat::make('Total Activities Tracked', number_format(UserActivity::count()))
                ->description('Total events recorded')
                ->descriptionIcon('heroicon-m-cursor-arrow-rays')
                ->color('primary'),
        ];
    }
}
