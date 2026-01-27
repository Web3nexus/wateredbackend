<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmailTemplateSeeder extends Seeder
{
    private function getLayout($title, $content)
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>$title</title>
            <style>
                body { margin: 0; padding: 0; background-color: #F8F5F2; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #1E293B; }
                .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-radius: 8px; margin-top: 40px; margin-bottom: 40px; }
                .header { background: linear-gradient(135deg, #0077BE 0%, #005E99 100%); padding: 40px 0; text-align: center; }
                .logo { font-size: 28px; font-weight: bold; color: #FFFFFF; letter-spacing: 4px; text-transform: uppercase; font-family: 'Georgia', serif; }
                .content { padding: 40px; line-height: 1.6; }
                .button { display: inline-block; padding: 14px 28px; background-color: #D4AF37; color: #ffffff; text-decoration: none; border-radius: 30px; font-weight: bold; margin-top: 20px; transition: background-color 0.3s; }
                .button:hover { background-color: #B5952F; }
                .footer { background-color: #1E293B; color: #94A3B8; padding: 30px; text-align: center; font-size: 12px; }
                .divider { height: 1px; background-color: #E2E8F0; margin: 30px 0; }
                h1 { color: #0F172A; font-family: 'Georgia', serif; font-size: 24px; margin-bottom: 20px; }
                p { margin-bottom: 16px; color: #475569; }
                .highlight { color: #0077BE; font-weight: 600; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='logo'>Watered</div>
                </div>
                <div class='content'>
                    $content
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Watered. Ancient African Spirituality.</p>
                    <p>You received this email because you are a valued member of our community.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    public function run(): void
    {
        $templates = [
            // Booking/Consultation Templates
            [
                'key' => 'booking_confirmation',
                'subject' => 'Consultation Booking Confirmed - Watered',
                'description' => 'Sent to users when a booking is confirmed.',
                'body' => $this->getLayout('Booking Confirmed', "
                    <h1>Booking Confirmed</h1>
                    <p>Hello {{ \$booking->user->name }},</p>
                    <p>Your consultation for <strong>{{ \$booking->consultationType->name }}</strong> has been confirmed.</p>
                    <p><strong>Date & Time:</strong> {{ \$booking->start_time->format('F j, Y, g:i a') }}</p>
                    <p>Thank you for choosing Watered.</p>
                "),
            ],
            [
                'key' => 'admin_booking_notification',
                'subject' => 'New Consultation Booking - Action Required',
                'description' => 'Sent to admins when a new booking is made.',
                'body' => $this->getLayout('New Booking Received', "
                    <h1>New Booking Received</h1>
                    <p>A new consultation has been booked.</p>
                    <p><strong>User:</strong> {{ \$booking->user->name }} ({{ \$booking->user->email }})</p>
                    <p><strong>Type:</strong> {{ \$booking->consultationType->name }}</p>
                    <p><strong>Requested Time:</strong> {{ \$booking->start_time->format('F j, Y, g:i a') }}</p>
                    <p><strong>Notes:</strong><br>{{ \$booking->notes ?? 'No notes provided.' }}</p>
                "),
            ],
            [
                'key' => 'booking_reminder',
                'subject' => 'Consultation Reminder - Watered',
                'description' => 'Sent to users 1 hour and 30 minutes before their booking.',
                'body' => $this->getLayout('Consultation Reminder', "
                    <h1>Consultation Reminder</h1>
                    <p>Hello {{ \$booking->user->name }},</p>
                    <p>This is a reminder for your upcoming consultation for <strong>{{ \$booking->consultationType->name }}</strong>.</p>
                    <p><strong>Date & Time:</strong> {{ \$booking->start_time->format('F j, Y, g:i a') }}</p>
                    <p>We look forward to connecting with you.</p>
                "),
            ],

            // Authentication Templates
            [
                'key' => 'registration_verification',
                'subject' => 'Verify Your Watered Account',
                'description' => 'Sent upon registration to verify email.',
                'body' => $this->getLayout('Verify Your Email', "
                    <h1>Welcome to Watered</h1>
                    <p>Greetings, Seeker.</p>
                    <p>Thank you for joining our community dedicated to Ancient African Spirituality. To begin your journey and access the sacred wisdom within, please verify your email address.</p>
                    <center><a href='{{ \$verificationUrl }}' class='button'>Verify Email Address</a></center>
                    <p style='margin-top: 30px; font-size: 13px; color: #94A3B8;'>If you did not create an account, no further action is required.</p>
                "),
            ],
            [
                'key' => 'welcome_verified',
                'subject' => 'Welcome to the Community',
                'description' => 'Sent after successful email verification.',
                'body' => $this->getLayout('Welcome Aboard', "
                    <h1>Your Journey Begins</h1>
                    <p>Hello {{ \$user->name }},</p>
                    <p>Your email has been successfully verified. You now have full access to our community features.</p>
                    <p>Explore the <strong>Library</strong> for ancient texts, visit the <strong>Temple</strong> for guided meditations, or connect with others in the <strong>Community</strong>.</p>
                    <center><a href='{{ \$appUrl }}' class='button'>Open App</a></center>
                "),
            ],

            // Subscription Templates
            [
                'key' => 'subscription_confirmed',
                'subject' => 'Watered+ Subscription Confirmed',
                'description' => 'Sent when a user subscribes to Premium.',
                'body' => $this->getLayout('Subscription Confirmed', "
                    <h1>Welcome to Watered+</h1>
                    <p>Thank you, {{ \$user->name }}.</p>
                    <p>Your subscription to <span class='highlight'>Watered+</span> has been confirmed. You now have unlimited access to:</p>
                    <ul>
                        <li>Full Library of Sacred Texts</li>
                        <li>Exclusive Video & Audio Teachings</li>
                        <li>Ad-Free Experience</li>
                        <li>Priority Community Access</li>
                    </ul>
                    <p>May this wisdom guide your path.</p>
                "),
            ],
            [
                'key' => 'subscription_expiring_5_days',
                'subject' => 'Your Subscription Expires in 5 Days',
                'description' => 'Renewal reminder 5 days before expiration.',
                'body' => $this->getLayout('Renewal Reminder', "
                    <h1>5 Days Remaining</h1>
                    <p>Hello {{ \$user->name }},</p>
                    <p>This is a gentle reminder that your <strong>Watered+</strong> subscription will expire in <span class='highlight'>5 days</span>.</p>
                    <p>To ensure uninterrupted access to your spiritual resources, please ensure your payment method is up to date or renew your subscription.</p>
                    <center><a href='{{ \$renewalUrl }}' class='button'>Manage Subscription</a></center>
                "),
            ],
            [
                'key' => 'subscription_expiring_3_days',
                'subject' => '3 Days Left: Keep Your Access',
                'description' => 'Renewal reminder 3 days before expiration.',
                'body' => $this->getLayout('Action Required', "
                    <h1>3 Days Remaining</h1>
                    <p>Greetings {{ \$user->name }},</p>
                    <p>Your journey with <strong>Watered+</strong> is important to us. Your subscription is set to expire in just <span class='highlight'>3 days</span>.</p>
                    <p>Don't lose access to your saved bookmarks, exclusive texts, and daily wisdom.</p>
                    <center><a href='{{ \$renewalUrl }}' class='button'>Renew Now</a></center>
                "),
            ],
            [
                'key' => 'subscription_expiring_24_hours',
                'subject' => 'Final Notice: Subscription Expiring Soon',
                'description' => 'Final renewal reminder 24 hours before expiration.',
                'body' => $this->getLayout('Final Reminder', "
                    <h1>24 Hours Left</h1>
                    <p>Hello {{ \$user->name }},</p>
                    <p>Your <strong>Watered+</strong> subscription will expire in less than 24 hours. This is your final notice to renew before your premium access is paused.</p>
                    <p>Continue your study without interruption.</p>
                    <center><a href='{{ \$renewalUrl }}' class='button'>Renew Access</a></center>
                "),
            ],
        ];

        foreach ($templates as $template) {
            DB::table('email_templates')->updateOrInsert(
                ['key' => $template['key']],
                [
                    'subject' => $template['subject'],
                    'body' => $template['body'],
                    'description' => $template['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
