<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;

class AppointmentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment $appointment)
    {
    }

    public function envelope(): Envelope
    {
        $template = EmailTemplate::where('key', 'appointment_reminder')->first();
        return new Envelope(
            subject: $template ? Blade::render($template->subject, ['appointment' => $this->appointment]) : 'Consultation Reminder',
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
        $template = EmailTemplate::where('key', 'appointment_reminder')->first();
        if (!$template) {
            return "Reminder for your consultation at {$this->appointment->start_time->format('H:i')}.";
        }

        return Blade::render($template->body, ['appointment' => $this->appointment]);
    }
}
