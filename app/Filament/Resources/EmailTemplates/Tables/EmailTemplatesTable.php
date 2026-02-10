<?php

namespace App\Filament\Resources\EmailTemplates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmailTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                TextColumn::make('key')
                    ->weight('bold')
                    ->icon('heroicon-o-key')
                    ->searchable(),
                TextColumn::make('subject')
                    ->icon('heroicon-o-envelope')
                    ->searchable(),
                TextColumn::make('description')
                    ->color('gray')
                    ->size('sm')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                Action::make('sendTest')
                    ->label('Send Test')
                    ->icon('heroicon-o-paper-airplane')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->label('Recipient Email')
                            ->default(auth()->user()?->email),
                    ])
                    ->action(function ($record, array $data) {
                        \Illuminate\Support\Facades\Notification::route('mail', $data['email'])
                            ->notify(new \App\Notifications\TemplateTestNotification($record->key));

                        \Filament\Notifications\Notification::make()
                            ->title('Test Email Sent')
                            ->body('A test email using this template has been sent to ' . $data['email'])
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
