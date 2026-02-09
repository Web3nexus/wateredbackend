<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DailyWisdom;
use Illuminate\Http\Request;

class DailyWisdomController extends Controller
{
    public function index()
    {
        // Get Nimasedani tradition
        $nimasedani = \App\Models\Tradition::where('slug', 'nima-sedani')->first();

        if ($nimasedani) {
            // Get all active entries for Nima Sedani
            // Joining through collections and chapters
            $query = \App\Models\Entry::where('is_active', true)
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
                    // Format the entry as daily wisdom
                    return response()->json([
                        'data' => [
                            'id' => $entry->id,
                            'quote' => $entry->text,
                            'author' => 'Nima Sedani', // User spelled it "ni maseani" but tradition is "Nima Sedani"
                            'background_image_url' => null, // We can add logic for backgrounds later
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
    }
}
