<?php

namespace App\Mail;

use App\Models\EventRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventAdminNotificationMail extends Mailable
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
            subject: 'New Event Booking: ' . $this->registration->event->title . ' - ' . $this->registration->full_name,
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
            <h1>New Event Registration</h1>
            <p>A new registration has been received for <strong>{$event->title}</strong>.</p>
            
            <div style='background: rgba(234, 179, 8, 0.05); padding: 20px; border-radius: 12px; margin: 20px 0;'>
                <h2 style='color: #EAB308; font-size: 18px; margin-top: 0;'>Registrant Details</h2>
                <p style='margin-bottom: 5px;'><strong>Booking Code:</strong> {$reg->booking_code}</p>
                <p style='margin-bottom: 5px;'><strong>Name:</strong> {$reg->full_name}</p>
                <p style='margin-bottom: 5px;'><strong>Email:</strong> {$reg->email}</p>
                <p style='margin-bottom: 5px;'><strong>Phone:</strong> {$reg->phone}</p>
                " . ($reg->payment_status === 'completed' ? "<p style='color: #22C55E;'><strong>Status:</strong> Paid</p>" : "<p style='color: #EAB308;'><strong>Status:</strong> {$reg->status}</p>") . "
            </div>

            <div style='padding: 20px; border: 1px solid rgba(234, 179, 8, 0.1); border-radius: 12px;'>
                <h2 style='color: #EAB308; font-size: 18px; margin-top: 0;'>Event Info</h2>
                <p style='margin-bottom: 5px;'><strong>Title:</strong> {$event->title}</p>
                <p style='margin-bottom: 5px;'><strong>Date:</strong> {$date}</p>
                <p style='margin-bottom: 0;'><strong>Location:</strong> {$location}</p>
            </div>
        ";

        return new Content(
            view: 'emails.premium',
            with: [
                'body' => $body,
            ],
        );
    }
}
