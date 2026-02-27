<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\GlobalSetting;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class AppointmentRevenueChart extends ChartWidget
{
    protected ?string $heading = 'Appointment Revenue';
    protected static ?int $sort = 4;

    public static function canView(): bool
    {
        return !request()->routeIs('filament.securegate.pages.dashboard');
    }

    protected function getData(): array
    {
        $data = Trend::model(Appointment::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'Appointment Revenue',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'fill' => 'start',
                    'tension' => 0.4,
                    'borderColor' => '#8b5cf6',
                    'backgroundColor' => '#8b5cf633',
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
