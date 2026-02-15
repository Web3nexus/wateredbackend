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
                ->orWhere('name', 'LIKE', '%Nima Sedani%')
                ->orWhere('name', 'LIKE', '%ni maseani%')
                ->first();

            if ($nimasedani) {
                // Get all active entries for Nima Sedani
                // Joining through collections and chapters
                $query = \App\Models\Entry::where('is_active', true)
                    ->whereHas('chapter', function ($q) {
                        $q->where('is_active', true);
                    })
                    ->whereHas('chapter.collection', function ($q) use ($nimasedani) {
                        $q->where('tradition_id', $nimasedani->id)
                            ->where('is_active', true);
                    });

                $totalEntries = $query->count();

                if ($totalEntries > 0) {
                    // Deterministic selection: one per day
                    $dayOfYear = (int) date('z'); // 0 to 365
                    $offset = $dayOfYear % $totalEntries;

                    $entry = $query->orderBy('id')
                        ->offset($offset)
                        ->first();

                    if ($entry) {
                        // Get fallback background image - use image_url instead of deity_image_url
                        $backgroundImage = $nimasedani->image_url ?? null;

                        if (!$backgroundImage) {
                            $fallbackWisdom = \App\Models\DailyWisdom::whereNotNull('background_image_url')->inRandomOrder()->first();
                            $backgroundImage = $fallbackWisdom?->background_image_url ?? 'https://images.unsplash.com/photo-1505118380757-91f5f5632de0?auto=format&fit=crop&q=80';
                        }

                        // Format the entry as daily wisdom
                        return response()->json([
                            'data' => [
                                'id' => $entry->id,
                                'quote' => $entry->text,
                                'author' => 'Nima Sedani',
                                'background_image_url' => $backgroundImage,
                                'active_date' => now()->toDateString(),
                                'is_active' => true,
                                'publish_date' => now()->toDateString(),
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
