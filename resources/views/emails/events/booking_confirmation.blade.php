<x-mail::message>
    # Booking Confirmation

    Hi {{ $registration->full_name }},

    You have successfully booked your spot for **{{ $registration->event->title }}**.

    ## Event Details
    - **Date:** {{ $registration->event->event_date?->format('l, F j, Y') ?? 'TBA' }}
    - **Time:**
    {{ $registration->event->event_time ? date('g:i A', strtotime($registration->event->event_time)) : 'TBA' }}
    - **Location:** {{ $registration->event->location ?? 'Online' }}

    ## Your Ticket
    - **Name:** {{ $registration->full_name }}
    - **Email:** {{ $registration->email }}
    - **Phone:** {{ $registration->phone }}
    @if($registration->payment_reference)
        - **Reference:** {{ $registration->payment_reference }}
    @endif

    <x-mail::button :url="config('app.url')">
        Visit Website
    </x-mail::button>

    We look forward to seeing you there!

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>