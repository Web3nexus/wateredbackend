<?php

namespace App\Http\Controllers;

use App\Models\Tradition;
use App\Models\GlobalSetting;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        $settings = GlobalSetting::first();
        $traditions = Tradition::where('is_active', true)->with('books')->get();

        return view('index', compact('settings', 'traditions'));
    }
}
