<?php

namespace App\Mail;

use App\Models\EventRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventBookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public EventRegistration $registration)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Event Booking Confirmation - ' . $this->registration->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.events.booking_confirmation',
        );
    }
}
