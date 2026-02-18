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
        $startTime = \Carbon\Carbon::parse($request->start_time);

        // Enforce availability rules
        if (!$this->isAvailable($type, $startTime)) {
            return response()->json([
                'message' => 'The selected time is not available for this appointment type.',
                'errors' => ['start_time' => ['This time slot is outside of allowed windows.']]
            ], 422);
        }

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
            $appointmentData['appointment_status'] = 'pending';
            $appointmentData['payment_status'] = 'pending';
        } else {
            $appointmentData['status'] = 'pending';
        }

        $appointment = Appointment::create($appointmentData);

        // Generate Paystack Checkout URL if price > 0
        $paymentUrl = null;
        if ($appointment->amount > 0) {
            $paymentUrl = $this->generatePaystackUrl($appointment);
        } else {
            // If free (e.g., Visit the Temple), confirm immediately
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

    protected function isAvailable(ConsultationType $type, \Carbon\Carbon $time)
    {
        $dayName = $time->format('l'); // Monday, Tuesday, etc.
        $hour = $time->hour;
        $minute = $time->minute;
        $timeString = $time->format('H:i');

        if ($type->category === 'temple_visit') {
            // Monday – Wednesday: 10:00 AM – 4:00 PM
            // Thursday: 7:00 AM – 6:00 PM
            // Friday: 10:00 AM – 4:00 PM
            // Saturday: 7:00 AM – 6:00 PM
            // Sunday: 10:00 AM – 4:00 PM
            if (in_array($dayName, ['Monday', 'Tuesday', 'Wednesday', 'Friday', 'Sunday'])) {
                return $timeString >= '10:00' && $timeString <= '16:00';
            } elseif (in_array($dayName, ['Thursday', 'Saturday'])) {
                return $timeString >= '07:00' && $timeString <= '18:00';
            }
        } elseif ($type->category === 'lord_uzih') {
            // Tuesday, Wednesday, Friday: 10:00 AM – 4:00 PM
            if (in_array($dayName, ['Tuesday', 'Wednesday', 'Friday'])) {
                return $timeString >= '10:00' && $timeString <= '16:00';
            }
        }

        return false;
    }

    protected function generatePaystackUrl(Appointment $appointment)
    {
        $settings = GlobalSetting::first();
        if (!$settings) {
            Log::error('Global settings not found when generating Paystack URL');
            return null;
        }

        $secretKey = $settings->paystack_secret_key;

        if (!$secretKey) {
            Log::error('Paystack Secret Key not found in settings');
            return null;
        }

        try {
            $response = Http::withToken($secretKey)
                ->post('https://api.paystack.co/transaction/initialize', [
                    'email' => $appointment->email,
                    'amount' => (int) ($appointment->amount * 100), // Ensure integer Kobo
                    'currency' => 'NGN',
                    'reference' => 'APT_' . ($appointment->appointment_code ?? $appointment->id) . '_' . time(),
                    'callback_url' => route('payment.callback'),
                    'metadata' => [
                        'appointment_id' => $appointment->id,
                        'type' => 'appointment'
                    ]
                ]);

            if ($response->successful()) {
                return $response->json()['data']['authorization_url'];
            } else {
                Log::error('Paystack initialization failed: ' . $response->body());
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
