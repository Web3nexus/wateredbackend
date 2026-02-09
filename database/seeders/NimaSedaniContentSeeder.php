<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tradition;
use App\Models\TextCollection;
use App\Models\Chapter;
use App\Models\Entry;
use Illuminate\Support\Str;

class NimaSedaniContentSeeder extends Seeder
{
    public function run(): void
    {
        $tradition = Tradition::where('slug', 'nima-sedani')->first();

        if (!$tradition) {
            return;
        }

        $collection = TextCollection::updateOrCreate(
            ['slug' => 'the-holy-book-of-the-reminder', 'tradition_id' => $tradition->id],
            [
                'name' => 'The Holy Book of The Reminder',
                'description' => 'The foundational sacred text of the Nima Sedani tradition.',
                'is_active' => true,
                'order' => 1,
            ]
        );

        $chapter = Chapter::updateOrCreate(
            ['number' => 1, 'collection_id' => $collection->id],
            [
                'name' => 'The Beginning of Wisdom',
                'description' => 'First revelations of the Reminder.',
                'is_active' => true,
                'order' => 1,
            ]
        );

        $verses = [
            "True wisdom begins with the recognition of the Voice that speaks from the depths of the Great Sea.",
            "The heart that listens to the whispers of the ancients shall never find itself lost in the desert of ignorance.",
            "Life is but a reflection of the light that emanates from the Source of all Voices.",
            "Seek first the stillness of the water, for in calmness, the truth reveals its countenance.",
            "Every breath is a sacred verse in the long poem of existence; breathe mindfully.",
            "The God of Seas and Voices guides those who acknowledge the current of divine will.",
            "Knowledge is a vessel, but wisdom is the compass that guides it across the turbulent oceans of life.",
            "Honor the echoes of the past, for they carry the blueprints of the future.",
            "Strength is found not in the rigidity of the rock, but in the persistent flow of the stream.",
            "To know oneself is to know the smallest drop and the entire ocean simultaneously.",
        ];

        foreach ($verses as $index => $text) {
            Entry::updateOrCreate(
                ['chapter_id' => $chapter->id, 'number' => $index + 1],
                [
                    'text' => $text,
                    'is_active' => true,
                    'order' => $index + 1,
                ]
            );
        }
    }
}
