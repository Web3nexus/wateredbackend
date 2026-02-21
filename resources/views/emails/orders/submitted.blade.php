<x-mail::message>
    # Application Received

    Hello {{ $userName }},

    We have received your application for **{{ $orderTitle }}**.

    Our elders will review your submission and you will be notified via email once a decision has been made.

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>