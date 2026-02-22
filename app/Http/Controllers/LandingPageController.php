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

    public function paymentCallback(Request $request)
    {
        $reference = $request->query('reference') ?? $request->query('trxref');
        $settings = GlobalSetting::first();

        // This is just the landing page after redirect. 
        // Logic for confirming payment is in the WebhookController.
        // We just show a nice message here.

        return view('payment.callback', [
            'settings' => $settings,
            'reference' => $reference,
            'status' => $request->query('status') === 'failed' ? 'failed' : 'success'
        ]);
    }

    public function privacy()
    {
        $settings = GlobalSetting::first();
        return view('privacy', compact('settings'));
    }

    public function terms()
    {
        $settings = GlobalSetting::first();
        return view('terms', compact('settings'));
    }

    public function contact()
    {
        $settings = GlobalSetting::first();
        return view('contact', compact('settings'));
    }

    public function deletion()
    {
        $settings = GlobalSetting::first();
        return view('deletion', compact('settings'));
    }
}
