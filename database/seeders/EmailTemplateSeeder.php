<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'verification',
                'subject' => 'Welcome to Watered - Verify Your Email',
                'body' => '<h1>Hello {{ name }}!</h1><p>Thank you for joining Watered. We are excited to have you on this journey of spiritual growth.</p><p>Please click the button below to verify your email address and activate your account:</p><div class="button-wrapper"><a href="{{ verification_url }}" class="button">Verify Email Address</a></div><p>If you did not create an account, you can safely ignore this email.</p><p>Stay blessed,<br>The Watered Team</p>',
                'description' => 'Sent when a user signs up to verify their email address.',
            ],
            [
                'key' => 'welcome',
                'subject' => 'Welcome to the Watered Community',
                'body' => '<h1>Welcome, {{ name }}!</h1><p>Your journey with Watered has officially begun. We are here to support your spiritual evolution through ancient wisdom and modern connection.</p><p>Explore the app to find daily wisdom, sacred rituals, and a community of seekers.</p><p>Stay blessed,<br>The Watered Team</p>',
                'description' => 'Sent after a user successfully verifies their email.',
            ],
            [
                'key' => 'newsletter',
                'subject' => 'Watered Insights: Weekly Wisdom',
                'body' => '<h1>Greetings {{ name }},</h1><p>Here is your weekly reminder of the spiritual forces guiding our path.</p><p>{{ content }}</p><div class="footer"><p>You are receiving this because you subscribed to our newsletter.</p><a href="{{ unsubscribe_url }}">Unsubscribe</a></div>',
                'description' => 'Template for newsletter broadcasts.',
            ],
            [
                'key' => 'password_reset',
                'subject' => 'Reset Your Watered Password',
                'body' => '<h1>Hello {{ name }}!</h1><p>We received a request to reset your password for your Watered account.</p><p>Please click the button below to set a new password:</p><div class="button-wrapper"><a href="{{ reset_url }}" class="button">Reset Password</a></div><p>If you did not request this, you can safely ignore this email.</p><p>Stay blessed,<br>The Watered Team</p>',
                'description' => 'Sent when a user requests a password reset.',
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(['key' => $template['key']], $template);
        }
    }
}
