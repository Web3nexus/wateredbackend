<?php

namespace App\Filament\Widgets;

use App\Models\EventRegistration;
use App\Models\GlobalSetting;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class EventRevenueChart extends ChartWidget
{
    protected ?string $heading = 'Event Revenue';
    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return !request()->routeIs('filament.securegate.pages.dashboard');
    }

    protected function getData(): array
    {
        $data = Trend::model(EventRegistration::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'Event Revenue',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'fill' => 'start',
                    'tension' => 0.4,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => '#3b82f633',
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        $settings = GlobalSetting::first();
        $currency = $settings->currency_symbol ?? '$';

        return [
            'scales' => [
                'y' => [
                    'ticks' => [
                        'callback' => "value => '{$currency}' + value.toLocaleString()",
                    ],
                ],
            ],
        ];
    }
}
