<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;
use App\Models\UserGuide;
use App\Models\GlobalSetting;

class InformationalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed FAQs
        $faqs = [
            [
                'category' => 'Sacred Traditions',
                'question' => 'What is Nimasedani?',
                'answer' => 'Nimasedani is the path of spiritual awareness and connection to the ancient traditions of Africa. It focuses on the harmony between the physical and spiritual realms through rituals, incantations, and ancestral wisdom.',
                'sort_order' => 1,
            ],
            [
                'category' => 'Sacred Traditions',
                'question' => 'How can I connect with my ancestors?',
                'answer' => 'Connection begins with intention. Setting up a sacred space (altar), performing regular libations, and quiet meditation are powerful ways to invite the guidance of your ancestors into your daily life.',
                'sort_order' => 2,
            ],
            [
                'category' => 'Account & Billing',
                'question' => 'What is Watered Plus+?',
                'answer' => 'Watered Plus+ is our premium subscription that unlocks full access to Nima Sedani, advanced rituals, exclusive spiritual events, and the complete library of ancient traditions.',
                'sort_order' => 3,
            ],
            [
                'category' => 'App Usage',
                'question' => 'How do I set ritual reminders?',
                'answer' => 'Go to the "Reminders" section via the Quick Actions on your dashboard or through your profile. You can set specific times for your daily rituals and choose from sacred alarm sounds.',
                'sort_order' => 4,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(
                ['question' => $faq['question']],
                array_merge($faq, ['is_active' => true])
            );
        }

        // 2. Seed User Guides
        $guides = [
            [
                'title' => 'Getting Started with Rituals',
                'content' => 'Begin your journey by selecting a simple morning cleansing ritual. Consistent practice is the key to spiritual growth. Ensure you have a quiet space and the necessary symbolic elements ready.',
                'type' => 'text',
                'sort_order' => 1,
            ],
            [
                'title' => 'Navigating the Sacred Library',
                'content' => 'The Library (Echoes) contains wisdom from various traditions. Use filters to explore specific paths like Watered or Yoruba, or search for topics like "Healing" or "Protection".',
                'type' => 'text',
                'sort_order' => 2,
            ],
            [
                'title' => 'Joining the Community',
                'content' => 'Connect with fellow seekers in the Feed. Share your experiences, ask questions, and support each other on your spiritual paths. Remember to always maintain respect for all traditions.',
                'type' => 'text',
                'sort_order' => 3,
            ],
        ];

        foreach ($guides as $guide) {
            UserGuide::updateOrCreate(
                ['title' => $guide['title']],
                array_merge($guide, ['is_active' => true])
            );
        }

        // 3. Update Legal Documents in GlobalSettings
        $settings = GlobalSetting::first();
        if ($settings) {
            $settings->update([
                'privacy_policy' => '
                    <div class="legal-content">
                        <h2>Privacy Policy</h2>
                        <p>Effective Date: February 10, 2026</p>
                        <p>At Watered, we respect your spiritual journey and your personal data. This policy outlines how we handle your information.</p>
                        <h3>1. Information We Collect</h3>
                        <p>We collect information you provide directly to us when you create an account, such as your name, email, and profile preferences.</p>
                        <h3>2. How We Use Your Information</h3>
                        <p>We use your data to personalize your experience, provide ritual reminders, and facilitate community interactions.</p>
                        <h3>3. Data Protection</h3>
                        <p>Your spiritual data and personal identifiers are stored securely and never sold to third parties.</p>
                    </div>
                ',
                'terms_of_service' => '
                    <div class="legal-content">
                        <h2>Terms of Service</h2>
                        <p>Welcome to Watered. By using our application, you agree to the following terms.</p>
                        <h3>1. Sacred Use</h3>
                        <p>The content provided is for spiritual and educational purposes. We encourage respectful exploration of all traditions.</p>
                        <h3>2. Community Guidelines</h3>
                        <p>Harassment, hate speech, or disrespect towards any tradition or user will result in account suspension.</p>
                        <h3>3. Subscription Services</h3>
                        <p>Watered Plus+ provides access to premium content. Subscriptions can be managed via your device settings.</p>
                    </div>
                ',
            ]);
        }
    }
}
