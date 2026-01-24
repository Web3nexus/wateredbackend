<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Incantation;
use Illuminate\Http\Request;

class IncantationController extends Controller
{
    /**
     * List incantations
     */
    public function index()
    {
        $incantations = Incantation::latest()->paginate(10);
        return response()->json($incantations);
    }

    /**
     * Show incantation details
     */
    public function show(Incantation $incantation)
    {
        return response()->json($incantation);
    }
}
