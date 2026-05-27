<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\EntryResource;
use App\Models\Entry;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EntryController extends Controller
{
    /**
     * Get a single entry
     */
    public function show(Request $request, Entry $entry): JsonResponse
    {
        $chapter = $entry->chapter;
        if ($chapter && !$chapter->collection->userCanAccess()) {
            return response()->json([
                'message' => 'This content requires purchase or premium subscription.',
                'requires_purchase' => !$chapter->collection->is_premium && $chapter->collection->price > 0,
                'requires_premium' => (bool) $chapter->collection->is_premium,
                'price' => $chapter->collection->price,
            ], 403);
        }

        $language = $request->query('language');

        // Load translations if language is specified
        if ($language) {
            $entry->load([
                'translations' => function ($q) use ($language) {
                    $q->where('language_code', $language);
                }
            ]);
        } else {
            $entry->load('translations');
        }

        return response()->json(new EntryResource($entry));
    }
}
