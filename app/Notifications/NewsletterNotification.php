<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewsletterNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $subject;
    public $content;

    public function __construct($subject, $content)
    {
        $this->subject = $subject;
        $this->content = $content;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $template = EmailTemplate::render('newsletter', [
            'name' => $notifiable->name,
            'content' => $this->content,
            'app_name' => config('app.name'),
            'unsubscribe_url' => url('/unsubscribe?email=' . urlencode($notifiable->email)),
        ]);

        $subject = $template ? $template['subject'] : $this->subject;
        $body = $template ? $template['body'] : $this->content;

        // Ensure the subject from the newsletter record is used if the template subject is generic
        if ($this->subject) {
            $subject = $this->subject;
        }

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.premium', ['body' => $body]);
    }
}
