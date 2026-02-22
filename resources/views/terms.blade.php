@extends('layouts.main')

@section('title', 'Terms of Service - ' . ($settings?->site_name ?? 'Watered'))

@section('content')
    <div class="py-24 max-w-4xl mx-auto px-6">
        <h1 class="text-5xl md:text-6xl text-parchment font-heading mb-12">Terms of Service</h1>

        <div class="prose prose-invert prose-lg max-w-none text-parchment/70 space-y-8">
            <section>
                <h2 class="text-2xl text-app-blue font-bold uppercase tracking-widest mb-4">1. Acceptance of Terms</h2>
                <p>By accessing and using the Watered website and application, you accept and agree to be bound by the terms
                    and provision of this agreement. In addition, when using these particular services, you shall be subject
                    to any posted guidelines or rules applicable to such services.</p>
            </section>

            <section>
                <h2 class="text-2xl text-app-blue font-bold uppercase tracking-widest mb-4">2. Description of Service</h2>
                <p>Watered provides users with access to spiritual teachings, community features, and ritual guidance (the
                    "Service"). You understand and agree that the Service is provided "AS-IS" and that Watered assumes no
                    responsibility for the timeliness, deletion, mis-delivery or failure to store any user communications or
                    personalization settings.</p>
            </section>

            <section>
                <h2 class="text-2xl text-app-blue font-bold uppercase tracking-widest mb-4">3. Registration Obligations</h2>
                <p>In consideration of your use of the Service, you agree to: (a) provide true, accurate, current and
                    complete information about yourself as prompted by the Service's registration form and (b) maintain and
                    promptly update the Registration Data to keep it true, accurate, current and complete.</p>
            </section>

            <section>
                <h2 class="text-2xl text-app-blue font-bold uppercase tracking-widest mb-4">4. Privacy Policy</h2>
                <p>Registration Data and certain other information about you is subject to our Privacy Policy. For more
                    information, see our full privacy policy.</p>
            </section>

            <section>
                <h2 class="text-2xl text-app-blue font-bold uppercase tracking-widest mb-4">5. User Conduct</h2>
                <p>You understand that all information, data, text, software, music, sound, photographs, graphics, video,
                    messages or other materials ("Content"), whether publicly posted or privately transmitted, are the sole
                    responsibility of the person from which such Content originated.</p>
            </section>

            <section>
                <h2 class="text-2xl text-app-blue font-bold uppercase tracking-widest mb-4">6. Modifications to Service</h2>
                <p>Watered reserves the right at any time and from time to time to modify or discontinue, temporarily or
                    permanently, the Service (or any part thereof) with or without notice.</p>
            </section>

            <p class="text-sm italic mt-12">Last Updated: {{ date('F d, Y') }}</p>
        </div>
    </div>
@endsection