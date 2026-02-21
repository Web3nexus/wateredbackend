<?php

namespace App\Mail;

use App\Models\OrderApplication;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public OrderApplication $application)
    {
    }

    public function envelope(): Envelope
    {
        $template = EmailTemplate::where('key', 'order_submitted')->first();
        $subject = 'Application Received: ' . $this->application->order->title;

        if ($template) {
            $rendered = EmailTemplate::render('order_submitted', [
                'order_title' => $this->application->order->title,
                'user_name' => $this->application->user->name,
                'app_name' => config('app.name'),
            ]);
            $subject = $rendered['subject'];
        }

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $template = EmailTemplate::where('key', 'order_submitted')->first();

        if ($template) {
            $rendered = EmailTemplate::render('order_submitted', [
                'order_title' => $this->application->order->title,
                'user_name' => $this->application->user->name,
                'app_name' => config('app.name'),
            ]);

            return new Content(
                view: 'emails.premium',
                with: ['body' => $rendered['body']],
            );
        }

        return new Content(
            markdown: 'emails.orders.submitted',
            with: [
                'orderTitle' => $this->application->order->title,
                'userName' => $this->application->user->name,
            ],
        );
    }
}
