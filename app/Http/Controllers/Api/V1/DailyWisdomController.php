<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DailyWisdom;
use Illuminate\Http\Request;

class DailyWisdomController extends Controller
{
    public function index()
    {
        try {
            // Get Nima Sedani tradition with more robust search
            $nimasedani = \App\Models\Tradition::whereIn('slug', ['nima-sedani', 'nimasedani', 'ni-maseani'])
                ->orWhere('name->en', 'LIKE', '%Nima Sedani%')
                ->orWhere('name', 'LIKE', '%Nima Sedani%')
                ->orWhere('name', 'LIKE', '%ni maseani%')
                ->first();

            if ($nimasedani) {
                // Get all active entries for Nima Sedani
                // Joining through collections and chapters
                $query = \App\Models\Entry::where('entries.is_active', true)
                    ->join('chapters', 'entries.chapter_id', '=', 'chapters.id')
                    ->join('text_collections', 'chapters.collection_id', '=', 'text_collections.id')
                    ->where('text_collections.tradition_id', $nimasedani->id)
                    ->where('chapters.is_active', true)
                    ->where('text_collections.is_active', true)
                    ->select('entries.*');

                $totalEntries = $query->count();

                if ($totalEntries > 0) {
                    // Use date as seed for randomization to ensure 24h consistency
                    $seed = (int) date('Ymd');
                    $entry = $query->with(['chapter'])
                        ->orderByRaw("RAND($seed)")
                        ->first();

                    if ($entry) {
                        // Use deity_image_url for background
                        $backgroundImage = $nimasedani->deity_image_url;

                        if (!$backgroundImage) {
                            $fallbackWisdom = \App\Models\DailyWisdom::whereNotNull('background_image_url')->inRandomOrder()->first();
                            $backgroundImage = $fallbackWisdom?->background_image_url ?? 'https://images.unsplash.com/photo-1505118380757-91f5f5632de0?auto=format&fit=crop&q=80';
                        }

                        // Format the entry as daily wisdom
                        return response()->json([
                            'data' => [
                                'id' => $entry->id,
                                'quote' => strip_tags($entry->text), // Strip HTML tags if any
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

            // Fallback to old behavior if Nimasedani doesn't exist or has no entries
            $wisdom = DailyWisdom::where('is_active', true)
                ->whereDate('active_date', now())
                ->first();

            if (!$wisdom) {
                $wisdom = DailyWisdom::where('is_active', true)
                    ->latest('updated_at')
                    ->first();
            }

            return response()->json(['data' => $wisdom]);
        } catch (\Exception $e) {
            \Log::error('Daily Wisdom Error: ' . $e->getMessage());

            // Return a fallback wisdom
            $fallback = DailyWisdom::where('is_active', true)->latest()->first();

            if ($fallback) {
                return response()->json(['data' => $fallback]);
            }

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
