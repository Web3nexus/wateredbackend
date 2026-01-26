<x-mail::message>
# New Booking Received

A new consultation has been booked.

**User:** {{ $booking->user->name }} ({{ $booking->user->email }})
**Type:** {{ $booking->consultationType->name }}
**Requested Time:** {{ $booking->start_time->format('F j, Y, g:i a') }}

**Notes:** 
{{ $booking->notes ?? 'No notes provided.' }}

<x-mail::button :url="config('app.url') . '/admin/bookings/' . $booking->id">
Manage Booking
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
