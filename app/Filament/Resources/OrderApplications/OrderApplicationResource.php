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
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\KeyValue;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

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
                Section::make('Application Info')->schema([
                    Select::make('order_id')
                        ->relationship('order', 'title')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->disabled(fn(string $operation): bool => $operation !== 'create'),
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->disabled(fn(string $operation): bool => $operation !== 'create'),
                    Select::make('status')
                        ->options([
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ])
                        ->default('pending')
                        ->required(),
                    Textarea::make('admin_notes')
                        ->label('Admin Internal Notes')
                        ->helperText('Notes for internal use, or to be sent in approval/rejection emails.')
                        ->maxLength(65535)
                        ->columnSpanFull(),
                ])->columns(2),
                Section::make('Answers')
                    ->description('Responses provided by the user for this specific order type.')
                    ->schema([
                        KeyValue::make('answers')
                            ->label('User Responses')
                            ->helperText('This holds the specific data fields submitted by the user.')
                            ->disabled(fn(string $operation): bool => $operation !== 'create')
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
                        Textarea::make('admin_notes')
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
                            Log::error('Failed to send approval email: ' . $e->getMessage());
                        }

                        Notification::make()
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
                        Textarea::make('admin_notes')
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
                            Log::error('Failed to send rejection email: ' . $e->getMessage());
                        }

                        Notification::make()
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
