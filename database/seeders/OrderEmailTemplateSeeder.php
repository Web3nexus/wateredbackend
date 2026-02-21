<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class OrderEmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'order_submitted',
                'subject' => 'Application Received: {{ order_title }}',
                'body' => '<h1>Application Received</h1><p>Hello {{ user_name }},</p><p>We have received your application for <strong>{{ order_title }}</strong>.</p><p>Our elders will review your submission and you will be notified via email once a decision has been made.</p><p>Thanks,<br>{{ app_name }}</p>',
                'description' => 'Sent to the user after they submit an order application.',
            ],
            [
                'key' => 'order_approved',
                'subject' => 'Application Approved: {{ order_title }}',
                'body' => '<h1>Application Approved</h1><p>Hello {{ user_name }},</p><p>Congratulations! Your application for <strong>{{ order_title }}</strong> has been approved.</p><div class="notes-wrapper"><strong>Elders\' Notes:</strong><br>{{ admin_notes }}</div><p>We look forward to your journey with us.</p><p>Thanks,<br>{{ app_name }}</p>',
                'description' => 'Sent when an admin approves an order application.',
            ],
            [
                'key' => 'order_rejected',
                'subject' => 'Application Status: {{ order_title }}',
                'body' => '<h1>Application Status Update</h1><p>Hello {{ user_name }},</p><p>Thank you for your interest in <strong>{{ order_title }}</strong>.</p><p>After careful review, we are unable to approve your application at this time.</p><div class="notes-wrapper"><strong>Elders\' Notes:</strong><br>{{ admin_notes }}</div><p>We encourage you to continue your spiritual path and check back for other opportunities.</p><p>Thanks,<br>{{ app_name }}</p>',
                'description' => 'Sent when an admin rejects an order application.',
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(['key' => $template['key']], $template);
        }
    }
}
