<?php

namespace Database\Seeders;

use App\Models\Teaching;
use Illuminate\Database\Seeder;

class TeachingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachings = [
            [
                'title' => 'The Essence of Ancient Wisdom',
                'summary' => 'Understanding the core principles that guided our ancestors towards spiritual clarity.',
                'content' => '<p>Ancient wisdom is not just about the past; it\'s a living philosophy that helps us navigate the complexities of modern life. By studying the traditions and rituals of our predecessors, we uncover truths that are as relevant today as they were thousands of years ago.</p><p>Key principles include:</p><ul><li>Respect for the natural world</li><li>The importance of community and ancestry</li><li>Pursuit of inner peace and balance</li></ul>',
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Modern Spirituality: A Path to Growth',
                'summary' => 'How to integrate ancient practices into a modern lifestyle for continuous spiritual evolution.',
                'content' => '<p>In today\'s fast-paced world, finding time for spiritual reflection can be challenging. However, by making small, consistent efforts to practice mindfulness and engage with our community, we can achieve profound growth.</p><blockquote>"Knowledge is the first step towards transformation."</blockquote><p>Integration strategies:</p><ol><li>Daily meditation and rituals</li><li>Engaging with local and online spiritual communities</li><li>Reflecting on your own spiritual journey</li></ol>',
                'is_published' => true,
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Ancestral Day: A Guide to Rituals',
                'summary' => 'Detailed instructions on how to observe and celebrate Ancestral Day with respect and devotion.',
                'content' => '<p>Ancestral Day is a sacred time to honor those who came before us. This guide provides step-by-step instructions for performing traditional rituals that foster a deep connection with your lineage.</p><p>Preparation is key: ensure you have a quiet space, clean water, and offerings that reflect your ancestors\' preferences.</p>',
                'is_published' => true,
                'published_at' => now()->subDays(5),
            ],
        ];

        foreach ($teachings as $teaching) {
            Teaching::updateOrCreate(
                ['title' => $teaching['title']],
                $teaching
            );
        }
    }
}
