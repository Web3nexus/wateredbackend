<x-mail::message>
    # Application Approved

    Hello {{ $userName }},

    Congratulations! Your application for **{{ $orderTitle }}** has been approved.

    @if($adminNotes)
        **Elders' Notes:**
        {{ $adminNotes }}
    @endif

    We look forward to your journey with us.

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>