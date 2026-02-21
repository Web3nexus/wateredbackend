<x-mail::message>
    # Application Status Update

    Hello {{ $userName }},

    Thank you for your interest in **{{ $orderTitle }}**.

    After careful review, we are unable to approve your application at this time.

    @if($adminNotes)
        **Elders' Notes:**
        {{ $adminNotes }}
    @endif

    We encourage you to continue your spiritual path and check back for other opportunities.

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>