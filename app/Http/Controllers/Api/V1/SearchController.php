<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Audio;
use App\Models\Product;
use App\Models\Temple;
use App\Models\Video;
use App\Models\Tradition;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->query('query');

        if (!$query) {
            return response()->json([
                'videos' => [],
                'audio' => [],
                'products' => [],
                'temples' => [],
                'traditions' => [],
            ]);
        }

        // Limit results per category to avoid huge payload
        $limit = 5;

        $videos = Video::where('is_active', true)
            ->where('title', 'like', "%{$query}%")
            ->take($limit)
            ->get();

        $audio = Audio::where('is_active', true)
            ->where('title', 'like', "%{$query}%")
            ->take($limit)
            ->get();

        $products = Product::where('is_active', true)
            ->where('name', 'like', "%{$query}%")
            ->take($limit)
            ->get();

        $temples = Temple::where('is_active', true)
            ->where('name', 'like', "%{$query}%")
            ->take($limit)
            ->get();

        $traditions = Tradition::where('is_active', true)
            ->where('name', 'like', "%{$query}%")
            ->take($limit)
            ->get();

        return response()->json([
            'videos' => $videos,
            'audio' => $audio,
            'products' => $products,
            'temples' => $temples,
            'traditions' => $traditions,
        ]);
    }
}
