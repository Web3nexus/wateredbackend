<?php

namespace App\Filament\Resources\Financials;

use App\Filament\Resources\Financials\Pages\ListRevenueTransactions;
use App\Models\RevenueTransaction;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;
use BackedEnum;

class RevenueResource extends Resource
{
    protected static ?string $model = RevenueTransaction::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-banknotes';
    protected static UnitEnum|string|null $navigationGroup = 'Financials';
    protected static ?string $navigationLabel = 'Revenue Records';
    protected static ?string $slug = 'revenue-records';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('source_type')
                    ->label('Source')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Subscription' => 'success',
                        'Event' => 'primary',
                        'Appointment' => 'info',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('reference')
                    ->label('Reference')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('amount')
                    ->money(fn() => \App\Models\GlobalSetting::first()->system_currency ?? 'NGN')
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'paid', 'completed', 'active' => 'success',
                        'pending' => 'warning',
                        'failed', 'expired', 'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('source_type')
                    ->options([
                        'Subscription' => 'Subscription',
                        'Event' => 'Event',
                        'Appointment' => 'Appointment',
                    ]),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                Action::make('export_all')
                    ->label('Export All (CSV)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn($livewire) => static::exportCsv($livewire->getFilteredTableQuery())),

                Action::make('export_subscriptions')
                    ->label('Export Subscriptions')
                    ->icon('heroicon-o-credit-card')
                    ->color('warning')
                    ->action(fn() => static::exportCsv(\App\Models\RevenueTransaction::where('source_type', 'Subscription'))),

                Action::make('export_events')
                    ->label('Export Events')
                    ->icon('heroicon-o-calendar-days')
                    ->color('primary')
                    ->action(fn() => static::exportCsv(\App\Models\RevenueTransaction::where('source_type', 'Event'))),

                Action::make('export_appointments')
                    ->label('Export Appointments')
                    ->icon('heroicon-o-calendar')
                    ->color('info')
                    ->action(fn() => static::exportCsv(\App\Models\RevenueTransaction::where('source_type', 'Appointment'))),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\SubscriptionRevenueChart::class,
            \App\Filament\Widgets\EventRevenueChart::class,
            \App\Filament\Widgets\AppointmentRevenueChart::class,
        ];
    }

    public static function exportCsv($query): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Source', 'Description', 'Reference', 'Customer', 'Amount', 'Status']);

            $query->chunk(100, function ($records) use ($handle) {
                foreach ($records as $record) {
                    fputcsv($handle, [
                        $record->created_at->format('Y-m-d H:i'),
                        $record->source_type,
                        $record->description,
                        $record->reference,
                        $record->customer_name,
                        $record->amount,
                        $record->payment_status,
                    ]);
                }
            });

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="revenue-export-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRevenueTransactions::route('/'),
        ];
    }
}
