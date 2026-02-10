<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class AppVerifyEmail extends Notification
{

    public $verificationUrl;

    public function __construct($verificationUrl)
    {
        $this->verificationUrl = $verificationUrl;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $template = EmailTemplate::render('verification', [
            'name' => $notifiable->name,
            'verification_url' => $this->verificationUrl,
            'app_name' => config('app.name'),
        ]);

        if (!$template) {
            // Fallback to default Laravel verification if template missing
            return (new MailMessage)
                ->subject(Lang::get('Verify Email Address'))
                ->line(Lang::get('Please click the button below to verify your email address.'))
                ->action(Lang::get('Verify Email Address'), $this->verificationUrl)
                ->line(Lang::get('If you did not create an account, no further action is required.'));
        }

        return (new MailMessage)
            ->subject($template['subject'])
            ->view('emails.premium', ['body' => $template['body']]);
    }
}
