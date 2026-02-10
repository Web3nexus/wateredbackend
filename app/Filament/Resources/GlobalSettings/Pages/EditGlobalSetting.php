<?php

namespace App\Filament\Resources\GlobalSettings\Pages;

use App\Filament\Resources\GlobalSettings\GlobalSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditGlobalSetting extends EditRecord
{
    protected static string $resource = GlobalSettingResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('test_mail')
                ->label('Test Mail Setup')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\TextInput::make('recipient_email')
                        ->email()
                        ->label('Recipient Email')
                        ->default(auth()->user()?->email)
                        ->required(),
                ])
                ->action(function (array $data) {
                    try {
                        // The SMTP settings are applied in AppServiceProvider @ boot
                        // But we want to ensure the current record's settings are used
                        // if they haven't been saved yet. However, header actions 
                        // usually work on the PERSISTED record or use the form data.
        
                        $recipient = $data['recipient_email'];

                        \Illuminate\Support\Facades\Mail::raw('This is a test email to verify your SMTP configuration. If you received this, your email setup is working correctly!', function ($message) use ($recipient) {
                            $message->to($recipient)
                                ->subject('SMTP Configuration Test - Watered');
                        });

                        \Filament\Notifications\Notification::make()
                            ->title('SMTP Connection Successful')
                            ->body('A test email has been sent to ' . $recipient)
                            ->success()
                            ->send();

                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('SMTP Configuration Error')
                            ->body('Could not send test email: ' . $e->getMessage())
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                }),
            DeleteAction::make(),
        ];
    }
}