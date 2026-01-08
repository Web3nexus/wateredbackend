<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tradition;
use App\Models\Language;
use Illuminate\Support\Str;

class FeatureTwoSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();

        if (!$english) {
            $english = Language::create([
                'name' => 'English',
                'code' => 'en',
                'is_default' => true,
                'is_active' => true,
            ]);
        }

        $traditions = [
            [
                'name' => ['en' => 'Nima Sedani'],
                'slug' => 'nima-sedani',
                'description' => ['en' => 'The Holy Book of The Reminder. Containing the sacred verses that guide humanity first towards the God of Seas & Voices.'],
                'language_id' => $english->id,
            ],
            [
                'name' => ['en' => 'Ancient Spirits'],
                'slug' => 'ancient-spirits',
                'description' => ['en' => 'Sacred teachings from the Hidorian Family regarding life, growth, and the rejection of witchcraft.'],
                'language_id' => $english->id,
            ],
            [
                'name' => ['en' => 'The Four Witnesses'],
                'slug' => 'the-four-witnesses',
                'description' => ['en' => 'The foundational proofs behind the existence and power of the Ancient Spirits in secular and spiritual realms.'],
                'language_id' => $english->id,
            ],
        ];

        foreach ($traditions as $data) {
            Tradition::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
