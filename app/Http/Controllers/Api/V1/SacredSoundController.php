<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SacredSound;
use Illuminate\Http\Resources\Json\JsonResource;

class SacredSoundController extends Controller
{
    public function index()
    {
        $sounds = SacredSound::where('is_active', true)
            ->latest()
            ->get();

        // Map to ensure full URL is returned
        return JsonResource::collection($sounds)->map(function ($sound) {
            return [
                'id' => $sound->id,
                'title' => $sound->title,
                'file_path' => asset('storage/' . $sound->file_path),
                'type' => $sound->type,
            ];
        });
    }
}
