<x-mail::message>
    # New Event Booking

    A new booking has been made for **{{ $registration->event->title }}**.

    ## Event Details
    - **Date:** {{ $registration->event->event_date?->format('l, F j, Y') ?? 'TBA' }}
    - **Time:**
    {{ $registration->event->event_time ? date('g:i A', strtotime($registration->event->event_time)) : 'TBA' }}
    - **Location:** {{ $registration->event->location ?? 'Online' }}

    ## Attendee Information
    - **Name:** {{ $registration->full_name }}
    - **Email:** {{ $registration->email }}
    - **Phone:** {{ $registration->phone }}

    ## Booking Details
    - **Status:** {{ ucfirst($registration->status) }}
    - **Payment Status:** {{ ucfirst($registration->payment_status) }}
    @if($registration->amount)
        - **Amount:** ${{ number_format($registration->amount, 2) }}
    @endif
    @if($registration->payment_reference)
        - **Reference:** {{ $registration->payment_reference }}
    @endif
    - **Booked At:** {{ $registration->created_at->format('M d, Y g:i A') }}

    <x-mail::button :url="config('app.url') . '/admin'">
        View in Admin Panel
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>