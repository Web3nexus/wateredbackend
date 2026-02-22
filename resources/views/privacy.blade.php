@extends('layouts.main')

@section('title', 'Privacy Policy - ' . ($settings?->site_name ?? 'Watered'))

@section('content')
    <div class="py-24 max-w-4xl mx-auto px-6">
        <h1 class="text-5xl md:text-6xl text-parchment font-heading mb-12">Privacy Policy</h1>

        <div class="prose prose-invert prose-lg max-w-none text-parchment/70 space-y-8">
            <section>
                <h2 class="text-2xl text-app-blue font-bold uppercase tracking-widest mb-4">Introduction</h2>
                <p>Welcome to Watered. We respect your privacy and are committed to protecting your personal data. This
                    privacy policy will inform you about how we look after your personal data when you visit our website or
                    use our application (regardless of where you visit it from) and tell you about your privacy rights and
                    how the law protects you.</p>
            </section>

            <section>
                <h2 class="text-2xl text-app-blue font-bold uppercase tracking-widest mb-4">The Data We Collect</h2>
                <p>Personal data, or personal information, means any information about an individual from which that person
                    can be identified. It does not include data where the identity has been removed (anonymous data).</p>
                <p>We may collect, use, store and transfer different kinds of personal data about you which we have grouped
                    together as follows:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li><strong>Identity Data</strong> includes first name, last name, username or similar identifier.</li>
                    <li><strong>Contact Data</strong> includes email address and telephone numbers.</li>
                    <li><strong>Technical Data</strong> includes internet protocol (IP) address, your login data, browser
                        type and version, time zone setting and location, browser plug-in types and versions, operating
                        system and platform, and other technology on the devices you use to access this website.</li>
                    <li><strong>Usage Data</strong> includes information about how you use our website, products and
                        services.</li>
                    <li><strong>Marketing and Communications Data</strong> includes your preferences in receiving marketing
                        from us and our third parties and your communication preferences.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl text-app-blue font-bold uppercase tracking-widest mb-4">How We Use Your Data</h2>
                <p>We will only use your personal data when the law allows us to. Most commonly, we will use your personal
                    data in the following circumstances:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Where we need to perform the contract we are about to enter into or have entered into with you.</li>
                    <li>Where it is necessary for our legitimate interests (or those of a third party) and your interests
                        and fundamental rights do not override those interests.</li>
                    <li>Where we need to comply with a legal obligation.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl text-app-blue font-bold uppercase tracking-widest mb-4">Data Security</h2>
                <p>We have put in place appropriate security measures to prevent your personal data from being accidentally
                    lost, used or accessed in an unauthorized way, altered or disclosed. In addition, we limit access to
                    your personal data to those employees, agents, contractors and other third parties who have a business
                    need to know.</p>
            </section>

            <section>
                <h2 class="text-2xl text-app-blue font-bold uppercase tracking-widest mb-4">Contact Us</h2>
                <p>If you have any questions about this privacy policy or our privacy practices, please contact us via our
                    contact page.</p>
            </section>

            <p class="text-sm italic mt-12">Last Updated: {{ date('F d, Y') }}</p>
        </div>
    </div>
@endsection