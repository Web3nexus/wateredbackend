<?php

namespace App\Http\Controllers\Api\V1;

use App\Mail\BookingConfirmationMail;
use App\Mail\AdminBookingNotificationMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

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

use App\Mail\BookingConfirmationMail;
use Illuminate\Support\Facades\Mail;
...
        // Send confirmation mail
        Mail::to($request->user())->send(new BookingConfirmationMail($booking));

        // Notify Admin (simplification: find first admin or use config)
        $admin = User::permission('manage_bookings')->first() ?? User::where('id', 1)->first();
        if ($admin) {
            Mail::to($admin)->send(new AdminBookingNotificationMail($booking));
        }

        return response()->json([
            'data' => $booking,
            'payment_url' => $paymentUrl,
            'message' => 'Booking created. Please complete payment.'
        ], 201);
    }
}
