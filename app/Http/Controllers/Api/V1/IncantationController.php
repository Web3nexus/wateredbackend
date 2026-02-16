<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\IncantationResource;
use App\Models\Incantation;
use Illuminate\Http\Request;

class IncantationController extends Controller
{
    /**
     * List incantations
     */
    public function index(Request $request)
    {
        $incantations = Incantation::when($request->category, function ($query, $category) {
            return $query->where('category', $category);
        })->latest()->paginate(20);

        return IncantationResource::collection($incantations);
    }

    /**
     * Show incantation details
     */
    public function show(Incantation $incantation)
    {
        return new IncantationResource($incantation);
    }
}
