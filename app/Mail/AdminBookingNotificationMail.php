<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;

class AdminBookingNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
    }

    public function envelope(): Envelope
    {
        $template = EmailTemplate::where('key', 'admin_booking_notification')->first();
        return new Envelope(
            subject: $template ? Blade::render($template->subject, ['booking' => $this->booking]) : 'New Booking Notification',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.dynamic',
            with: [
                'body' => $this->getRenderedBody(),
            ],
        );
    }

    protected function getRenderedBody(): string
    {
        $template = EmailTemplate::where('key', 'admin_booking_notification')->first();
        if (!$template) {
            return "New booking from {$this->booking->user->name}.";
        }

        return Blade::render($template->body, ['booking' => $this->booking]);
    }
}
