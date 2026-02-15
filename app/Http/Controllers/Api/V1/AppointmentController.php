<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ConsultationType;
use App\Models\Appointment;
use App\Models\GlobalSetting;
use App\Models\User;
use App\Mail\UserAppointmentConfirmationMail;
use App\Mail\AdminAppointmentNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    /**
     * List available types
     */
    public function indexTypes(Request $request)
    {
        $types = ConsultationType::where('is_active', true)
            ->when($request->category, function ($query, $category) {
                return $query->where('category', $category);
            })->get();
        return response()->json(['data' => $types]);
    }

    /**
     * List appointments (authenticated users)
     */
    public function index(Request $request)
    {
        $appointments = $request->user()->appointments()
            ->with('consultationType')
            ->latest()
            ->get();
        return response()->json(['data' => $appointments]);
    }

    /**
     * Track appointment (for guests)
     */
    public function show($code)
    {
        $appointment = Appointment::where('appointment_code', $code)
            ->with('consultationType')
            ->firstOrFail();

        return response()->json(['data' => $appointment]);
    }

    /**
     * Store new appointment
     */
    public function store(Request $request)
    {
        $request->validate([
            'consultation_type_id' => 'required|exists:consultation_types,id',
            'start_time' => 'required|date|after:now',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $type = ConsultationType::findOrFail($request->consultation_type_id);

        $hasAppointmentCode = (new Appointment)->getConnection()->getSchemaBuilder()->hasColumn((new Appointment)->getTable(), 'appointment_code');

        $appointmentData = [
            'user_id' => $request->user()?->id,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'consultation_type_id' => $request->consultation_type_id,
            'start_time' => $request->start_time,
            'notes' => $request->notes,
            'amount' => $type->price,
        ];

        if ($hasAppointmentCode) {
            $appointmentData['appointment_code'] = $this->generateUniqueCode();
            $appointmentData['appointment_status'] = 'pending';
            $appointmentData['payment_status'] = 'pending';
        } else {
            // Fallback for legacy 'bookings' table schema
            $appointmentData['status'] = 'pending';
            // Code might be 'booking_code' or non-existent in legacy? 
            // Better to check for 'booking_code' if it existed before.
        }

        $appointment = Appointment::create($appointmentData);

        // Generate Paystack Checkout URL if price > 0
        $paymentUrl = null;
        if ($appointment->amount > 0) {
            $paymentUrl = $this->generatePaystackUrl($appointment);
        } else {
            // If free, confirm immediately? 
            // The prompt says confirm after payment, but if free maybe confirm now.
            $appointment->update(['appointment_status' => 'confirmed', 'payment_status' => 'paid']);
        }

        // Notify Admin Immediately
        $this->notifyAdmin($appointment);

        return response()->json([
            'data' => $appointment,
            'payment_url' => $paymentUrl,
            'message' => 'Appointment created successfully.'
        ], 201);
    }

    protected function generateUniqueCode()
    {
        do {
            $code = 'APT-' . strtoupper(Str::random(8));
        } while (Appointment::where('appointment_code', $code)->exists());

        return $code;
    }

    protected function generatePaystackUrl(Appointment $appointment)
    {
        $settings = GlobalSetting::first();
        $secretKey = $settings?->paystack_secret_key;

        if (!$secretKey) {
            Log::error('Paystack Secret Key not found in settings');
            return null;
        }

        try {
            $response = Http::withToken($secretKey)
                ->post('https://api.paystack.co/transaction/initialize', [
                    'email' => $appointment->email,
                    'amount' => $appointment->amount * 100, // Kobo
                    'reference' => 'APT_' . $appointment->appointment_code . '_' . time(),
                    'callback_url' => route('payment.callback'),
                    'metadata' => [
                        'appointment_id' => $appointment->id,
                        'type' => 'appointment'
                    ]
                ]);

            if ($response->successful()) {
                return $response->json()['data']['authorization_url'];
            }
        } catch (\Exception $e) {
            Log::error('Paystack Initialization Error: ' . $e->getMessage());
        }

        return null;
    }

    protected function notifyAdmin(Appointment $appointment)
    {
        try {
            $adminEmail = GlobalSetting::first()?->contact_email ?? config('mail.from.address');
            Mail::to($adminEmail)->send(new AdminAppointmentNotificationMail($appointment));
        } catch (\Exception $e) {
            Log::error('Admin Notification Error: ' . $e->getMessage());
        }
    }
}
