<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventsListingController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::where(function ($q) {
            $q->where('event_date', '>=', now()->toDateString())
                ->orWhere(function ($sq) {
                    $sq->whereNull('event_date')->where('start_time', '>=', now());
                });
        })
            ->orderBy('event_date', 'asc')
            ->get();

        return view('events.index', compact('events'));
    }

    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }

    public function register(Request $request, Event $event)
    {
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
