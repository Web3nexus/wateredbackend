<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $status === 'success' ? 'Payment Successful' : 'Payment Failed' }} -
        {{ $settings?->site_name ?? 'Watered' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if($settings?->favicon_url)
        <link rel="icon" type="image/x-icon" href="{{ $settings->favicon_url }}?v={{ time() }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('images/watered-logo.png') }}">
    @endif
</head>

<body class="bg-sea-deep text-parchment font-sans">
    <div class="fixed inset-0 -z-10 bg-sea-deep">
        <div class="absolute inset-0 opacity-5"
            style="background-image: radial-gradient(circle at 2px 2px, rgba(0,119,190,0.1) 1px, transparent 0); background-size: 40px 40px;">
        </div>
        <div class="absolute top-1/4 right-0 w-[800px] h-[800px] bg-app-blue/10 blur-[200px] rounded-full"></div>
    </div>

    <main class="min-h-screen flex items-center justify-center p-6">
        <div
            class="max-w-xl w-full bg-parchment/5 border border-parchment/10 rounded-[3rem] p-12 text-center backdrop-blur-md">
            @if($status === 'success')
                <div
                    class="w-20 h-20 bg-green-500/20 text-green-500 rounded-full flex items-center justify-center mx-auto mb-8">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="font-heading text-4xl text-parchment mb-4 uppercase tracking-tight">Payment Received</h1>
                <p class="text-parchment/60 mb-10 leading-relaxed">Your request has been successfully processed. We've sent
                    a detailed confirmation to your email.</p>

                <div class="bg-parchment/5 rounded-2xl p-6 mb-10 text-left border border-parchment/5">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-parchment/30 mb-2">Transaction Reference
                    </p>
                    <p class="font-mono text-app-blue break-all uppercase">{{ $reference }}</p>
                </div>
            @else
                <div
                    class="w-20 h-20 bg-red-500/20 text-red-500 rounded-full flex items-center justify-center mx-auto mb-8">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </div>
                <h1 class="font-heading text-4xl text-parchment mb-4 uppercase tracking-tight">Payment Failed</h1>
                <p class="text-parchment/60 mb-10 leading-relaxed">We encountered an issue while processing your payment.
                    Please try again or contact support.</p>
            @endif

            <div class="flex flex-col gap-4">
                <a href="{{ route('home') }}"
                    class="w-full py-5 bg-app-blue text-white font-bold rounded-2xl hover:bg-white hover:text-sea-deep transition-all uppercase tracking-widest text-sm shadow-xl shadow-app-blue/20">Return
                    Home</a>
                <p class="text-[10px] text-parchment/20 uppercase tracking-[0.3em] font-medium pt-4">© {{ date('Y') }}
                    {{ $settings?->site_name ?? 'Watered' }}</p>
            </div>
        </div>
    </main>
</body>

</html>