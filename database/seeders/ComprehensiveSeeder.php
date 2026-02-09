<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tradition;
use App\Models\Ritual;
use App\Models\Incantation;
use App\Models\Deity;
use App\Models\Holiday;
use App\Models\Event;
use App\Models\ConsultationType;
use App\Models\Video;
use App\Models\Audio;
use Carbon\Carbon;

class ComprehensiveSeeder extends Seeder
{
    public function run(): void
    {
        $language = \App\Models\Language::first() ?? \App\Models\Language::create(['name' => 'English', 'code' => 'en', 'is_active' => true, 'is_default' => true]);

        // 1. Seed Traditions (The Ancient Paths)
        $traditionData = [
            ['name' => 'Kemet', 'slug' => 'kemet', 'description' => 'Ancient Egyptian spiritual tradition.'],
            ['name' => 'Kush', 'slug' => 'kush', 'description' => 'Ancient Nubian spiritual heritage.'],
            ['name' => 'Yoruba', 'slug' => 'yoruba', 'description' => 'Traditions of the Orishas from West Africa.'],
            ['name' => 'Igbo', 'slug' => 'igbo', 'description' => 'Odinani - the traditional faith of the Igbo people.'],
            ['name' => 'Akan', 'slug' => 'akan', 'description' => 'Spiritual concepts of the Akan people of Ghana.'],
            ['name' => 'Zulu', 'slug' => 'zulu', 'description' => 'Bantu spiritual heritage from Southern Africa.'],
            ['name' => 'Dogon', 'slug' => 'dogon', 'description' => 'Deep astronomical and spiritual knowledge of the Dogon.'],
            ['name' => 'Sumerian', 'slug' => 'sumerian', 'description' => 'Ancient Mesopotamian spiritual paths.'],
            ['name' => 'Origins', 'slug' => 'origins', 'description' => 'Primary origins of spiritual thought.'],
            ['name' => 'Other', 'slug' => 'other', 'description' => 'Other sacred spiritual paths.'],
        ];

        foreach ($traditionData as $data) {
            Tradition::updateOrCreate(['slug' => $data['slug']], [
                'name' => ['en' => $data['name']],
                'description' => ['en' => $data['description']],
                'is_active' => true,
                'language_id' => $language->id,
            ]);
        }

        $kemetId = Tradition::where('slug', 'kemet')->first()->id;
        $yorubaId = Tradition::where('slug', 'yoruba')->first()->id;
        $dogonId = Tradition::where('slug', 'dogon')->first()->id;
        $akanId = Tradition::where('slug', 'akan')->first()->id;

        // 2. Seed Rituals
        $rituals = [
            [
                'title' => 'Morning Water Ritual',
                'category' => 'Cleansing',
                'description' => 'Water rituals of thanksgiving.',
                'content' => 'A sacred morning practice to thank the Source and cleanse the spirit.',
                'time_of_day' => '05:00:00',
                'steps' => json_encode(['Waking up at dawn', 'Filling a bowl with fresh water', 'Speaking words of gratitude', 'Cleansing hands and face']),
            ],
            [
                'title' => 'Afternoon Libations',
                'category' => 'Ancestral',
                'description' => 'Libations ritual in honor of ancestors.',
                'content' => 'Honoring those who came before us through sacred water offerings.',
                'time_of_day' => '13:00:00',
                'steps' => json_encode(['Preparing the libation vessel', 'Calling the names of ancestors', 'Pouring water onto the earth', 'Praying for guidance']),
            ],
            [
                'title' => 'Night Water Cleansing',
                'category' => 'Blessing',
                'description' => 'Water cleansing ritual in honor of the gods.',
                'content' => 'A nightly practice to release the day\'s energy and receive divine blessings.',
                'time_of_day' => '20:00:00',
                'steps' => json_encode(['Preparing a ritual bath', 'Adding sacred herbs or salts', 'Meditating on the God of Seas', 'Stepping into the water with intention']),
            ],
            [
                'title' => 'Purification Bath',
                'category' => 'Cleansing',
                'description' => 'Full spiritual cleansing.',
                'content' => 'Deep spiritual purification using sacred elements.',
            ],
        ];

        foreach ($rituals as $ritual) {
            Ritual::updateOrCreate(['title' => $ritual['title']], $ritual);
        }

        // 3. Seed Holidays
        $holidays = [
            [
                'name' => 'Festival of Waters',
                'theme' => 'Celebration of spiritual renewal',
                'date' => Carbon::create(2025, 3, 21),
                'description' => 'A major festival celebrating the renewal of life and spirit through water.',
            ],
            [
                'name' => 'Day of Illumination',
                'theme' => 'Honoring the inner light',
                'date' => Carbon::create(2025, 4, 15),
                'description' => 'A day dedicated to self-reflection and honoring the divine spark within.',
            ],
            [
                'name' => 'Harvest of Souls',
                'theme' => 'Gathering of the faithful',
                'date' => Carbon::create(2025, 5, 10),
                'description' => 'A time for community gathering and spiritual harvest.',
            ],
        ];

        foreach ($holidays as $holiday) {
            Holiday::updateOrCreate(['name' => $holiday['name']], $holiday);
        }

        // 4. Seed Incantations
        $incantations = [
            [
                'title' => 'Abundance Flow',
                'category' => 'Prosperity',
                'content' => 'Spoken words to attract wealth and prosperity.',
                'spoken_text' => 'As the river flows to the sea, so does abundance flow to me.',
                'intended_outcome' => 'Attracting financial and spiritual wealth.',
            ],
            [
                'title' => 'Shield of Light',
                'category' => 'Protection',
                'content' => 'A powerful verbal shield against negativity.',
                'spoken_text' => 'Surrounded by light, I am protected. No harm shall touch my soul.',
                'intended_outcome' => 'Protection from external negative energies.',
            ],
            [
                'title' => 'Heart Mending',
                'category' => 'Healing',
                'content' => 'Words for emotional restoration.',
                'spoken_text' => 'Heal the wounds, still the waters, restore the peace within.',
                'intended_outcome' => 'Emotional and spiritual inner healing.',
            ],
        ];

        foreach ($incantations as $inc) {
            Incantation::updateOrCreate(['title' => $inc['title']], $inc);
        }

        // 5. Seed The Gods (Deities)
        $deities = [
            [
                'name' => 'Ra',
                'tradition_id' => $kemetId,
                'origin' => 'Kemet',
                'domains' => 'Sun, Creation, Sovereignty',
                'symbols' => 'Sun disk, Falcon, Obelisk',
                'mythology_story' => 'Ra is the sun god of Ancient Egypt. He travels across the sky in his solar barque, fighting the serpent Apep every night to bring the dawn again.',
            ],
            [
                'name' => 'Isis (Aset)',
                'tradition_id' => $kemetId,
                'origin' => 'Kemet',
                'domains' => 'Magic, Motherhood, Healing',
                'symbols' => 'Throne, Tyet knot, Wings',
                'mythology_story' => 'Isis is the great enchantress who used her magic to resurrect her husband Osiris and birth Horus. She is the protector of the throne.',
            ],
            [
                'name' => 'Oshun',
                'tradition_id' => $yorubaId,
                'origin' => 'Yoruba',
                'domains' => 'Love, Rivers, Prosperity',
                'symbols' => 'Peacock, Mirror, Honey, Gold',
                'mythology_story' => 'Oshun is the Orisha of the river. She is known for her sweetness and beauty, but she is also a powerful diplomat who once saved the world by bringing back the rain.',
            ],
            [
                'name' => 'Shango',
                'tradition_id' => $yorubaId,
                'origin' => 'Yoruba',
                'domains' => 'Thunder, Justice, Masculinity',
                'symbols' => 'Double axe (Oshe), Red and White beads',
                'mythology_story' => 'Shango was the third Alaafin of Oyo. He is the god of lightning and thunder, representing pure energy and divine justice.',
            ],
            [
                'name' => 'Amma',
                'tradition_id' => $dogonId,
                'origin' => 'Dogon',
                'domains' => 'Creation, Universe',
                'symbols' => 'Egg, Vibrations',
                'mythology_story' => 'Amma is the supreme creator deity of the Dogon. He created the cosmos from his infinite potential, beginning with the Egg of the World.',
            ],
            [
                'name' => 'Nyame',
                'tradition_id' => $akanId,
                'origin' => 'Akan',
                'domains' => 'Sky, Creation, Order',
                'symbols' => 'Gye Nyame',
                'mythology_story' => 'Nyame is the omnipotent sky god. His name means "He who knows all and sees all," representing the ultimate source of existence.',
            ],
        ];

        foreach ($deities as $deity) {
            Deity::updateOrCreate(['name' => $deity['name']], $deity);
        }

        // 6. Seed Events
        $events = [
            [
                'title' => 'Osun Festival',
                'category' => 'Sacred Celebration',
                'recurrence' => 'Yearly',
                'cultural_origin' => 'Yoruba',
                'tradition_id' => $yorubaId,
                'description' => 'A week-long cultural and spiritual event honoring the goddess Oshun in Osogbo.',
                'start_time' => Carbon::create(2025, 8, 1, 10, 0, 0),
            ],
            [
                'title' => 'Sacred Water Symposium',
                'category' => 'Symposium',
                'recurrence' => 'Special',
                'cultural_origin' => 'Watered',
                'tradition_id' => $kemetId, // Default to Kemet for now
                'description' => 'A spiritual gathering organized by Watered to discuss the ancient wisdom of the Nile.',
                'start_time' => Carbon::create(2025, 6, 15, 14, 0, 0),
            ],
        ];

        foreach ($events as $event) {
            Event::updateOrCreate(['title' => $event['title']], $event);
        }

        // 7. Seed Consultation Types
        $consultations = [
            ['name' => 'Spiritual Consultation', 'category' => 'Guidance', 'price' => 50.00],
            ['name' => 'Counseling Session', 'category' => 'Emotional', 'price' => 45.00],
            ['name' => 'Divination Reading', 'category' => 'Spiritual', 'price' => 60.00],
            ['name' => 'Ancestral Reading', 'category' => 'Ancestral', 'price' => 65.00],
            ['name' => 'Healing Session', 'category' => 'Healing', 'price' => 75.00],
        ];

        foreach ($consultations as $ctype) {
            ConsultationType::updateOrCreate(['name' => $ctype['name']], $ctype);
        }

        // 8. Update Video/Audio categories for existing items? or just seed a few
        Video::where('title', 'LIKE', '%Teaching%')->update(['category' => 'Teaching']);
        Video::where('title', 'LIKE', '%Music%')->update(['category' => 'Music Video']);

        Audio::where('title', 'LIKE', '%Incantation%')->update(['category' => 'Incantation']);
        Audio::where('title', 'LIKE', '%Sermon%')->update(['category' => 'Sermons']);
    }
}
