@extends('layouts.main')

@section('title', 'Account Deletion - ' . ($settings?->site_name ?? 'Watered'))

@section('content')
    <main class="pt-32 pb-64">
        <div class="max-w-4xl mx-auto px-6">
            <div class="text-center mb-16">
                <p class="text-app-blue/90 mb-4 text-sm uppercase tracking-[0.3em] font-bold">Data Privacy</p>
                <h1 class="font-heading text-5xl md:text-7xl text-parchment mb-6 tracking-tighter uppercase">Account
                    Deletion</h1>
                <p class="text-parchment/60 text-lg leading-relaxed">We respect your right to privacy and control over your
                    data.</p>
            </div>

            <div class="prose prose-invert max-w-none space-y-12">
                <section class="bg-parchment/5 border border-parchment/10 rounded-[2rem] p-8 md:p-12">
                    <h2 class="text-2xl font-heading text-parchment mb-6 uppercase">How to Delete Your Account</h2>
                    <p class="text-parchment/70 leading-relaxed mb-6">
                        You can request the deletion of your account and all associated data directly through the Watered
                        mobile application or by contacting our support team.
                    </p>

                    <div class="space-y-8">
                        <div class="flex gap-6">
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-app-blue/20 rounded-full flex items-center justify-center text-app-blue font-bold">
                                1</div>
                            <div>
                                <h3 class="text-lg font-bold text-parchment mb-2">Via the Mobile App</h3>
                                <p class="text-parchment/50 text-sm">Open the app, go to <strong>Profile</strong> &rarr;
                                    <strong>Edit Profile</strong>, and tap the <strong>Delete Account</strong> button at the
                                    bottom of the screen. You will be asked to confirm your choice.</p>
                            </div>
                        </div>

                        <div class="flex gap-6">
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-app-blue/20 rounded-full flex items-center justify-center text-app-blue font-bold">
                                2</div>
                            <div>
                                <h3 class="text-lg font-bold text-parchment mb-2">Via Email Request</h3>
                                <p class="text-parchment/50 text-sm">Send an email to <a
                                        href="mailto:{{ $settings?->contact_email ?? 'support@watered.app' }}"
                                        class="text-app-blue">{{ $settings?->contact_email ?? 'support@watered.app' }}</a>
                                    from the email address associated with your account. Please include your full name and a
                                    request to delete your account.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="space-y-6">
                    <h2 class="text-2xl font-heading text-parchment uppercase">What happens next?</h2>
                    <div class="grid md:grid-cols-2 gap-8">
                        <div class="space-y-4">
                            <h3 class="text-app-blue font-bold uppercase tracking-widest text-xs">Data Removal</h3>
                            <p class="text-parchment/50 text-sm leading-relaxed">Once confirmed, your profile information,
                                preferences, and personal data are permanently removed from our active databases.</p>
                        </div>
                        <div class="space-y-4">
                            <h3 class="text-app-blue font-bold uppercase tracking-widest text-xs">Processing Time</h3>
                            <p class="text-parchment/50 text-sm leading-relaxed">In-app deletions are immediate. Email
                                requests are typically processed within 48-72 hours.</p>
                        </div>
                    </div>
                </section>

                <div class="pt-12 border-t border-parchment/10 text-center">
                    <p class="text-parchment/30 text-xs tracking-widest uppercase">
                        Need assistance? <a href="{{ route('contact') }}"
                            class="text-app-blue hover:text-parchment transition">Contact Support</a>
                    </p>
                </div>
            </div>
        </div>
    </main>
@endsection