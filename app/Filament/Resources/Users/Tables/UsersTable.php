<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('provider')
                    ->label('Source')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'google' => 'success',
                        'apple' => 'gray',
                        'firebase_email' => 'info',
                        default => 'warning',
                    })
                    ->searchable(),
                \Filament\Tables\Columns\IconColumn::make('is_premium')
                    ->label('Premium')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('email_verified_at')
                    ->label('Verified')
                    ->badge()
                    ->state(fn(User $record): string => $record->hasVerifiedEmail() ? 'Verified' : 'Unverified')
                    ->color(fn(string $state): string => $state === 'Verified' ? 'success' : 'gray')
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
            fputcsv($handle, ['Name', 'Email', 'Source', 'Premium', 'Email Verified', 'Created At']);

            $query = $records ? collect($records) : User::all();

            foreach ($query as $user) {
                fputcsv($handle, [
                    $user->name,
                    $user->email,
                    $user->provider,
                    $user->is_premium ? 'Yes' : 'No',
                    $user->email_verified_at ? $user->email_verified_at->format('Y-m-d H:i') : 'No',
                    $user->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users-export-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
