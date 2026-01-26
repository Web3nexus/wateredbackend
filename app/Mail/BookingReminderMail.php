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

class BookingReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
    }

    public function envelope(): Envelope
    {
        $template = EmailTemplate::where('key', 'booking_reminder')->first();
        return new Envelope(
            subject: $template ? Blade::render($template->subject, ['booking' => $this->booking]) : 'Consultation Reminder',
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
        $template = EmailTemplate::where('key', 'booking_reminder')->first();
        if (!$template) {
            return "Reminder for your consultation at {$this->booking->start_time->format('H:i')}.";
        }

        return Blade::render($template->body, ['booking' => $this->booking]);
    }
}
