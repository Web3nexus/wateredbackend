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
        $reg = $this->registration;
        $event = $reg->event;

        $date = $event->event_date?->format('l, F j, Y') ?? 'TBA';
        $time = $event->event_time ? date('g:i A', strtotime($event->event_time)) : 'TBA';
        $location = $event->location ?? 'Online';

        $body = "
            <h1>Booking Confirmation</h1>
            <p>Hi {$reg->full_name},</p>
            <p>You have successfully booked your spot for <strong>{$event->title}</strong>.</p>
            
            <div style='background: rgba(234, 179, 8, 0.05); padding: 20px; border-radius: 12px; margin: 20px 0;'>
                <h2 style='color: #EAB308; font-size: 18px; margin-top: 0;'>Event Details</h2>
                <p style='margin-bottom: 5px;'><strong>Date:</strong> {$date}</p>
                <p style='margin-bottom: 5px;'><strong>Time:</strong> {$time}</p>
                <p style='margin-bottom: 0;'><strong>Location:</strong> {$location}</p>
            </div>

            <h2 style='color: #EAB308; font-size: 18px;'>Your Ticket</h2>
            <p style='margin-bottom: 5px;'><strong>Name:</strong> {$reg->full_name}</p>
            <p style='margin-bottom: 5px;'><strong>Email:</strong> {$reg->email}</p>
            <p style='margin-bottom: 0;'><strong>Phone:</strong> {$reg->phone}</p>
            " . ($reg->payment_reference ? "<p><strong>Reference:</strong> {$reg->payment_reference}</p>" : "") . "

            <p style='margin-top: 30px; text-align: center;'>We look forward to seeing you there!</p>
        ";

        return new Content(
            view: 'emails.premium',
            with: [
                'body' => $body,
            ],
        );
    }
}
