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

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
    }

    public function envelope(): Envelope
    {
        $template = EmailTemplate::where('key', 'booking_confirmation')->first();
        return new Envelope(
            subject: $template ? Blade::render($template->subject, ['booking' => $this->booking]) : 'Booking Confirmed',
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
        $template = EmailTemplate::where('key', 'booking_confirmation')->first();
        if (!$template) {
            return "Booking confirmed for {$this->booking->consultationType->name}.";
        }

        return Blade::render($template->body, ['booking' => $this->booking]);
    }
}
