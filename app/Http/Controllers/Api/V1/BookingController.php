<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ConsultationType;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // List available types
    public function indexTypes()
    {
        $types = ConsultationType::where('is_active', true)->get();
        return response()->json(['data' => $types]);
    }

    // List user bookings
    public function index(Request $request)
    {
        $bookings = $request->user()->bookings()
            ->with('consultationType')
            ->latest()
            ->get();
        return response()->json(['data' => $bookings]);
    }

    // Create booking
    public function store(Request $request)
    {
        $request->validate([
            'consultation_type_id' => 'required|exists:consultation_types,id',
            'scheduled_at' => 'required|date|after:now',
            'notes' => 'nullable|string',
        ]);

        $booking = $request->user()->bookings()->create([
            'consultation_type_id' => $request->consultation_type_id,
            'scheduled_at' => $request->scheduled_at,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return response()->json(['data' => $booking, 'message' => 'Booking request sent.'], 201);
    }
}
