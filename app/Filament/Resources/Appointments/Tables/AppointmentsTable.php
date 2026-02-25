<?php

namespace App\Filament\Resources\Appointments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Appointment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('appointment_code')
                    ->searchable()
                    ->copyable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->placeholder('Guest'),
                TextColumn::make('full_name')
                    ->label('Guest Name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('consultationType.name')
                    ->searchable(),
                TextColumn::make('start_time')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('appointment_status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('amount')
                    ->money('NGN')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('export_csv')
                    ->label('Export All (CSV)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn() => static::exportCsv()),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('export_selected_csv')
                        ->label('Export Selected (CSV)')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(fn($records) => static::exportCsv($records)),
                ]),
            ]);
    }

    public static function exportCsv($records = null): StreamedResponse
    {
        return new StreamedResponse(function () use ($records) {
            $handle = fopen('php://output', 'w');

            // Header
            fputcsv($handle, ['Code', 'User/Guest', 'Type', 'Time', 'Status', 'Payment', 'Amount', 'Created At']);

            $query = $records ? collect($records) : Appointment::with(['user', 'consultationType'])->get();

            foreach ($query as $appointment) {
                fputcsv($handle, [
                    $appointment->appointment_code,
                    $appointment->user ? $appointment->user->name : ($appointment->full_name ?? 'Guest'),
                    $appointment->consultationType->name ?? 'N/A',
                    $appointment->start_time ? $appointment->start_time->format('Y-m-d H:i') : 'N/A',
                    $appointment->appointment_status,
                    $appointment->payment_status,
                    $appointment->amount,
                    $appointment->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="appointments-export-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
