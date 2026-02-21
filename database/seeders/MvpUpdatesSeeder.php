<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Holiday;
use App\Models\Deity;
use App\Models\Ritual;
use App\Models\Incantation;
use App\Models\Tradition;
use Carbon\Carbon;

class MvpUpdatesSeeder extends Seeder
{
    public function run(): void
    {
        // Seed Holidays
        $holidays = [
            [
                'name' => 'Festival of Waters',
                'theme' => 'Spiritual Renewal',
                'date' => Carbon::create(2025, 3, 21),
                'description' => 'A sacred celebration of spiritual cleansing and renewal through water ceremonies.',
            ],
            [
                'name' => 'Day of Illumination',
                'theme' => 'Honoring Inner Light',
                'date' => Carbon::create(2025, 4, 15),
                'description' => 'A day dedicated to honoring the divine light within each soul.',
            ],
            [
                'name' => 'Harvest of Souls',
                'theme' => 'Gathering of the Faithful',
                'date' => Carbon::create(2025, 5, 10),
                'description' => 'A celebration of spiritual growth and community gathering.',
            ],
        ];

        foreach ($holidays as $holiday) {
            Holiday::updateOrCreate(['name' => $holiday['name']], $holiday);
        }

        // Get traditions for deities
        $english = \App\Models\Language::firstOrCreate(
            ['code' => 'en'],
            [
                'name' => 'English',
                'is_default' => true,
                'is_active' => true,
            ]
        );

        $kemetTradition = Tradition::updateOrCreate(['slug' => 'kemet'], [
            'name' => ['en' => 'Watered'],
            'description' => ['en' => 'The spiritual path of the Nile Valley.'],
            'language_id' => $english->id,
            'is_active' => true,
        ]);

        $yorubaTradition = Tradition::updateOrCreate(['slug' => 'yoruba'], [
            'name' => ['en' => 'Yoruba'],
            'description' => ['en' => 'West African spiritual tradition'],
            'language_id' => $english->id,
            'is_active' => true,
        ]);

        // Seed Deities
        $deities = [
            [
                'name' => 'Oshun',
                'origin' => 'Yoruba',
                'tradition_id' => $yorubaTradition->id,
                'description' => 'Goddess of love, beauty, and rivers',
                'mythology_story' => 'Oshun is the Yoruba goddess of love, intimacy, beauty, wealth and diplomacy. She is associated with rivers and fresh water, and is known for her beauty, grace, and sensuality.',
                'symbols' => 'Rivers, mirrors, honey, peacock feathers',
                'domains' => 'Love, Beauty, Fertility, Rivers',
                'sacred_elements' => 'Water, Gold, Honey',
            ],
            [
                'name' => 'Odomankoma',
                'origin' => 'Watered',
                'tradition_id' => $kemetTradition->id,
                'description' => 'God of wisdom, writing, and magic',
                'mythology_story' => 'Odomankoma is the ancient Nile Valley god of wisdom, writing, hieroglyphs, science, magic, art, and judgment. He is often depicted with the head of an ibis.',
                'symbols' => 'Ibis, Moon, Writing palette',
                'domains' => 'Wisdom, Writing, Magic, Moon',
                'sacred_elements' => 'Papyrus, Ink, Moonlight',
            ],
            [
                'name' => 'Yemoja',
                'origin' => 'Yoruba',
                'tradition_id' => $yorubaTradition->id,
                'description' => 'Mother of all Orishas, goddess of the ocean',
                'mythology_story' => 'Yemoja is the Yoruba goddess of the ocean and motherhood. She is the mother of all Orishas and is associated with the moon, water, and feminine mysteries.',
                'symbols' => 'Ocean waves, cowrie shells, fish',
                'domains' => 'Ocean, Motherhood, Protection, Fertility',
                'sacred_elements' => 'Seawater, Shells, Pearls',
            ],
        ];

        foreach ($deities as $deity) {
            Deity::updateOrCreate(['name' => $deity['name']], $deity);
        }

        // Seed Rituals
        $rituals = [
            [
                'title' => 'Morning Water Ritual',
                'category' => 'Cleansing',
                'description' => 'A daily morning ritual for spiritual cleansing and thanksgiving',
                'content' => 'Begin your day with gratitude and spiritual cleansing through sacred water.',
                'steps' => json_encode([
                    'Fill a clean bowl with fresh water',
                    'Face the rising sun',
                    'Speak words of thanksgiving',
                    'Wash your hands and face with the water',
                    'Pour remaining water onto the earth',
                ]),
                'time_of_day' => '05:00:00',
                'symbolic_meaning' => 'Represents spiritual cleansing and the renewal of divine connection each morning.',
            ],
            [
                'title' => 'Afternoon Libations',
                'category' => 'Ancestral',
                'description' => 'Honoring ancestors through water offerings',
                'content' => 'Connect with your ancestors through sacred water libations.',
                'steps' => json_encode([
                    'Prepare clean water in a sacred vessel',
                    'Call upon your ancestors by name',
                    'Pour water onto the earth slowly',
                    'Speak your prayers and requests',
                    'Give thanks for ancestral guidance',
                ]),
                'time_of_day' => '13:00:00',
                'symbolic_meaning' => 'Maintains connection with ancestral spirits and honors their wisdom.',
            ],
            [
                'title' => 'Night Water Cleansing',
                'category' => 'Blessing',
                'description' => 'Evening ritual honoring The Gods',
                'content' => 'End your day with spiritual cleansing and honoring The Gods.',
                'steps' => json_encode([
                    'Prepare a bath or basin of water',
                    'Add sacred herbs if available',
                    'Cleanse your body mindfully',
                    'Speak prayers to The Gods',
                    'Release the day\'s burdens',
                ]),
                'time_of_day' => '20:00:00',
                'symbolic_meaning' => 'Releases negative energy and prepares the spirit for rest and renewal.',
            ],
            [
                'title' => 'Ancestral Libation',
                'category' => 'Ancestral',
                'description' => 'Traditional water offering to ancestors',
                'content' => 'Honor your ancestors with sacred water offerings.',
            ],
            [
                'title' => 'Purification Bath',
                'category' => 'Cleansing',
                'description' => 'Full spiritual cleansing ritual',
                'content' => 'Deep spiritual cleansing through sacred bathing.',
            ],
        ];

        foreach ($rituals as $ritual) {
            Ritual::updateOrCreate(['title' => $ritual['title']], $ritual);
        }

        // Seed Incantations
        $incantations = [
            [
                'title' => 'Abundance Flow',
                'category' => 'Prosperity',
                'description' => 'Incantation for attracting abundance and prosperity',
                'content' => 'Sacred words to open the flow of abundance in your life.',
                'spoken_text' => 'Waters of abundance, flow to me. Prosperity and wealth, I decree. By the power of The Gods, so shall it be.',
                'intended_outcome' => 'Attracts financial abundance and opens pathways to prosperity.',
            ],
            [
                'title' => 'Shield of Light',
                'category' => 'Protection',
                'description' => 'Protective incantation against negative energy',
                'content' => 'Create a shield of divine protection around yourself.',
                'spoken_text' => 'Light of The Gods, surround me now. No harm shall pass, this I avow. Protected and safe, in divine light I bow.',
                'intended_outcome' => 'Creates a protective barrier against negative energies and harm.',
            ],
            [
                'title' => 'Heart Mending',
                'category' => 'Healing',
                'description' => 'Incantation for emotional and spiritual healing',
                'content' => 'Heal emotional wounds and restore inner peace.',
                'spoken_text' => 'Waters of healing, wash over me. Mend my heart, set my spirit free. By The Gods\' grace, whole I shall be.',
                'intended_outcome' => 'Facilitates emotional healing and restoration of inner peace.',
            ],
            [
                'title' => 'Wisdom\'s Call',
                'category' => 'Wisdom',
                'description' => 'Invocation for divine wisdom and clarity',
                'content' => 'Call upon divine wisdom for guidance.',
                'spoken_text' => 'Ancient wisdom, hear my call. Guide my steps, lest I fall. Clarity and truth, reveal to me all.',
                'intended_outcome' => 'Opens the mind to divine wisdom and spiritual clarity.',
            ],
            [
                'title' => 'Peace Within',
                'category' => 'Peace',
                'description' => 'Incantation for inner peace and tranquility',
                'content' => 'Find deep inner peace and calm.',
                'spoken_text' => 'Still waters run deep within my soul. Peace and calm, make me whole. Tranquility reigns, this is my goal.',
                'intended_outcome' => 'Brings deep inner peace and emotional tranquility.',
            ],
        ];

        foreach ($incantations as $incantation) {
            Incantation::updateOrCreate(['title' => $incantation['title']], $incantation);
        }
    }
}
