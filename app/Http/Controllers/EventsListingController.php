<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventsListingController extends Controller
{
    public function index(Request $request)
    {
        $hasEventDate = (new Event)->getConnection()->getSchemaBuilder()->hasColumn('events', 'event_date');

        $events = Event::query()
            ->when($hasEventDate, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('event_date', '>=', now()->toDateString())
                        ->orWhere(function ($ssq) {
                            $ssq->whereNull('event_date')->where('start_time', '>=', now());
                        });
                })->orderBy('event_date', 'asc');
            }, function ($q) {
                $q->where('start_time', '>=', now())->orderBy('start_time', 'asc');
            })
            ->get();

        $settings = \App\Models\GlobalSetting::first();

        return view('events.index', compact('events', 'settings'));
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

        $settings = \App\Models\GlobalSetting::first();

        return view('events.show', compact('event', 'settings'));
    }

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

        // Handled via proxy to Api V1 EventController or duplicate logic here
        // For simplicity, let's call the API logic
        $apiController = new \App\Http\Controllers\Api\V1\EventController();
        $response = $apiController->register($request, $event);

        $data = json_decode($response->getContent(), true);

        if ($response->getStatusCode() === 201) {
            return back()->with('success', 'Successfully registered for the event!');
        }

        if ($response->getStatusCode() === 402) {
            // Initiate payment for guest/user
            $paymentResponse = $apiController->initiatePayment($request, $event);
            $paymentData = json_decode($paymentResponse->getContent(), true);

            if (isset($paymentData['data']['authorization_url'])) {
                return redirect($paymentData['data']['authorization_url']);
            }
        }

        return back()->withErrors(['message' => $data['message'] ?? 'Registration failed.']);
    }
}
