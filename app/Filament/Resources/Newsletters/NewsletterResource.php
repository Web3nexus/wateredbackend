<?php

namespace App\Filament\Resources\Newsletters;

use App\Filament\Resources\Newsletters\Pages\CreateNewsletter;
use App\Filament\Resources\Newsletters\Pages\EditNewsletter;
use App\Filament\Resources\Newsletters\Pages\ListNewsletters;
use App\Models\Newsletter;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class NewsletterResource extends Resource
{
    protected static ?string $model = Newsletter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationGroup = 'Community';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('subject')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                RichEditor::make('content')
                    ->required()
                    ->columnSpanFull(),
                Select::make('recipients_type')
                    ->options([
                        'all' => 'All Users',
                        'subscribers' => 'Subscribers Only',
                    ])
                    ->default('all')
                    ->required(),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                    ])
                    ->default('draft')
                    ->required(),
                DateTimePicker::make('sent_at')
                    ->disabled()
                    ->label('Sent At'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sent' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('recipients_type'),
                TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                Action::make('sendTest')
                    ->label('Send Test')
                    ->icon('heroicon-o-beaker')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->label('Recipient Email')
                            ->default(auth()->user()?->email),
                    ])
                    ->action(function ($record, array $data) {
                        try {
                            \Illuminate\Support\Facades\Notification::route('mail', $data['email'])
                                ->notify(new \App\Notifications\TemplateTestNotification('newsletter'));

                            Notification::make()
                                ->title('Test Newsletter Sent')
                                ->body('A test newsletter has been sent to ' . $data['email'])
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Email Failed to Send')
                                ->body('Error: ' . $e->getMessage())
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    })
                    ->visible(fn(Newsletter $record) => $record->status !== 'sent'),

                Action::make('send')
                    ->label('Send Now (Queue)')
                    ->icon('heroicon-o-paper-airplane')
                    ->requiresConfirmation()
                    ->action(function (Newsletter $record) {
                        \App\Jobs\SendNewsletterJob::dispatch($record);

                        \Filament\Notifications\Notification::make()
                            ->title('Newsletter Sending Initiated')
                            ->body('The newsletter is being sent to recipients in the background. Ensure your queue worker is running.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn(Newsletter $record) => $record->status !== 'sent'),

                Action::make('sendDirect')
                    ->label('Emergency Direct Send')
                    ->icon('heroicon-o-bolt')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Send Immediately?')
                    ->modalDescription('This will send the newsletter immediately WITHOUT using a queue. Only use this for small updates or if the queue is not working. It may take a minute to process.')
                    ->action(function (Newsletter $record) {
                        try {
                            \App\Jobs\SendNewsletterJob::dispatchSync($record);

                            \Filament\Notifications\Notification::make()
                                ->title('Newsletter Sent')
                                ->body('The newsletter has been sent successfully to all recipients.')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Send Failed')
                                ->body('Error: ' . $e->getMessage())
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    })
                    ->visible(fn(Newsletter $record) => $record->status !== 'sent'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNewsletters::route('/'),
            'create' => CreateNewsletter::route('/create'),
            'edit' => EditNewsletter::route('/{record}/edit'),
        ];
    }
}
