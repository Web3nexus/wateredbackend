<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DailyWisdomController extends Controller
{
    public function index()
    {
        try {
            // Priority: Explicitly fetch from TextCollection ID 1 (Nima Sedani as per user)
            $collection = \App\Models\TextCollection::find(1);

            // Fallback: If ID 1 is missing, search by name
            if (!$collection) {
                $collection = \App\Models\TextCollection::where('name', 'LIKE', '%Nima Sedani%')
                    ->orWhere('name', 'LIKE', '%ni maseani%')
                    ->first();
            }

            if ($collection) {
                // Get all active entries for this collection through its chapters
                $query = \App\Models\Entry::where('entries.is_active', true)
                    ->join('chapters', 'entries.chapter_id', '=', 'chapters.id')
                    ->where('chapters.collection_id', $collection->id)
                    ->where('chapters.is_active', true)
                    ->select('entries.*');

                $totalEntries = $query->count();

                if ($totalEntries > 0) {
                    // Use date as seed for randomization to ensure 24h consistency
                    $seed = (int) date('Ymd');
                    $entry = $query->with(['chapter'])
                        ->orderByRaw("RAND($seed)")
                        ->first();

                    if ($entry) {
                        // Background image logic
                        $backgroundImage = $collection->background_image_url;

                        // If collection doesn't have one, try its tradition
                        if (!$backgroundImage && $collection->tradition_id) {
                            $tradition = \App\Models\Tradition::find($collection->tradition_id);
                            $backgroundImage = $tradition?->deity_image_url;
                        }

                        if (!$backgroundImage) {
                            $backgroundImage = 'https://images.unsplash.com/photo-1505118380757-91f5f5632de0?auto=format&fit=crop&q=80';
                        }

                        // Format the entry as daily wisdom
                        return response()->json([
                            'data' => [
                                'id' => $entry->id,
                                'quote' => strip_tags($entry->text),
                                'author' => 'Nima Sedani',
                                'chapter_number' => $entry->chapter?->order ?? 1,
                                'verse_number' => $entry->order ?? 1,
                                'background_image_url' => $backgroundImage,
                                'active_date' => now()->toDateString(),
                                'publish_date' => now()->toDateString(),
                                'is_active' => true,
                                'citation' => "Chapter " . ($entry->chapter?->order ?? 1) . ":" . ($entry->order ?? 1),
                            ]
                        ]);
                    }
                }
            }

            // Ultimate fallback if no Nima Sedani entries are found
            return response()->json([
                'data' => [
                    'id' => 0,
                    'quote' => 'The God of Seas and Voices guides those who acknowledge the current of divine will.',
                    'author' => 'Nima Sedani',
                    'background_image_url' => 'https://images.unsplash.com/photo-1505118380757-91f5f5632de0?auto=format&fit=crop&q=80',
                    'active_date' => now()->toDateString(),
                    'is_active' => true,
                    'publish_date' => now()->toDateString(),
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Daily Wisdom Error: ' . $e->getMessage());

            // Ultimate fallback
            return response()->json([
                'data' => [
                    'id' => 0,
                    'quote' => 'The God of Seas and Voices guides those who acknowledge the current of divine will.',
                    'author' => 'Nima Sedani',
                    'background_image_url' => 'https://images.unsplash.com/photo-1505118380757-91f5f5632de0?auto=format&fit=crop&q=80',
                    'active_date' => now()->toDateString(),
                    'is_active' => true,
                    'publish_date' => now()->toDateString(),
                ]
            ]);
        }
    }
}
