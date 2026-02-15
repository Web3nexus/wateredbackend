<x-mail::message>
    # Appointment Confirmed

    Dear {{ $appointment->full_name }},

    Your appointment has been successfully confirmed.

    **Appointment Summary:**
    - **Tracking Code:** {{ $appointment->appointment_code }}
    - **Service:** {{ $appointment->service_type ?? $appointment->consultationType?->name }}
    - **Date & Time:** {{ $appointment->start_time->format('F d, Y - h:i A') }}
    - **Status:** Confirmed

    We look forward to seeing you. If you need to reschedule or have any questions, please contact us at
    {{ config('mail.from.address') }}.

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>