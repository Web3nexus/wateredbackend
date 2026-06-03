<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DailyWisdomController extends Controller
{
    public function index()
    {
        $default = [
            'data' => [
                'id' => 0,
                'quote' => 'The God of Seas and Voices guides those who acknowledge the current of divine will.',
                'author' => 'Nima Sedani',
                'background_image_url' => 'https://images.unsplash.com/photo-1505118380757-91f5f5632de0?auto=format&fit=crop&q=80',
                'active_date' => now()->toDateString(),
                'is_active' => true,
                'publish_date' => now()->toDateString(),
            ]
        ];

        try {
            $collection = \App\Models\TextCollection::find(1);

            if (!$collection) {
                $collection = \App\Models\TextCollection::where('name', 'LIKE', '%Nima Sedani%')
                    ->orWhere('name', 'LIKE', '%ni maseani%')
                    ->first();
            }

            if (!$collection) {
                return response()->json($default);
            }

            $entry = \App\Models\Entry::where('entries.is_active', true)
                ->join('chapters', 'entries.chapter_id', '=', 'chapters.id')
                ->where('chapters.collection_id', $collection->id)
                ->where('chapters.is_active', true)
                ->select('entries.*')
                ->with('chapter')
                ->inRandomOrder()
                ->first();

            if (!$entry) {
                return response()->json($default);
            }

            $chapterOrder = $entry->chapter?->order ?? 1;
            $entryOrder = $entry->order ?? 1;

            $backgroundImage = $collection->background_image_url;
            if (!$backgroundImage && $collection->tradition_id) {
                $tradition = \App\Models\Tradition::find($collection->tradition_id);
                $backgroundImage = $tradition?->deity_image_url;
            }
            if (!$backgroundImage) {
                $backgroundImage = $default['data']['background_image_url'];
            }

            return response()->json([
                'data' => [
                    'id' => $entry->id,
                    'quote' => strip_tags((string) $entry->text),
                    'author' => 'Nima Sedani',
                    'chapter_number' => $chapterOrder,
                    'verse_number' => $entryOrder,
                    'background_image_url' => $backgroundImage,
                    'active_date' => now()->toDateString(),
                    'publish_date' => now()->toDateString(),
                    'is_active' => true,
                    'citation' => "Chapter {$chapterOrder}:{$entryOrder}",
                ]
            ]);
        } catch (\Throwable $e) {
            \Log::error('Daily Wisdom Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json($default);
        }
    }
}
