<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'upcoming'); // default to upcoming

        $query = Event::query()
            ->when($request->category, function ($q, $category) {
                return $q->where('category', $category);
            })
            ->when($request->tradition_id, function ($q, $traditionId) {
                return $q->where('tradition_id', $traditionId);
            });

        $hasEventDate = (new Event)->getConnection()->getSchemaBuilder()->hasColumn('events', 'event_date');

        switch ($filter) {
            case 'new':
                $query->orderBy('created_at', 'desc');
                break;
            case 'past':
                $query->where(function ($q) use ($hasEventDate) {
                    if ($hasEventDate) {
                        $q->where('event_date', '<', now()->toDateString())
                            ->orWhere(function ($sq) {
                                $sq->whereNull('event_date')->where('start_time', '<', now());
                            });
                    } else {
                        $q->where('start_time', '<', now());
                    }
                });
                if ($hasEventDate) {
                    $query->orderBy('event_date', 'desc');
                } else {
                    $query->orderBy('start_time', 'desc');
                }
                break;
            case 'upcoming':
            default:
                $query->where(function ($q) use ($hasEventDate) {
                    if ($hasEventDate) {
                        $q->where('event_date', '>=', now()->toDateString())
                            ->orWhere(function ($sq) {
                                $sq->whereNull('event_date')->where('start_time', '>=', now());
                            });
                    } else {
                        $q->where('start_time', '>=', now());
                    }
                });
                if ($hasEventDate) {
                    $query->orderBy('event_date', 'asc');
                } else {
                    $query->orderBy('start_time', 'asc');
                }
                break;
        }

        $events = $query->get();

        return response()->json([
            'data' => $events
        ]);
    }

    public function show($identifier)
    {
        $hasSlug = (new Event)->getConnection()->getSchemaBuilder()->hasColumn('events', 'slug');

        $event = Event::query()
            ->when($hasSlug, function ($q) use ($identifier) {
                $q->where('slug', $identifier)->orWhere('id', $identifier);
            }, function ($q) use ($identifier) {
                $q->where('id', $identifier);
            })
            ->firstOrFail();

        $user = auth()->user();
        $eventData = $event->toArray();

        // Add registration and reminder status for authenticated users
        if ($user) {
            $eventData['is_registered'] = $event->isRegistered($user);
            $eventData['has_reminder'] = $event->hasReminder($user);
        }

        return response()->json([
            'data' => $eventData
        ]);
    }

    /**
     * Register user for an event
     */
    public function register(Request $request, $identifier)
    {
        $hasSlug = (new Event)->getConnection()->getSchemaBuilder()->hasColumn('events', 'slug');

        $event = Event::query()
            ->when($hasSlug, function ($q) use ($identifier) {
                $q->where('slug', $identifier)->orWhere('id', $identifier);
            }, function ($q) use ($identifier) {
                $q->where('id', $identifier);
            })
            ->firstOrFail();

        $user = auth()->user();

        $validated = $request->validate([
            'full_name' => $user ? 'nullable|string' : 'required|string',
            'email' => $user ? 'nullable|email' : 'required|email',
            'phone' => $user ? 'nullable|string' : 'required|string',
        ]);

        // Check if already registered
        if ($user) {
            if ($event->isRegistered($user)) {
                return response()->json(['message' => 'Already registered for this event'], 400);
            }
        } else {
            $existing = $event->registrations()
                ->where('email', $validated['email'])
                ->first();
            if ($existing) {
                return response()->json(['message' => 'This email is already registered for this event'], 400);
            }
        }

        // For paid events via API, we usually return requires_payment 
        // and let them initiate payment separately
        if ($event->is_paid) {
            return response()->json([
                'message' => 'Payment required for this event',
                'requires_payment' => true,
                'amount' => $event->price
            ], 402);
        }

        // Register for free event
        $registration = $event->registrations()->create([
            'user_id' => $user?->id,
            'full_name' => $validated['full_name'] ?? $user?->name,
            'email' => $validated['email'] ?? $user?->email,
            'phone' => $validated['phone'] ?? $user?->phone,
            'status' => 'registered',
            'payment_status' => 'completed', // Free event
        ]);

        return response()->json([
            'message' => 'Successfully registered for event',
            'data' => $registration
        ], 201);
    }

    /**
     * Cancel event registration
     */
    public function cancelRegistration($identifier)
    {
        $hasSlug = (new Event)->getConnection()->getSchemaBuilder()->hasColumn('events', 'slug');

        $event = Event::query()
            ->when($hasSlug, function ($q) use ($identifier) {
                $q->where('slug', $identifier)->orWhere('id', $identifier);
            }, function ($q) use ($identifier) {
                $q->where('id', $identifier);
            })
            ->firstOrFail();

        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $registration = $event->registrations()
            ->where('user_id', $user->id)
            ->first();

        if (!$registration) {
            return response()->json(['message' => 'Not registered for this event'], 404);
        }

        $registration->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Registration cancelled successfully'
        ]);
    }

    /**
     * Initiate Paystack payment for paid event
     */
    public function initiatePayment(Request $request, $identifier)
    {
        $hasSlug = (new Event)->getConnection()->getSchemaBuilder()->hasColumn('events', 'slug');

        $event = Event::query()
            ->when($hasSlug, function ($q) use ($identifier) {
                $q->where('slug', $identifier)->orWhere('id', $identifier);
            }, function ($q) use ($identifier) {
                $q->where('id', $identifier);
            })
            ->firstOrFail();

        $user = auth()->user();

        $validated = $request->validate([
            'full_name' => $user ? 'nullable|string' : 'required|string',
            'email' => $user ? 'nullable|email' : 'required|email',
            'phone' => $user ? 'nullable|string' : 'required|string',
        ]);

        if (!$event->is_paid) {
            return response()->json(['message' => 'This event is free'], 400);
        }

        // Initialize Paystack payment
        $settings = \App\Models\GlobalSetting::first();
        $paystackSecretKey = $settings->paystack_secret_key;

        $url = "https://api.paystack.co/transaction/initialize";

        $fields = [
            'email' => $validated['email'] ?? $user?->email,
            'amount' => $event->price * 100, // Convert to kobo
            'metadata' => [
                'type' => 'event',
                'event_id' => $event->id,
                'user_id' => $user?->id,
                'full_name' => $validated['full_name'] ?? $user?->name,
                'email' => $validated['email'] ?? $user?->email,
                'phone' => $validated['phone'] ?? $user?->phone,
                'event_title' => $event->title,
            ],
            'callback_url' => route('payment.callback'),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $paystackSecretKey,
            "Content-Type: application/json",
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if ($result && $result['status']) {
            return response()->json([
                'message' => 'Payment initialized',
                'data' => $result['data']
            ]);
        }

        return response()->json([
            'message' => 'Failed to initialize payment',
            'error' => $result['message'] ?? 'Unknown error'
        ], 500);
    }

}
