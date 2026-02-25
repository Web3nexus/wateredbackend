<?php

namespace App\Filament\Resources\Subscriptions;

use App\Filament\Resources\Subscriptions\Pages\ManageSubscriptions;
use App\Models\Subscription;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static UnitEnum|string|null $navigationGroup = 'Community';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('plan_id')
                    ->required(),
                TextInput::make('provider')
                    ->required(),
                TextInput::make('platform'),
                TextInput::make('provider_subscription_id')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
                DateTimePicker::make('starts_at')
                    ->required(),
                DateTimePicker::make('expires_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('plan_id')
                    ->label('Plan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('provider')
                    ->label('Payment Provider')
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->searchable(),
                TextColumn::make('platform')
                    ->label('Source')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'ios' => 'slate',
                        'android' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'ios' => 'heroicon-o-device-phone-mobile',
                        'android' => 'heroicon-o-device-phone-mobile',
                        default => 'heroicon-o-globe-alt',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'ios' => 'iPhone/iOS',
                        'android' => 'Android',
                        default => ucfirst($state),
                    }),
                TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'expired' => 'danger',
                        'cancelled' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('starts_at')
                    ->label('Started On')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label('Expires On')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('platform')
                    ->label('Source')
                    ->options([
                        'ios' => 'iPhone/iOS',
                        'android' => 'Android',
                    ]),
            ])
            ->headerActions([
                \Filament\Actions\Action::make('export_csv')
                    ->label('Export All (CSV)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn() => static::exportCsv()),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    \Filament\Actions\BulkAction::make('export_selected_csv')
                        ->label('Export Selected (CSV)')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(fn($records) => static::exportCsv($records)),
                ]),
            ]);
    }

    public static function exportCsv($records = null): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($records) {
            $handle = fopen('php://output', 'w');

            // Header
            fputcsv($handle, ['Customer', 'Plan', 'Provider', 'Source', 'Amount', 'Status', 'Started At', 'Expires At']);

            $query = $records ? collect($records) : Subscription::with('user')->get();

            foreach ($query as $subscription) {
                fputcsv($handle, [
                    $subscription->user->name ?? 'Unknown',
                    $subscription->plan_id,
                    $subscription->provider,
                    $subscription->platform,
                    $subscription->amount,
                    $subscription->status,
                    $subscription->starts_at->format('Y-m-d H:i'),
                    $subscription->expires_at ? $subscription->expires_at->format('Y-m-d H:i') : 'Never',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="subscriptions-export-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSubscriptions::route('/'),
        ];
    }
}
