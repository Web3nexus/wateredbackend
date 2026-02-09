<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ConsultationType;
use App\Models\User;
use App\Mail\BookingConfirmationMail;
use App\Mail\AdminBookingNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    // List available types
    public function indexTypes(Request $request)
    {
        $types = ConsultationType::where('is_active', true)
            ->when($request->category, function ($query, $category) {
                return $query->where('category', $category);
            })->get();
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
            'start_time' => 'required|date|after:now',
            'notes' => 'nullable|string',
        ]);

        $type = ConsultationType::findOrFail($request->consultation_type_id);

        $booking = $request->user()->bookings()->create([
            'consultation_type_id' => $request->consultation_type_id,
            'start_time' => $request->start_time,
            'notes' => $request->notes,
            'status' => 'pending_payment',
            'amount' => $type->price,
        ]);

        // Send confirmation mail
        try {
            Mail::to($request->user())->send(new BookingConfirmationMail($booking));
        } catch (\Exception $e) {
            \Log::error('Failed to send booking confirmation mail: ' . $e->getMessage());
        }

        // Notify Admin
        try {
            // Find super admin or fallback to ID 1
            $admin = User::role('super_admin')->first() ?? User::where('id', 1)->first();

            if ($admin) {
                Mail::to($admin)->send(new AdminBookingNotificationMail($booking));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to notify admin of new booking: ' . $e->getMessage());
        }

        return response()->json([
            'data' => $booking,
            'payment_url' => null,
            'message' => 'Booking created. Please complete payment.'
        ], 201);
    }
}
