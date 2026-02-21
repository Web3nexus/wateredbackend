<?php

namespace App\Mail;

use App\Models\OrderApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public OrderApplication $application)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Application Approved: ' . $this->application->order->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.approved',
            with: [
                'orderTitle' => $this->application->order->title,
                'userName' => $this->application->user->name,
                'adminNotes' => $this->application->admin_notes,
            ],
        );
    }
}
