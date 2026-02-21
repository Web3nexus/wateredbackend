<?php

namespace App\Filament\Resources\OrderApplications;

use App\Filament\Resources\OrderApplications\Pages\ManageOrderApplications;
use App\Models\OrderApplication;
use App\Mail\ApplicationApproved;
use App\Mail\ApplicationRejected;
use BackedEnum;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Support\Facades\Mail;

class OrderApplicationResource extends Resource
{
    protected static ?string $model = OrderApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;
    protected static UnitEnum|string|null $navigationGroup = 'Spiritual Practices';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Section::make('Application Info')->schema([
                    \Filament\Forms\Components\Select::make('order_id')
                        ->relationship('order', 'title')
                        ->disabled(),
                    \Filament\Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->disabled(),
                    \Filament\Forms\Components\Select::make('status')
                        ->options([
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ])
                        ->required(),
                    \Filament\Forms\Components\Textarea::make('admin_notes')
                        ->maxLength(65535)
                        ->columnSpanFull(),
                ])->columns(2),
                \Filament\Forms\Components\Section::make('Answers')->schema([
                    \Filament\Forms\Components\KeyValue::make('answers')
                        ->disabled()
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->form([
                        \Filament\Forms\Components\Textarea::make('admin_notes')
                            ->label('Admin Notes (Optional, will be sent in email)'),
                    ])
                    ->action(function (OrderApplication $record, array $data): void {
                        $record->update([
                            'status' => 'approved',
                            'admin_notes' => $data['admin_notes'] ?? $record->admin_notes,
                        ]);

                        try {
                            Mail::to($record->user->email)->send(new ApplicationApproved($record));
                        } catch (\Exception $e) {
                            \Log::error('Failed to send approval email: ' . $e->getMessage());
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Application Approved')
                            ->success()
                            ->send();
                    })
                    ->visible(fn(OrderApplication $record) => $record->status === 'pending'),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        \Filament\Forms\Components\Textarea::make('admin_notes')
                            ->label('Reason for Rejection (Optional, will be sent in email)'),
                    ])
                    ->action(function (OrderApplication $record, array $data): void {
                        $record->update([
                            'status' => 'rejected',
                            'admin_notes' => $data['admin_notes'] ?? $record->admin_notes,
                        ]);

                        try {
                            Mail::to($record->user->email)->send(new ApplicationRejected($record));
                        } catch (\Exception $e) {
                            \Log::error('Failed to send rejection email: ' . $e->getMessage());
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Application Rejected')
                            ->danger()
                            ->send();
                    })
                    ->visible(fn(OrderApplication $record) => $record->status === 'pending'),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageOrderApplications::route('/'),
        ];
    }
}
