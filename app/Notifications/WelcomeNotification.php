<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $template = EmailTemplate::render('welcome', [
            'name' => $notifiable->name,
            'app_name' => config('app.name'),
        ]);

        if (!$template) {
            return (new MailMessage)
                ->subject('Welcome to ' . config('app.name'))
                ->line('Hello ' . $notifiable->name . '!')
                ->line('Your account has been successfully verified.')
                ->line('Welcome to our community!');
        }

        return (new MailMessage)
            ->subject($template['subject'])
            ->view('emails.premium', ['body' => $template['body']]);
    }
}
