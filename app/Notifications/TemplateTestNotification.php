<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TemplateTestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $templateKey;
    public $placeholders;

    public function __construct($templateKey, $placeholders = [])
    {
        $this->templateKey = $templateKey;
        $this->placeholders = $placeholders;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $template = EmailTemplate::render($this->templateKey, array_merge([
            'name' => 'Test User',
            'verification_url' => url('/test-verify'),
            'reset_url' => url('/test-reset'),
            'app_name' => config('app.name'),
            'unsubscribe_url' => url('/test-unsubscribe'),
            'content' => 'This is a test content for the newsletter.',
        ], $this->placeholders));

        if (!$template) {
            return (new MailMessage)
                ->subject('Template Test Failed')
                ->line('The template with key "' . $this->templateKey . '" was not found.');
        }

        return (new MailMessage)
            ->subject('[TEST] ' . $template['subject'])
            ->view('emails.premium', ['body' => $template['body']]);
    }
}
