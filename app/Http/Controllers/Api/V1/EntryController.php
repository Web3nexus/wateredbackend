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
