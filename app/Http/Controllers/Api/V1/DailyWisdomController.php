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

        if (!$nimasedani) {
            // Fallback to old behavior if Nimasedani doesn't exist
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

        // Get a random entry from Nimasedani
        $entry = \App\Models\Entry::whereHas('chapter.collection', function ($query) use ($nimasedani) {
            $query->where('tradition_id', $nimasedani->id);
        })
            ->where('is_active', true)
            ->inRandomOrder()
            ->first();

        if (!$entry) {
            // Fallback to old behavior if no entries found
            $wisdom = DailyWisdom::where('is_active', true)
                ->latest('updated_at')
                ->first();

            return response()->json(['data' => $wisdom]);
        }

        // Format the entry as daily wisdom
        $wisdom = [
            'id' => $entry->id,
            'quote' => $entry->text,
            'author' => 'Nimasedani',
            'background_image_url' => null,
            'active_date' => now()->toDateString(),
            'is_active' => true,
            'publish_date' => now()->toDateString(),
        ];

        return response()->json(['data' => $wisdom]);
    }
}
