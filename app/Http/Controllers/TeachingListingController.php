<?php

namespace App\Http\Controllers;

use App\Models\Teaching;
use App\Models\GlobalSetting;
use Illuminate\Http\Request;

class TeachingListingController extends Controller
{
    public function index()
    {
        $settings = GlobalSetting::first();
        $teachings = Teaching::where('is_published', true)
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('teachings.index', compact('teachings', 'settings'));
    }

    public function show($slug)
    {
        $settings = GlobalSetting::first();
        $teaching = Teaching::where('slug', $slug)->firstOrFail();

        // Fetch 3 related teachings (latest excluding current)
        $related = Teaching::where('is_published', true)
            ->where('id', '!=', $teaching->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('teachings.show', compact('teaching', 'settings', 'related'));
    }
}
