<?php

namespace App\Filament\Widgets;

use App\Models\Subscription;
use App\Models\GlobalSetting;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class SubscriptionRevenueChart extends ChartWidget
{
    protected ?string $heading = 'Subscription Revenue';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Trend::model(Subscription::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'Monthly Revenue',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'fill' => 'start',
                    'tension' => 0.4,
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
