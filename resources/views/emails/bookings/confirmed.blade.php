<x-mail::message>
# Booking Confirmed

Hello {{ $booking->user->name }},

Your consultation for **{{ $booking->consultationType->name }}** has been confirmed.

**Date & Time:** {{ $booking->start_time->format('F j, Y, g:i a') }}

Thank you for choosing Watered.

<x-mail::button :url="config('app.url')">
View in App
</x-mail::button>

Blessings,<br>
{{ config('app.name') }}
</x-mail::message>
