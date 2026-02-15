<x-mail::message>
    # New Appointment Booked

    A new appointment has been booked on the website.

    **Appointment Details:**
    - **Code:** {{ $appointment->appointment_code }}
    - **Full Name:** {{ $appointment->full_name }}
    - **Email:** {{ $appointment->email }}
    - **Phone:** {{ $appointment->phone }}
    - **Service:** {{ $appointment->service_type ?? $appointment->consultationType?->name }}
    - **Date & Time:** {{ $appointment->start_time->format('F d, Y - h:i A') }}
    - **Payment Status:** {{ strtoupper($appointment->payment_status) }}

    <x-mail::button :url="config('app.url') . '/admin/appointments/' . $appointment->id">
        View in Admin Panel
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>