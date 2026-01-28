<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // FAQ Content
        DB::table('faqs')->insert([
            [
                'question' => 'What is Watered?',
                'answer' => 'Watered is a spiritual platform dedicated to ancient African spirituality, providing access to sacred texts, teachings, rituals, and community connection.',
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'How do I access premium content?',
                'answer' => 'Premium content is available through our subscription service. Tap "GET PLUS+" to subscribe and unlock exclusive teachings, audio, video content, and more.',
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Can I book a consultation?',
                'answer' => 'Yes! Navigate to the Consultation tab to view available consultation types and book a session with our spiritual guides.',
                'order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'How do I find temples near me?',
                'answer' => 'Visit the Temple Discovery section in your Profile to find temples and spiritual centers near your location.',
                'order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept Stripe for international payments, and Paystack/Flutterwave for Nigerian users. All major cards are supported.',
                'order' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Privacy Policy
        DB::table('settings')->updateOrInsert(
            ['key' => 'privacy_policy'],
            [
                'value' => '<h1>Privacy Policy</h1>
<p>Last updated: ' . now()->format('F d, Y') . '</p>

<h2>1. Information We Collect</h2>
<p>We collect information you provide directly to us, including your name, email address, profile information, and payment details when you use our services.</p>

<h2>2. How We Use Your Information</h2>
<p>We use the information we collect to:</p>
<ul>
<li>Provide, maintain, and improve our services</li>
<li>Process your transactions and send related information</li>
<li>Send you technical notices and support messages</li>
<li>Respond to your comments and questions</li>
</ul>

<h2>3. Information Sharing</h2>
<p>We do not sell your personal information. We may share your information with service providers who assist us in operating our platform.</p>

<h2>4. Data Security</h2>
<p>We implement appropriate security measures to protect your personal information.</p>

<h2>5. Your Rights</h2>
<p>You have the right to access, update, or delete your personal information at any time through your account settings.</p>

<h2>6. Contact Us</h2>
<p>If you have questions about this Privacy Policy, please contact us through the app.</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Terms of Service
        DB::table('settings')->updateOrInsert(
            ['key' => 'terms_of_service'],
            [
                'value' => '<h1>Terms of Service</h1>
<p>Last updated: ' . now()->format('F d, Y') . '</p>

<h2>1. Acceptance of Terms</h2>
<p>By accessing and using Watered, you accept and agree to be bound by these Terms of Service.</p>

<h2>2. Use of Service</h2>
<p>You agree to use our service only for lawful purposes and in accordance with these Terms.</p>

<h2>3. User Accounts</h2>
<p>You are responsible for maintaining the confidentiality of your account and password.</p>

<h2>4. Subscriptions</h2>
<p>Premium subscriptions are billed on a recurring basis. You may cancel at any time.</p>

<h2>5. Intellectual Property</h2>
<p>All content on Watered, including texts, graphics, logos, and software, is the property of Watered or its content suppliers.</p>

<h2>6. Prohibited Conduct</h2>
<p>You may not use the service to harass, abuse, or harm others, or to violate any applicable laws.</p>

<h2>7. Termination</h2>
<p>We reserve the right to terminate or suspend your account at our discretion.</p>

<h2>8. Changes to Terms</h2>
<p>We may modify these terms at any time. Continued use of the service constitutes acceptance of modified terms.</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // User Guide
        DB::table('settings')->updateOrInsert(
            ['key' => 'user_guide'],
            [
                'value' => '<h1>User Guide</h1>

<h2>Getting Started</h2>
<p>Welcome to Watered! This guide will help you navigate our platform and make the most of your spiritual journey.</p>

<h2>Dashboard</h2>
<p>Your home screen shows:</p>
<ul>
<li><strong>Daily Wisdom</strong>: Receive daily spiritual guidance</li>
<li><strong>Featured Teachings</strong>: Explore curated content</li>
<li><strong>Quick Actions</strong>: Access Rituals, Incantations, Consultations, and Events</li>
</ul>

<h2>Library</h2>
<p>Browse our extensive collection of:</p>
<ul>
<li><strong>Scriptures</strong>: Sacred texts organized by tradition (Igbo, Yoruba, Kemetic, etc.)</li>
<li><strong>Audio Teachings</strong>: Listen to spiritual lessons</li>
<li><strong>Video Content</strong>: Watch teachings in TikTok-style format</li>
</ul>

<h2>Consultations</h2>
<p>Book one-on-one sessions with spiritual guides. Choose your consultation type and schedule a time that works for you.</p>

<h2>Shop</h2>
<p>Purchase spiritual items, books, and ritual supplies. Prices are automatically shown in your local currency.</p>

<h2>Profile</h2>
<p>Manage your account, view bookings, access Temple Discovery, and join the Community.</p>

<h2>Premium Subscription</h2>
<p>Unlock exclusive content, ad-free experience, and priority booking by subscribing to Watered Plus+.</p>

<h2>Need Help?</h2>
<p>Visit the FAQ section or contact support through the app.</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
