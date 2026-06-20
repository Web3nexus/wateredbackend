<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ConsultationCategory;
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
            })
            ->with('categoryModel')
            ->get();

        $data = $types->map(function ($type) {
            return array_merge($type->toArray(), [
                'availability' => $type->categoryModel?->availability ?? [],
            ]);
        });

        return response()->json(['data' => $data]);
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
        $settings = GlobalSetting::first();
        if ($settings) {
            $isEnabled = $request->user() ? $settings->is_app_bookings_enabled : $settings->is_web_bookings_enabled;
            if (!$isEnabled) {
                $platform = $request->user() ? 'app' : 'website';
                return response()->json([
                    'message' => "Appointment booking is currently disabled on the {$platform}.",
                ], 403);
            }
        }

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
            if (!$paymentUrl) {
                // Payment URL generation failed — clean up and return error
                $appointment->delete();
                return response()->json([
                    'message' => 'Unable to initialize payment. Please try again later or contact support.',
                ], 502);
            }
        } else {
            // If free (e.g., Visit the Temple), confirm immediately
            $appointment->update(['appointment_status' => 'confirmed', 'payment_status' => 'paid']);
            try {
                Mail::to($appointment->email)->send(new UserAppointmentConfirmationMail($appointment));
            } catch (\Exception $e) {
                Log::error('Failed to send free appointment confirmation: ' . $e->getMessage());
            }
        }

        // Notify Admin Immediately
        $this->notifyAdmin($appointment);

        return response()->json([
            'data' => $appointment,
            'payment_url' => $paymentUrl,
            'callback_url' => route('payment.callback'),
            'reference' => $paymentUrl ? ($appointment->appointment_code ?? $appointment->id) : null,
            'message' => 'Appointment created successfully.'
        ], 201);
    }

    protected function isAvailable(ConsultationType $type, \Carbon\Carbon $time)
    {
        $category = ConsultationCategory::where('slug', $type->category)->first();

        if (!$category || !$category->availability) {
            return false;
        }

        $dayOfWeek = $time->dayOfWeek; // 0=Sunday, 6=Saturday
        $timeString = $time->format('H:i');

        foreach ($category->availability as $slot) {
            if (in_array($dayOfWeek, $slot['days']) && $timeString >= $slot['start'] && $timeString <= $slot['end']) {
                return true;
            }
        }

        return false;
    }

    protected function generatePaystackUrl(Appointment $appointment)
    {
        $settings = GlobalSetting::first();
        if (!$settings) {
            Log::error('Paystack: Global settings not found when generating URL for appointment #' . $appointment->id);
            return null;
        }

        $secretKey = $settings->paystack_secret_key
            ?? config('services.paystack.secret_key');

        if (!$secretKey) {
            Log::error('Paystack: Secret Key not configured (empty in DB and .env) for appointment #' . $appointment->id);
            return null;
        }

        try {
            $reference = 'APT_' . ($appointment->appointment_code ?? $appointment->id) . '_' . time();

            $response = Http::withToken($secretKey)
                ->post('https://api.paystack.co/transaction/initialize', [
                    'email' => $appointment->email,
                    'amount' => (int) ($appointment->amount * 100),
                    'currency' => 'NGN',
                    'reference' => $reference,
                    'callback_url' => route('payment.callback'),
                    'metadata' => [
                        'appointment_id' => $appointment->id,
                        'type' => 'appointment',
                        'cancel_action' => 'https://mywatered.com',
                    ]
                ]);

            if ($response->successful()) {
                $body = $response->json();
                Log::info('Paystack: URL generated for appointment #' . $appointment->id . ' ref=' . $reference);
                return $body['data']['authorization_url'] ?? null;
            } else {
                Log::error('Paystack: initialization failed for appointment #' . $appointment->id . ': ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Paystack: Initialization Error for appointment #' . $appointment->id . ': ' . $e->getMessage());
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
