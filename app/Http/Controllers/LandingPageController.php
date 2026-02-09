<?php

namespace App\Http\Controllers;

use App\Models\Tradition;
use App\Models\GlobalSetting;
use App\Models\BlogPost;
use App\Models\LandingPageFeature;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        $settings = GlobalSetting::first();
        $traditions = Tradition::where('is_active', true)->with('books')->get();
        $features = LandingPageFeature::where('is_active', true)->orderBy('order')->get();
        $blogPosts = BlogPost::where('is_published', true)->orderByDesc('published_at')->limit(3)->get();

        if ($settings && !$settings?->is_landing_page_enabled) {
            return response()->view('welcome');
        }

        return view('index', compact('settings', 'traditions', 'features', 'blogPosts'));
    }
}
