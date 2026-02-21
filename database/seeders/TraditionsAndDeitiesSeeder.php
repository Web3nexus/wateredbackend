<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tradition;
use App\Models\Deity;
use App\Models\Language;

class TraditionsAndDeitiesSeeder extends Seeder
{
    public function run(): void
    {
        $language = Language::where('code', 'en')->first() ?? Language::create([
            'name' => 'English',
            'code' => 'en',
            'is_active' => true,
            'is_default' => true
        ]);

        $traditions = [
            [
                'name' => 'Yoruba',
                'slug' => 'yoruba',
                'description' => 'The Orisha tradition of the Yoruba people, emphasizing the connection between the human and divine through nature and ancestor veneration.',
                'origin' => 'West Africa (Nigeria, Benin, Togo)',
                'deities' => [
                    [
                        'name' => 'Olodumare',
                        'domains' => 'Supreme Creator, Source of Existence',
                        'symbols' => 'The Sun, White Cloth',
                        'mythology' => 'Olodumare is the supreme being in the Yoruba pantheon, the source of all ase (vital energy). He is too vast and remote for direct worship, so he created the Orishas to act as intermediaries.'
                    ],
                    [
                        'name' => 'Obatala',
                        'domains' => 'Creation, Purity, Wisdom, Peace',
                        'symbols' => 'White cloth, Lead, Silver, Snail shell',
                        'mythology' => 'Obatala was tasked by Olodumare to create the physical world and mold the human form. He represents the highest ethical standards and the power of the mind.'
                    ],
                    [
                        'name' => 'Oshun',
                        'domains' => 'Love, Intimacy, Beauty, Wealth, Fresh Waters',
                        'symbols' => 'Gold, Honey, Mirrors, Peacocks, Fans',
                        'mythology' => 'Oshun is the Orisha of the river. She is known for her sweetness and beauty, but she is also a powerful diplomat who once saved the world by bringing back the rain.'
                    ],
                    [
                        'name' => 'Shango',
                        'domains' => 'Thunder, Lightning, Justice, Masculinity',
                        'symbols' => 'Double-headed axe (Oshe), Red and White beads',
                        'mythology' => 'A historical king of Oyo turned deity, Shango represents the raw power of nature and the pursuit of divine justice.'
                    ],
                    [
                        'name' => 'Ogun',
                        'domains' => 'Iron, War, Technology, Creative Destruction',
                        'symbols' => 'Machete, Iron tools, Dog',
                        'mythology' => 'Ogun is the pioneer who cleared the path for the other Orishas to enter the physical world. He is the master of all crafts involving metal.'
                    ],
                    [
                        'name' => 'Yemaya',
                        'domains' => 'The Ocean, Motherhood, Protection',
                        'symbols' => 'Cowrie shells, Blue and White beads, Seven skirts',
                        'mythology' => 'Known as the Mother of All, Yemaya rules the oceans and represents the nurturing, protective power of the sea.'
                    ],
                    [
                        'name' => 'Eshu (Eleggua)',
                        'domains' => 'Crossroads, Messenger, Trickster, Divine Justice',
                        'symbols' => 'Hooked staff (Garabato), Key, Laterite stone',
                        'mythology' => 'Eshu stands at the crossroads of life, testing human character and carrying prayers to the other Orishas.'
                    ]
                ]
            ],
            [
                'name' => 'Igbo (Odinani)',
                'slug' => 'igbo',
                'description' => 'The traditional spiritual belief system of the Igbo people of southeastern Nigeria, centered on Chukwu and the concept of Chi (personal god).',
                'origin' => 'Southeast Nigeria',
                'deities' => [
                    [
                        'name' => 'Chukwu',
                        'domains' => 'Supreme Being, Infinite Wisdom, Creator',
                        'symbols' => 'The Sun (Anyanwu), White Chalk',
                        'mythology' => 'Chukwu is the high god, the creator of the universe and everything in it. He is often associated with the sun as Anyanwu Chukwu.'
                    ],
                    [
                        'name' => 'Ala',
                        'domains' => 'Earth Goddess, Morality, Fertility',
                        'symbols' => 'The Earth, Crescent Moon, Python',
                        'mythology' => 'Ala is the most important Alusi (spirit) in Igbo tradition. She is the mother of the earth, the guardian of morality, and the judge of all human actions.'
                    ],
                    [
                        'name' => 'Amadioha',
                        'domains' => 'Thunder, Lightning, Divine Justice',
                        'symbols' => 'White Ram, Lightning',
                        'mythology' => 'Amadioha is the god of thunder and lightning, often seen as the enforcer of Ala\'s laws. He represents the masculine principle of justice.'
                    ],
                    [
                        'name' => 'Ikenga',
                        'domains' => 'Success, Achievement, Strength, Industry',
                        'symbols' => 'Ram horns, Carved wooden figure with horns',
                        'mythology' => 'Ikenga is a personal Alusi that represents a person\'s right hand, signifying their strength and ability to achieve success through hard work.'
                    ],
                    [
                        'name' => 'Anyanwu',
                        'domains' => 'Light, Truth, Sun',
                        'symbols' => 'The Sun',
                        'mythology' => 'Anyanwu is the solar deity, representing the eye of the creator. She brings light, truth, and life to the world.'
                    ],
                    [
                        'name' => 'Agwu Nsi',
                        'domains' => 'Divination, Healing, Creativity, Chaos',
                        'symbols' => 'Turtle, Divination beads',
                        'mythology' => 'Agwu is the Alusi of health and divination. He is a complex spirit who can bring both madness and profound prophetic insight.'
                    ]
                ]
            ],
            [
                'name' => 'Watered',
                'slug' => 'kemet',
                'description' => 'The spiritual path of the Nile Valley, focusing on Universal Order and the cycle of renewal.',
                'origin' => 'Nile Valley',
                'deities' => [
                    [
                        'name' => 'Ra',
                        'domains' => 'Sun, Creation, Eternal Life',
                        'symbols' => 'Solar Disk, Falcon, Obelisk',
                        'mythology' => 'The sun god and king of the deities. He travels the sky in his solar barque, renewing life daily.'
                    ],
                    [
                        'name' => 'Rezi',
                        'domains' => 'Magic, Protection, Healing, Motherhood',
                        'symbols' => 'Throne, Wings, Tyet knot',
                        'mythology' => 'The great enchantress who represents the power of love and devotion.'
                    ],
                    [
                        'name' => 'Tiurakh',
                        'domains' => 'Afterlife, Resurrection, Agriculture',
                        'symbols' => 'Crook and Flail, Atef crown, Djed pillar',
                        'mythology' => 'The lord of the dead and judge of souls. His resurrection symbolizes the eternal cycle of life.'
                    ],
                    [
                        'name' => 'Sango',
                        'domains' => 'Kingship, Sky, Divine Justice',
                        'symbols' => 'Eye of Horus (Wedjat), Falcon, Double Crown',
                        'mythology' => 'The son of Rezi and Tiurakh, who reflects the ideal leader.'
                    ],
                    [
                        'name' => 'Odomankoma',
                        'domains' => 'Wisdom, Writing, Science, Moon',
                        'symbols' => 'Ibis, Baboon, Stylus, Writing tablet',
                        'mythology' => 'The scribe of the gods who recorded the judgments of the heart. He is the master of all knowledge.'
                    ],
                    [
                        'name' => 'Ovia',
                        'domains' => 'Truth, Balance, Order, Harmony',
                        'symbols' => 'Ostrich Feather, Scales',
                        'mythology' => 'The personification of the cosmic order. The "Weighing of the Heart" ceremony is based on these principles.'
                    ],
                    [
                        'name' => 'Inkosazana',
                        'domains' => 'Embalming, Funerals, Protection of Graves',
                        'symbols' => 'Jackal, Ankh, Flail',
                        'mythology' => 'The guardian of the scales in the underworld, he ensures the proper passage of souls to the afterlife.'
                    ]
                ]
            ],
            [
                'name' => 'Akan',
                'slug' => 'akan',
                'description' => 'The spiritual system of the Akan people, centered on the supreme Nyame and the Nana Nom (venerated ancestors).',
                'origin' => 'Ghana and Ivory Coast',
                'deities' => [
                    [
                        'name' => 'Nyame',
                        'domains' => 'Supreme Creator, Sky God, Omnipotence',
                        'symbols' => 'Gye Nyame (Adinkra symbol), Stool',
                        'mythology' => 'Nyame is the sky god who created the world. "Gye Nyame" means "Except for God," indicating his ultimate power over all.'
                    ],
                    [
                        'name' => 'Asase Yaa',
                        'domains' => 'Earth Mother, Fertility, Truth',
                        'symbols' => 'Agricultural tools, The Earth',
                        'mythology' => 'Asase Yaa is the sustainer of life. Unlike Nyame, she has no temples; instead, the entire earth is her shrine.'
                    ],
                    [
                        'name' => 'Anansi',
                        'domains' => 'Wisdom, Stories, Trickster',
                        'symbols' => 'Spider, Web',
                        'mythology' => 'Kwaku Anansi is the spider-spirit who owns all the worlds\' stories and teaches through cunning and humor.'
                    ],
                    [
                        'name' => 'Tano',
                        'domains' => 'Nature, Rivers, War',
                        'symbols' => 'The Tano River',
                        'mythology' => 'Tano is one of the most powerful "Abosom" (nature spirits) and a son of Nyame, associated with the Tano River.'
                    ]
                ]
            ],
            [
                'name' => 'Zulu',
                'slug' => 'zulu',
                'description' => 'The rich Bantu spiritual heritage of the Zulu people, emphasizing the ancestors (Amadlozi) and the harmony of life.',
                'origin' => 'Southern Africa (South Africa)',
                'deities' => [
                    [
                        'name' => 'Unkulunkulu',
                        'domains' => 'The First One, Great Ancestor',
                        'symbols' => 'Reed (Ohlanga)',
                        'mythology' => 'Unkulunkulu is the first ancestor of the human race. He emerged from a bed of reeds and created the people and the animals.'
                    ],
                    [
                        'name' => 'Nomkhubulwane',
                        'domains' => 'Nature, Rain, Agriculture, Change',
                        'symbols' => 'Rainbow, Mist, Corn',
                        'mythology' => 'The Princess of Heaven, she is the mother of nature and the one who brings rain and ensures a good harvest.'
                    ],
                    [
                        'name' => 'Uthlanga',
                        'domains' => 'The Cosmic Source, Primordial Water',
                        'symbols' => 'Water, Reeds',
                        'mythology' => 'The source from which all life emerged, often conceptualized as a great bed of reeds in the primordial waters.'
                    ],
                    [
                        'name' => 'Mamlambo',
                        'domains' => 'Rivers, Brains, Riches',
                        'symbols' => 'Giant Serpent/Fish',
                        'mythology' => 'The "Goddess of Rivers," a powerful and often feared deity associated with the deep waters and the acquisition of wealth.'
                    ]
                ]
            ],
            [
                'name' => 'Dogon',
                'slug' => 'dogon',
                'description' => 'A complex astronomical and spiritual system based on Deep Knowledge of Sirius and the Nommo spirits.',
                'origin' => 'Mali',
                'deities' => [
                    [
                        'name' => 'Amma',
                        'domains' => 'Supreme Creator, The Cosmic Egg',
                        'symbols' => 'Egg of the World, Vibrations',
                        'mythology' => 'Amma created the universe from his infinite potential, vibrating the basic elements within the cosmic egg.'
                    ],
                    [
                        'name' => 'Nommo',
                        'domains' => 'Water, Life, Wisdom, Messengers from the Stars',
                        'symbols' => 'Amphibious forms, The Sirius Star system',
                        'mythology' => 'The Nommo are ancestral spirits who descended to Earth to bring wisdom and order. They are masters of water and speech.'
                    ],
                    [
                        'name' => 'Lebe',
                        'domains' => 'Agriculture, Resurrection, Earth',
                        'symbols' => 'Serpent',
                        'mythology' => 'The primordial ancestor who sacrificed himself and was resurrected, teaching the Dogon the secrets of the grain.'
                    ]
                ]
            ],
            [
                'name' => 'Hausa',
                'slug' => 'hausa',
                'description' => 'The spiritual traditions of the Hausa people, involving the Bori spirits and the link between humans and the invisible world.',
                'origin' => 'Northern Nigeria and Niger',
                'deities' => [
                    [
                        'name' => 'Inna',
                        'domains' => 'Earth Mother, Fertility, Harvest',
                        'symbols' => 'Grains, Sacred groves',
                        'mythology' => 'The ancient mother of the earth and the source of all agricultural abundance.'
                    ],
                    [
                        'name' => 'Sarkin Baka',
                        'domains' => 'Hunting, Spirits of the Wild',
                        'symbols' => 'Bow and Arrow',
                        'mythology' => 'The master of the hunt and king of the bush spirits, protecting those who venture into the wild.'
                    ]
                ]
            ],
            [
                'name' => 'Ganda (Kintu)',
                'slug' => 'ganda',
                'description' => 'The spiritual path of the Baganda people, focused on the legend of Kintu and the Balubaale spirits.',
                'origin' => 'Uganda',
                'deities' => [
                    [
                        'name' => 'Katonda',
                        'domains' => 'Supreme Creator, Lord of All',
                        'symbols' => 'Sky',
                        'mythology' => 'The ultimate creator god, often referred to as "Lubaale wa Lubaale" (the deity above all deities).'
                    ],
                    [
                        'name' => 'Mukasa',
                        'domains' => 'Lake Victoria, Fertility, Abundance',
                        'symbols' => 'Canoe, Water',
                        'mythology' => 'The most popular Balubaale, he rules the waters of Lake Victoria and provides for the people.'
                    ],
                    [
                        'name' => 'Kibuuka',
                        'domains' => 'War, Protection',
                        'symbols' => 'Shield and Spear',
                        'mythology' => 'The god of war who defended the kingdom, often said to appear in the clouds.'
                    ]
                ]
            ],
            [
                'name' => 'San (Khoisan)',
                'slug' => 'san',
                'description' => 'One of the oldest spiritual traditions, centered on the Mantis creator and the trance dance for healing.',
                'origin' => 'Southern Africa',
                'deities' => [
                    [
                        'name' => '/Kaggen (Cagn)',
                        'domains' => 'Creator, Shape-shifter, Mantis',
                        'symbols' => 'Praying Mantis, Eland',
                        'mythology' => '/Kaggen is the trickster-god who created the world. He often takes the form of a mantis and is the protector of the eland.'
                    ],
                    [
                        'name' => '!Khwa',
                        'domains' => 'Rain, Thunder, Water-Bull',
                        'symbols' => 'Water-Bull, Rain clouds',
                        'mythology' => 'The spirit of the rain, appearing as a powerful water-bull who must be respected to ensure life-giving waters.'
                    ]
                ]
            ],
            [
                'name' => 'Maasai',
                'slug' => 'maasai',
                'description' => 'A monotheistic spiritual path focusing on Enkai and the sacred relationship with cattle and the land.',
                'origin' => 'East Africa (Kenya and Tanzania)',
                'deities' => [
                    [
                        'name' => 'Enkai (Engai)',
                        'domains' => 'Sky God, Rain, Life and Death',
                        'symbols' => 'Black (Good rain) and Red (Drought)',
                        'mythology' => 'Enkai is both feminine and masculine. Enkai Narok (Black God) is benevolent and brings rain, while Enkai Nyokie (Red God) reflects wrath.'
                    ],
                    [
                        'name' => 'Olapa',
                        'domains' => 'The Moon',
                        'symbols' => 'Moon',
                        'mythology' => 'The goddess of the moon, married to Enkai (the sun). Their relationship explains the cycles of the heavens.'
                    ]
                ]
            ]
        ];

        foreach ($traditions as $tData) {
            $deities = $tData['deities'];
            unset($tData['deities']);

            $tradition = Tradition::updateOrCreate(
                ['slug' => $tData['slug']],
                [
                    'name' => ['en' => $tData['name']],
                    'description' => ['en' => $tData['description']],
                    'african_origin' => $tData['origin'],
                    'is_active' => true,
                    'language_id' => $language->id,
                ]
            );

            foreach ($deities as $dData) {
                Deity::updateOrCreate(
                    ['name' => $dData['name'], 'tradition_id' => $tradition->id],
                    [
                        'domains' => $dData['domains'],
                        'symbols' => $dData['symbols'],
                        'mythology_story' => $dData['mythology'],
                        'origin' => $tData['name'],
                    ]
                );
            }
        }
    }
}
