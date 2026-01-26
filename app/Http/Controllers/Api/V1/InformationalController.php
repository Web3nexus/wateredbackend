<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\UserGuide;
use Illuminate\Http\Request;

class InformationalController extends Controller
{
    /**
     * Get all active FAQs
     */
    public function indexFaqs()
    {
        $faqs = Faq::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        return response()->json(['data' => $faqs]);
    }

    /**
     * Get all active User Guides
     */
    public function indexUserGuides()
    {
        $guides = UserGuide::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json(['data' => $guides]);
    }
}
