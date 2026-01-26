<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'key' => 'booking_confirmation',
                'subject' => 'Consultation Booking Confirmed - Watered',
                'body' => "# Booking Confirmed\n\nHello {{ \$booking->user->name }},\n\nYour consultation for **{{ \$booking->consultationType->name }}** has been confirmed.\n\n**Date & Time:** {{ \$booking->start_time->format('F j, Y, g:i a') }}\n\nThank you for choosing Watered.",
                'description' => 'Sent to users when a booking is confirmed.',
            ],
            [
                'key' => 'admin_booking_notification',
                'subject' => 'New Consultation Booking - Action Required',
                'body' => "# New Booking Received\n\nA new consultation has been booked.\n\n**User:** {{ \$booking->user->name }} ({{ \$booking->user->email }})\n**Type:** {{ \$booking->consultationType->name }}\n**Requested Time:** {{ \$booking->start_time->format('F j, Y, g:i a') }}\n\n**Notes:** \n{{ \$booking->notes ?? 'No notes provided.' }}",
                'description' => 'Sent to admins when a new booking is made.',
            ],
            [
                'key' => 'booking_reminder',
                'subject' => 'Consultation Reminder - Watered',
                'body' => "# Consultation Reminder\n\nHello {{ \$booking->user->name }},\n\nThis is a reminder for your upcoming consultation for **{{ \$booking->consultationType->name }}**.\n\n**Date & Time:** {{ \$booking->start_time->format('F j, Y, g:i a') }}\n\nWe look forward to connecting with you.",
                'description' => 'Sent to users 1 hour and 30 minutes before their booking.',
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(['key' => $template['key']], $template);
        }
    }
}
