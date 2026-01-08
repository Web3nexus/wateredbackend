<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $settings->site_name ?? 'Watered' }} - {{ $settings->tagline ?? 'The Ancient Spirits' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    {{-- Background --}}
    <div class="fixed inset-0 -z-10 bg-sea-deep">
        <div class="absolute inset-0 opacity-5"
            style="background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.15) 1px, transparent 0); background-size: 40px 40px;">
        </div>
        <div class="absolute top-1/4 right-0 w-[800px] h-[800px] bg-gold-antique/5 blur-[200px] rounded-full"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-sea-light/10 blur-[180px] rounded-full"></div>
    </div>

    {{-- Navigation --}}
    <nav class="border-b border-white/10 backdrop-blur-md bg-sea-deep/60 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                @if($settings->logo_path)
                    <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="Logo" class="h-10 w-10 object-contain">
                @else
                    <img src="{{ asset('images/watered-logo.png') }}" alt="Watered Logo" class="h-10 w-10 object-contain">
                @endif
                <span
                    class="font-heading text-xl text-gold-antique tracking-wider">{{ $settings->site_name ?? 'Watered' }}</span>
            </div>
            <div class="flex items-center gap-8">
                <a href="#traditions"
                    class="text-parchment/70 hover:text-gold-antique text-sm transition hidden md:block">Traditions</a>
                <a href="#rituals"
                    class="text-parchment/70 hover:text-gold-antique text-sm transition hidden md:block">Rituals</a>
                @auth('admin')
                    <a href="{{ url('/securegate') }}"
                        class="px-5 py-2 bg-gold-antique/10 border border-gold-antique/30 text-gold-antique rounded hover:bg-gold-antique/20 text-sm transition">Dashboard</a>
                @else
                    <a href="{{ url('/securegate/login') }}"
                        class="px-5 py-2 bg-gold-antique/10 border border-gold-antique/30 text-gold-antique rounded hover:bg-gold-antique/20 text-sm transition">Sign
                        in</a>
                @endauth
            </div>
        </div>
    </nav>

    <main>
        {{-- Hero Section - Asymmetric Layout --}}
        <section class="relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 py-16 md:py-24">
                <div class="grid md:grid-cols-5 gap-12 items-center">
                    {{-- Content - Takes 3 columns --}}
                    <div class="md:col-span-3 space-y-8">
                        <div>
                            <p class="text-gold-antique/90 mb-3 text-xs uppercase tracking-[0.2em] font-medium">
                                {{ $settings->hero_subtitle ?? 'The God of Seas & Voices' }}
                            </p>
                            <h1 class="text-6xl md:text-8xl mb-6 text-parchment font-heading leading-[0.9]">
                                {{ $settings->hero_title ?? 'Lord Uzih' }}
                            </h1>
                        </div>
                        <p class="text-lg md:text-xl text-parchment/80 leading-relaxed max-w-xl">
                            {{ $settings->hero_description ?? 'Accept The Reminder as the true Messenger of the Spirits. Through the sacred teachings, we cultivate spiritual, mental, and physical growth.' }}
                        </p>
                        <div class="flex flex-wrap items-center gap-4">
                            <a href="#traditions"
                                class="px-8 py-4 bg-gold-antique text-sea-deep font-semibold rounded-lg hover:bg-gold-antique/90 transition shadow-xl shadow-gold-antique/20">
                                {{ $settings->hero_cta_text ?? 'Explore Sacred Texts' }}
                            </a>
                            <a href="#rituals"
                                class="px-8 py-4 border-2 border-white/20 text-parchment font-semibold rounded-lg hover:bg-white/5 transition">
                                Learn About Rituals
                            </a>
                        </div>
                    </div>

                    {{-- Image - Takes 2 columns --}}
                    <div class="md:col-span-2 relative">
                        <div
                            class="relative aspect-[3/4] rounded-2xl overflow-hidden border border-white/10 shadow-2xl">
                            @if($settings->hero_image)
                                <img src="{{ asset('storage/' . $settings->hero_image) }}" alt="Lord Uzih"
                                    class="w-full h-full object-cover">
                            @else
                                <img src="{{ asset('images/lord-uzih-hero.png') }}" alt="Lord Uzih"
                                    class="w-full h-full object-cover">
                            @endif
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-sea-deep/60 via-transparent to-transparent">
                            </div>
                        </div>
                        {{-- Floating accent --}}
                        <div class="absolute -bottom-6 -left-6 w-32 h-32 bg-gold-antique/20 rounded-full blur-3xl">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Sacred Traditions Section --}}
        <section id="traditions" class="py-20 border-t border-white/10">
            <div class="max-w-7xl mx-auto px-6">
                <div class="mb-16 max-w-2xl">
                    <h2 class="text-4xl md:text-5xl mb-4 text-parchment font-heading">Sacred Traditions</h2>
                    <p class="text-parchment/60 text-lg">The foundational texts that guide our spiritual journey through
                        the ages</p>
                </div>

                @if($traditions->count() > 0)
                    <div class="grid md:grid-cols-3 gap-8">
                        @foreach($traditions as $index => $tradition)
                            <div
                                class="group relative bg-gradient-to-br from-white/5 to-white/[0.02] backdrop-blur-sm border border-white/10 rounded-2xl p-8 hover:border-gold-antique/40 transition-all duration-300 {{ $index === 0 ? 'md:col-span-2 md:row-span-2' : '' }}">
                                {{-- Icon --}}
                                <div
                                    class="w-14 h-14 bg-gold-antique/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-gold-antique/20 group-hover:scale-110 transition-all">
                                    <svg class="w-7 h-7 text-gold-antique" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.168.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>

                                <h3
                                    class="text-2xl {{ $index === 0 ? 'md:text-3xl' : '' }} mb-4 text-parchment font-heading group-hover:text-gold-antique transition-colors">
                                    {{ $tradition->name }}
                                </h3>
                                <p class="text-parchment/70 leading-relaxed mb-6 {{ $index === 0 ? 'text-lg' : 'text-sm' }}">
                                    {{ $tradition->description }}
                                </p>
                                <a href="#"
                                    class="inline-flex items-center gap-2 text-gold-antique text-sm font-medium hover:gap-3 transition-all">
                                    <span>Explore this tradition</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </a>

                                {{-- Decorative corner --}}
                                <div
                                    class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-gold-antique/10 to-transparent rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity">
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-24 bg-white/5 rounded-2xl border border-dashed border-white/10">
                        <div
                            class="w-16 h-16 mx-auto mb-4 bg-gold-antique/10 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gold-antique/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <p class="text-parchment/40 text-lg">Sacred texts are being prepared...</p>
                    </div>
                @endif
            </div>
        </section>

        {{-- Rituals Section - Split Layout --}}
        <section id="rituals" class="py-20 border-t border-white/10 bg-gradient-to-b from-transparent to-sea-light/20">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid md:grid-cols-2 gap-16 items-center">
                    {{-- Image Side --}}
                    <div class="order-2 md:order-1">
                        <div
                            class="relative aspect-[4/3] rounded-2xl overflow-hidden border border-white/10 shadow-2xl">
                            @if($settings->rituals_image)
                                <img src="{{ asset('storage/' . $settings->rituals_image) }}" alt="Sacred Rituals"
                                    class="w-full h-full object-cover">
                            @else
                                <img src="{{ asset('images/acceptance-ritual.png') }}" alt="Acceptance Ritual"
                                    class="w-full h-full object-cover">
                            @endif
                            <div
                                class="absolute inset-0 bg-gradient-to-tr from-sea-deep/80 via-sea-deep/40 to-transparent">
                            </div>

                            {{-- Floating quote card --}}
                            <div
                                class="absolute bottom-6 left-6 right-6 bg-sea-deep/90 backdrop-blur-md border border-gold-antique/30 rounded-xl p-6">
                                <p class="text-parchment/90 italic text-sm leading-relaxed">
                                    {{ $settings->about_quote ?? '"Humanity first. Worshipping the Ancient Spirits, cultivating growth, and rejecting the paths of old."' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Content Side --}}
                    <div class="order-1 md:order-2 space-y-8">
                        <div>
                            <h2 class="text-4xl md:text-5xl mb-4 text-parchment font-heading leading-tight">
                                {{ $settings->rituals_title ?? 'Sacred Practices' }}
                            </h2>
                            <p class="text-parchment/70 text-lg leading-relaxed">
                                {{ $settings->about_description ?? 'Through ancient ceremonies and spiritual teachings, we connect with the divine forces that guide our path.' }}
                            </p>
                        </div>

                        {{-- Ritual Cards --}}
                        <div class="space-y-6">
                            <div
                                class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-6 hover:border-gold-antique/30 transition-all group">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-10 h-10 flex-shrink-0 bg-gold-antique/10 rounded-lg flex items-center justify-center group-hover:bg-gold-antique/20 transition">
                                        <svg class="w-5 h-5 text-gold-antique" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-xl text-parchment font-heading mb-2">
                                            {{ $settings->ritual_acceptance_title ?? 'The Acceptance Ritual' }}
                                        </h3>
                                        <p class="text-parchment/70 text-sm leading-relaxed">
                                            {{ $settings->ritual_acceptance_description ?? 'Initiation into the deep mysteries through sacred water ceremonies and spiritual teachings.' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-6 hover:border-gold-antique/30 transition-all group">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-10 h-10 flex-shrink-0 bg-gold-antique/10 rounded-lg flex items-center justify-center group-hover:bg-gold-antique/20 transition">
                                        <svg class="w-5 h-5 text-gold-antique" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-xl text-parchment font-heading mb-2">
                                            {{ $settings->ritual_witnesses_title ?? 'The Watered Four Witnesses' }}
                                        </h3>
                                        <p class="text-parchment/70 text-sm leading-relaxed">
                                            {{ $settings->ritual_witnesses_description ?? 'Ancient proofs of spiritual truth that connect the physical and spiritual realms.' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- Footer --}}
    <footer class="border-t border-white/10 mt-32 bg-sea-deep/50 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="text-center md:text-left">
                    <p class="text-parchment/40 text-sm">&copy; {{ date('Y') }}
                        {{ $settings->site_name ?? 'The Hudorian Family' }}. All rights reserved.</p>
                </div>
                <div class="flex gap-8 text-sm">
                    <a href="/securegate" class="text-parchment/40 hover:text-gold-antique transition">Admin Portal</a>
                    <a href="#" class="text-parchment/40 hover:text-gold-antique transition">Privacy Policy</a>
                    <a href="#" class="text-parchment/40 hover:text-gold-antique transition">Contact Us</a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>