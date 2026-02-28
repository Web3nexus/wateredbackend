@extends('layouts.main')

@section('content')
    {{-- Hero Section - App Focused --}}
    <section class="relative overflow-hidden pt-16 pb-24">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid md:grid-cols-5 gap-16 items-center">
                <div class="md:col-span-3 space-y-10">
                    <div>
                        <p class="text-app-blue/90 mb-4 text-sm uppercase tracking-[0.3em] font-bold">
                            {{ $settings?->hero_subtitle ?? 'Spirituality for the Modern Seeker' }}
                        </p>
                        <h1 class="text-6xl md:text-8xl mb-6 text-parchment font-heading leading-[0.9] tracking-tight">
                            {{ $settings?->hero_title ?? 'Experience Divine Clarity' }}
                        </h1>
                    </div>
                    <p class="text-xl md:text-2xl text-parchment/70 leading-relaxed max-w-2xl">
                        {{ $settings?->hero_description ?? 'Connect with ancient wisdom and modern teachings. Watered is your daily companion for spiritual growth and a supportive community dedicated to humanity first.' }}
                    </p>
                    <div class="flex flex-wrap items-center gap-6">
                        <a href="#download"
                            class="px-10 py-5 bg-app-blue text-white font-bold rounded-xl hover:bg-app-blue/90 transition-all shadow-2xl shadow-app-blue/30 scale-110">
                            {{ $settings?->hero_cta_text ?? 'Start Your Journey' }}
                        </a>
                        <a href="#features" class="text-parchment/60 hover:text-app-blue transition font-medium">Explore
                            Features &rarr;</a>
                    </div>
                </div>
                <div class="md:col-span-2 relative">
                    <div
                        class="relative aspect-[3/4] md:aspect-[2/3] max-w-sm ml-auto rounded-[3rem] overflow-hidden border border-parchment/10 shadow-3xl bg-parchment/5">
                        @if($settings?->hero_image)
                            <img src="{{ $settings->hero_image_url }}" alt="Watered App" class="w-full h-full object-cover">
                        @else
                            <div
                                class="w-full h-full flex items-center justify-center bg-gradient-to-br from-app-blue/10 to-transparent p-12">
                                <img src="{{ asset('images/watered-logo.png') }}" alt="Watered Logo"
                                    class="w-48 h-48 object-contain opacity-50">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Dynamic Features 1 & 2 --}}
    <section id="features" class="space-y-32 py-32">
        @forelse($features->take(2) as $index => $feature)
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid md:grid-cols-5 gap-16 items-center">
                    <div class="md:col-span-2 {{ $feature->image_position === 'right' ? 'md:order-2' : '' }}">
                        <div
                            class="relative aspect-[4/3] rounded-[2.5rem] overflow-hidden border border-parchment/10 shadow-2xl bg-parchment/5">
                            @if($feature->image)
                                <img src="{{ asset('storage/' . $feature->image) }}" alt="{{ $feature->title }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-20 h-20 text-app-blue/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="md:col-span-3 space-y-6">
                        <h2 class="text-4xl md:text-5xl text-parchment font-heading leading-tight">{{ $feature->title }}
                        </h2>
                        <p class="text-lg text-parchment/70 leading-relaxed">{!! $feature->description !!}</p>
                    </div>
                </div>
            </div>
        @empty
            {{-- Placeholder Features if empty --}}
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid md:grid-cols-2 gap-20 items-center">
                    <div
                        class="relative aspect-[4/3] rounded-[2.5rem] overflow-hidden border border-parchment/10 shadow-2xl bg-parchment/5">
                        <div
                            class="w-full h-full flex items-center justify-center bg-gradient-to-br from-app-blue/5 to-transparent">
                            <svg class="w-20 h-20 text-app-blue/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.168.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <h2 class="text-4xl md:text-5xl text-parchment font-heading leading-tight">Sacred Wisdom & Daily
                            Teachings</h2>
                        <p class="text-lg text-parchment/70 leading-relaxed">Access a vast collection of ancient
                            spiritual texts and modern interpretations. Receive daily wisdom to nourish your spirit and
                            guide your growth.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </section>

    {{-- Download Section with Goo Animation --}}
    <section id="download" class="relative py-32 overflow-hidden">
        <div class="absolute inset-0 bg-app-blue/10 -z-10 bg-gradient-to-b from-sea-deep to-app-blue/5">
        </div>

        {{-- Goo Bubbles --}}
        <div class="absolute top-1/2 left-1/4 w-96 h-96 bg-app-blue/20 blur-[100px] animate-goo"></div>
        <div class="absolute top-1/3 right-1/4 w-[500px] h-[500px] bg-app-blue/15 blur-[120px] animate-goo"
            style="animation-delay: -4s;"></div>

        <div class="max-w-5xl mx-auto px-6">
            {{-- The "Box" --}}
            <div
                class="bg-sea-deep/80 backdrop-blur-xl border border-parchment/10 rounded-[4rem] p-12 md:p-20 text-center space-y-12 shadow-3xl relative overflow-hidden">
                {{-- Inner subtle glow --}}
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-app-blue/10 blur-[80px] rounded-full"></div>

                <div class="relative z-10 space-y-6">
                    <div class="space-y-4">
                        <h2 class="text-5xl md:text-7xl text-parchment font-heading leading-tight">Begin Your Path
                            <br /> Today
                        </h2>
                        <p class="text-xl text-parchment/60 max-w-2xl mx-auto">Download Watered and join a global
                            community seeking truth, clarity, and spiritual evolution.</p>
                    </div>

                    {{-- Actual Download Buttons - Side by Side --}}
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-6 pt-8">
                        <a href="{{ $settings?->android_download_url ?? '#' }}" target="_blank"
                            class="w-full sm:w-auto group flex items-center gap-4 px-8 py-5 bg-sea-deep border border-parchment/10 rounded-2xl hover:border-app-blue transition-all shadow-xl hover:-translate-y-1">
                            <svg class="w-10 h-10 text-app-blue" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M17.523 15.3414C16.92 15.3414 16.4297 15.8317 16.4297 16.4347C16.4297 17.0377 16.92 17.528 17.523 17.528C18.126 17.528 18.6163 17.0377 18.6163 16.4347C18.6163 15.8317 18.126 15.3414 17.523 15.3414ZM6.47702 15.3414C5.87402 15.3414 5.38372 15.8317 5.38372 16.4347C5.38372 17.0377 5.87402 17.528 6.47702 17.528C7.08002 17.528 7.57031 17.0377 7.57031 16.4347C7.57031 15.8317 7.08002 15.3414 6.47702 15.3414ZM17.9613 11.6256L19.7226 8.57463C19.8398 8.37135 19.7698 8.11142 19.5665 7.99424C19.3631 7.87706 19.1033 7.94703 18.9861 8.15042L17.2001 11.244C15.6841 10.5529 13.9189 10.1506 12 10.1506C10.0811 10.1506 8.31592 10.5529 6.7999 11.244L5.0139 8.15042C4.89672 7.94703 4.63689 7.87713 4.4335 7.99424C4.23011 8.11142 4.16013 8.37135 4.27731 8.57463L6.0387 11.6256C3.1207 13.2081 1.13401 16.148 1.01162 19.5912H22.9883C22.8659 16.148 20.8793 13.2081 17.9613 11.6256Z" />
                            </svg>
                            <div class="text-left">
                                <p class="text-[10px] text-parchment/40 uppercase font-bold tracking-widest mb-1">
                                    Get it on</p>
                                <p class="text-xl font-bold text-parchment leading-none">Google Play</p>
                            </div>
                        </a>

                        <a href="{{ $settings?->ios_download_url ?? '#' }}" target="_blank"
                            class="w-full sm:w-auto group flex items-center gap-4 px-8 py-5 bg-sea-deep border border-parchment/10 rounded-2xl hover:border-app-blue transition-all shadow-xl hover:-translate-y-1">
                            <svg class="w-10 h-10 text-app-blue" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M18.71 19.5c-.83 1.24-1.71 2.45-3.1 2.48-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z" />
                            </svg>
                            <div class="text-left">
                                <p class="text-[10px] text-parchment/40 uppercase font-bold tracking-widest mb-1">
                                    Download on</p>
                                <p class="text-xl font-bold text-parchment leading-none">App Store</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Dynamic Features 3 & 4 --}}
    <section class="space-y-32 py-32">
        @forelse($features->skip(2)->take(2) as $index => $feature)
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid md:grid-cols-5 gap-16 items-center">
                    <div class="md:col-span-2 {{ $feature->image_position === 'right' ? 'md:order-2' : '' }}">
                        <div
                            class="relative aspect-[4/3] rounded-[2.5rem] overflow-hidden border border-parchment/10 shadow-2xl bg-parchment/5">
                            @if($feature->image)
                                <img src="{{ asset('storage/' . $feature->image) }}" alt="{{ $feature->title }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-20 h-20 text-app-blue/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m9-10a4 4 0 100-8 4 4 0 000 8zm6 5h6m-3-3v6" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="md:col-span-3 space-y-6">
                        <h2 class="text-4xl md:text-5xl text-parchment font-heading leading-tight">{{ $feature->title }}
                        </h2>
                        <p class="text-lg text-parchment/70 leading-relaxed">{!! $feature->description !!}</p>
                    </div>
                </div>
            </div>
        @empty
            {{-- Placeholder Features if empty --}}
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid md:grid-cols-2 gap-20 items-center">
                    <div
                        class="md:order-2 relative aspect-[4/3] rounded-[2.5rem] overflow-hidden border border-parchment/10 shadow-2xl bg-parchment/5">
                        <div
                            class="w-full h-full flex items-center justify-center bg-gradient-to-br from-app-blue/5 to-transparent">
                            <svg class="w-20 h-20 text-app-blue/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m9-10a4 4 0 100-8 4 4 0 000 8zm6 5h6m-3-3v6" />
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <h2 class="text-4xl md:text-5xl text-parchment font-heading leading-tight">Sacred Rituals &
                            Community</h2>
                        <p class="text-lg text-parchment/70 leading-relaxed">Join a vibrant family of seekers.
                            Participate in guided rituals and connect with others on the same spiritual path wherever
                            you are in the world.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </section>

    {{-- Teachings Section --}}
    <section id="teachings" class="py-32 bg-parchment/[0.02] border-y border-parchment/10">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-8">
                <div class="max-w-2xl">
                    <h2 class="text-5xl md:text-6xl text-parchment font-heading mb-4">Lords Uzih's Teachings</h2>
                    <p class="text-lg text-parchment/60">Thoughts and wisdom from our spiritual leaders and community
                        explorers.</p>
                </div>
                <a href="{{ route('teachings.index') }}" class="text-app-blue font-bold hover:underline mb-2">View All
                    Teachings &rarr;</a>
            </div>

            @if($teachings->count() > 0)
                <div class="grid md:grid-cols-3 gap-10">
                    @foreach($teachings as $post)
                        <article class="group relative space-y-6">
                            <div
                                class="relative aspect-video rounded-3xl overflow-hidden border border-parchment/10 shadow-lg mb-6">
                                @if($post->featured_image)
                                    <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @else
                                    <div class="w-full h-full bg-parchment/10 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-app-blue/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM3 8h10M9 12h5M9 16h5" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="space-y-3">
                                <p class="text-xs text-app-blue font-bold uppercase tracking-widest">
                                    {{ $post->published_at?->format('F d, Y') ?? $post->created_at->format('F d, Y') }}
                                </p>
                                <h3 class="text-2xl text-parchment font-heading group-hover:text-app-blue transition">
                                    {{ $post->title }}
                                </h3>
                                <p class="text-parchment/60 text-sm line-clamp-3 leading-relaxed">{!! $post->summary !!}</p>
                            </div>
                            <a href="{{ route('teachings.show', $post->slug) }}"
                                class="inline-block pt-2 text-app-blue font-semibold border-b border-transparent hover:border-app-blue transition">Read
                                More</a>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="text-center py-24 bg-parchment/5 rounded-3xl border border-dashed border-parchment/10">
                    <p class="text-parchment/40">Our scribes are currently crafting new teachings. Please check back
                        soon.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- Appointment Booking Section --}}
    <section id="appointments" class="py-32 bg-app-blue/5 border-y border-app-blue/10">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-20 items-center">
                <div class="space-y-10">
                    <div>
                        <h2 class="text-5xl md:text-6xl text-parchment font-heading mb-6 tracking-tight">Book an
                            <br /> Appointment
                        </h2>
                        <p class="text-xl text-parchment/60 leading-relaxed">Seek guidance and clarity. Schedule a
                            private consultation with Lord Uzih or our spiritual advisors.</p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-10 h-10 rounded-full bg-app-blue/10 flex items-center justify-center text-app-blue shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-parchment">Instant Confirmation</h4>
                                <p class="text-sm text-parchment/50">Receive your tracking code immediately via
                                    email.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div
                                class="w-10 h-10 rounded-full bg-app-blue/10 flex items-center justify-center text-app-blue shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-parchment">Secure Payments</h4>
                                <p class="text-sm text-parchment/50">Powered by Paystack for 100% security.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <form id="appointmentForm" class="space-y-6" x-data="bookingSystem()" @submit.prevent="submitForm">
                    @csrf
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-parchment/40 uppercase tracking-widest">Full
                                Name</label>
                            <input type="text" name="full_name" required
                                class="w-full px-5 py-4 bg-parchment/5 border border-parchment/10 rounded-xl focus:border-app-blue outline-none transition text-parchment">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-parchment/40 uppercase tracking-widest">Phone
                                Number</label>
                            <input type="tel" name="phone" required
                                class="w-full px-5 py-4 bg-parchment/5 border border-parchment/10 rounded-xl focus:border-app-blue outline-none transition text-parchment">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-parchment/40 uppercase tracking-widest">Email
                            Address</label>
                        <input type="email" name="email" required
                            class="w-full px-5 py-4 bg-parchment/5 border border-parchment/10 rounded-xl focus:border-app-blue outline-none transition text-parchment">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-parchment/40 uppercase tracking-widest">Category</label>
                            <select x-model="selectedCategory" @change="updateSubTypes()"
                                class="w-full px-5 py-4 bg-parchment/5 border border-parchment/10 rounded-xl focus:border-app-blue outline-none transition text-parchment">
                                <option value="">Select Category</option>
                                <option value="temple_visit">Visit the Temple (FREE)</option>
                                <option value="lord_uzih">Talk to Lord Uzih</option>
                            </select>
                        </div>

                        <div class="space-y-2" x-show="selectedCategory === 'lord_uzih'">
                            <label class="text-sm font-bold text-parchment/40 uppercase tracking-widest">Consultation
                                Type</label>
                            <select name="consultation_type_id" x-model="selectedSubType"
                                :required="selectedCategory === 'lord_uzih'" :disabled="selectedCategory !== 'lord_uzih'"
                                class="w-full px-5 py-4 bg-parchment/5 border border-parchment/10 rounded-xl focus:border-app-blue outline-none transition text-parchment">
                                <option value="">Select Type</option>
                                <template x-for="type in filteredTypes" :key="type.id">
                                    <option :value="type.id" x-text="`${type.name} - ₦${formatNumber(type.price)}`">
                                    </option>
                                </template>
                            </select>
                        </div>
                        <template x-if="selectedCategory === 'temple_visit'">
                            <input type="hidden" name="consultation_type_id" :value="templeVisitTypeId">
                        </template>
                    </div>

                    <div x-show="selectedCategory === 'temple_visit'"
                        class="p-4 bg-app-blue/10 border border-app-blue/20 rounded-xl">
                        <p class="text-sm text-app-blue font-medium italic">"Visitors may not meet Lord Uzih the
                            priest during temple visits."</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-bold text-parchment/40 uppercase tracking-widest">Preferred Date
                            & Time</label>
                        <input type="text" id="start_time_picker" name="start_time" x-model="startTime" required
                            class="w-full px-5 py-4 bg-parchment/5 border border-parchment/10 rounded-xl focus:border-app-blue outline-none transition text-parchment"
                            placeholder="Select Day & Hour">
                        <p x-show="timeError" class="text-xs text-red-400 mt-1" x-text="timeError"></p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-bold text-parchment/40 uppercase tracking-widest">Notes
                            (Optional)</label>
                        <textarea name="notes" rows="3"
                            class="w-full px-5 py-4 bg-parchment/5 border border-parchment/10 rounded-xl focus:border-app-blue outline-none transition text-parchment"
                            placeholder="Any specific questions..."></textarea>
                    </div>

                    <button type="submit"
                        :disabled="isSubmitting || !!timeError || !selectedCategory || (selectedCategory === 'lord_uzih' && !selectedSubType)"
                        class="w-full py-5 bg-app-blue text-white font-bold rounded-xl hover:bg-app-blue/90 transition shadow-xl shadow-app-blue/20 uppercase tracking-widest disabled:opacity-50 disabled:cursor-not-allowed"
                        x-text="isSubmitting ? 'Processing...' : (selectedCategory === 'temple_visit' ? 'Book Temple Visit' : 'Confirm & Pay')">
                    </button>
                </form>
                <div id="appointmentMessage" class="hidden mt-6 p-4 rounded-xl text-center"></div>
            </div>
        </div>
    </section>

    {{-- Rituals Section --}}
    <section id="rituals" class="py-32">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-20 items-center">
                <div class="space-y-10">
                    <div>
                        <h2 class="text-5xl md:text-6xl text-parchment font-heading mb-6 tracking-tight">Sacred
                            Practices</h2>
                        <p class="text-xl text-parchment/60 leading-relaxed">Experience a connection to the divine
                            through guided rituals and spiritual disciplines developed over generations.</p>
                    </div>
                    <div class="space-y-6">
                        <div
                            class="p-8 bg-parchment/5 border border-parchment/10 rounded-3xl hover:border-app-blue/30 transition shadow-inner">
                            <h3 class="text-2xl text-parchment font-heading mb-3">
                                {{ $settings?->ritual_acceptance_title ?? 'The Acceptance Ritual' }}
                            </h3>
                            <p class="text-parchment/60 leading-relaxed">
                                {{ $settings?->ritual_acceptance_description ?? 'Initiation into the deep mysteries through sacred water ceremonies and spiritual teachings.' }}
                            </p>
                        </div>
                        <div
                            class="p-8 bg-parchment/5 border border-parchment/10 rounded-3xl hover:border-app-blue/30 transition shadow-inner">
                            <h3 class="text-2xl text-parchment font-heading mb-3">
                                {{ $settings?->ritual_witnesses_title ?? 'The Watered Four Witnesses' }}
                            </h3>
                            <p class="text-parchment/60 leading-relaxed">
                                {{ $settings?->ritual_witnesses_description ?? 'Ancient proofs of spiritual truth that connect the physical and spiritual realms.' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="aspect-[3/4] rounded-[4rem] overflow-hidden shadow-2xl border border-parchment/10">
                        @if($settings?->rituals_image)
                            <img src="{{ $settings->rituals_image_url }}" alt="Sacred Rituals"
                                class="w-full h-full object-cover">
                        @else
                            <img src="{{ asset('images/acceptance-ritual.png') }}" alt="Acceptance Ritual"
                                class="w-full h-full object-cover grayscale brightness-75">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- About Uzih Section --}}
    <section id="uzih" class="py-32 bg-app-blue/3 rounded-[5rem] mx-6 mb-32 border border-app-blue/10">
        <div class="max-w-5xl mx-auto px-6 text-center space-y-12">
            <div class="w-32 h-32 mx-auto overflow-hidden rounded-full border-2 border-app-blue p-1 bg-sea-deep">
                <img src="{{ asset('images/lord-uzih-hero.png') }}" alt="Lord Uzih"
                    class="w-full h-full object-cover rounded-full">
            </div>
            <div class="space-y-6">
                <h2 class="text-4xl md:text-5xl text-parchment font-heading">The Messenger: Lord Uzih</h2>
                <p class="text-xl text-parchment/70 leading-relaxed italic">
                    "{{ $settings?->about_quote ?? 'Accept The Reminder as the true Messenger of the Spirits. Through the sacred teachings, we cultivate spiritual, mental, and physical growth.' }}"
                </p>
            </div>
            <p class="text-parchment/60 max-w-3xl mx-auto leading-relaxed">
                {{ $settings?->about_description ?? 'Through ancient ceremonies and spiritual teachings, we connect with the divine forces that guide our path. Lord Uzih serves as the vessel for these timeless truths, bringing clarity to those who seek.' }}
            </p>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        function bookingSystem() {
            return {
                allTypes: [], filteredTypes: [], selectedCategory: '', selectedSubType: '', templeVisitTypeId: '', startTime: '', timeError: '', isSubmitting: false, fp: null,
                async init() { 
                    try { 
                        const response = await fetch('/api/v1/consultation-types'); 
                        const result = await response.json(); 
                        this.allTypes = result.data || []; 
                        this.initFlatpickr(); 
                    } catch (error) { 
                        console.error('Failed to fetch types:', error); 
                    } 
                },
                initFlatpickr() {
                    this.fp = flatpickr("#start_time_picker", {
                        enableTime: true, dateFormat: "Y-m-d H:i", minDate: "today", theme: "dark", disable: [(date) => {
                            if (!this.selectedCategory) return true; 
                            if (this.selectedCategory === 'lord_uzih') {
                                // Only Tue(2), Wed(3), Fri(5)
                                return ![2, 3, 5].includes(date.getDay());
                            }
                            return false; // Temple visits all days
                        }],
                        onChange: (selectedDates, dateStr) => {
                            this.startTime = dateStr;
                            this.validateTime();
                        },
                        onOpen: (selectedDates, dateStr, instance) => {
                            // Ensure instance is ready for category-specific disabling
                        }
                    });
                },
                updateSubTypes() { 
                    this.filteredTypes = this.allTypes.filter(t => t.category === this.selectedCategory); 
                    if (this.selectedCategory === 'temple_visit' && this.filteredTypes.length > 0) { 
                        this.templeVisitTypeId = this.filteredTypes[0].id; 
                    } else { 
                        this.templeVisitTypeId = ''; 
                    } 
                    this.selectedSubType = ''; 
                    this.startTime = ''; 
                    if (this.fp) this.fp.clear(); 
                    this.validateTime(); 
                },
                validateTime() { 
                    if(!this.startTime || !this.selectedCategory) { this.timeError = ''; return; }
                    const date = new Date(this.startTime); const day = date.getDay(); // 0-6 (Sun-Sat)                 
                    const hour = date.getHours();                 
                    const minutes = date.getMinutes();                 
                    const timeInt = hour * 100 + minutes;

                    if (this.selectedCategory === 'temple_visit') {                     
                        // Mon-Wed, Fri, Sun: 10:00 AM – 4:00 PM (1000 - 1600)                     
                        // Thu, Sat: 7:00 AM – 6:00 PM (0700 - 1800)                     
                        if ([1, 2, 3, 5, 0].includes(day)) {                         
                            if (timeInt < 1000 || timeInt > 1600) {                             
                                this.timeError = 'Temple visits are only available 10:00 AM - 4:00 PM on this day.';                             
                                return;                         
                            }                     
                        } else if ([4, 6].includes(day)) {                         
                            if (timeInt < 700 || timeInt > 1800) {                             
                                this.timeError = 'Temple visits are only available 7:00 AM - 6:00 PM on this day.';                             
                                return;                         
                            }                     
                        }                 
                    } else if (this.selectedCategory === 'lord_uzih') {                     
                        // Tue, Wed, Fri: 10:00 AM – 4:00 PM                     
                        if (![2, 3, 5].includes(day)) {                         
                            this.timeError = 'Consultations with Lord Uzih are only available on Tuesday, Wednesday, and Friday.';                         
                            return;                     
                        }                     
                        if (timeInt < 1000 || timeInt > 1600) {                         
                            this.timeError = 'Consultations with Lord Uzih are only available 10:00 AM - 4:00 PM.';                         
                            return;                     
                        }                 
                    }
                    this.timeError = '';
                },
                    formatNumber(num) { return new Intl.NumberFormat('en-NG').format(num); },
                                 async submitForm(e) {
                        const form = e.target; const msg = document.getElementById('appointmentMessage');
                        this.isSubmitting = true; msg.classList.add('hidden');
                        try {
                            const formData = new FormData(form); const response = await fetch('/api/v1/appointments/guest', { method: 'POST', headers: { 'Accept': 'application/json', }, body: formData });
                            const result = await response.json();
                            if (response.ok) {
                                msg.innerText = 'Success! Redirecting...'; msg.classList.remove('hidden', 'bg-red-100/10', 'text-red-400', 'border-red-400/20'); msg.classList.add('bg-green-100/10', 'text-green-400', 'border-green-400/20', 'border');
                                if (result.payment_url) { window.location.href = result.payment_url; } else { msg.innerText = 'Appointment confirmed! Tracking code: ' + result.data.appointment_code; form.reset(); this.selectedCategory = ''; this.isSubmitting = false; }
                            } else { throw new Error(result.message || 'Failed to create appointment'); }
                        } catch (error) { msg.innerText = error.message; msg.classList.remove('hidden', 'bg-green-100/10', 'text-green-400', 'border-green-400/20'); msg.classList.add('bg-red-100/10', 'text-red-400', 'border-red-400/20', 'border'); this.isSubmitting = false; }
                    }
                }
            }
    </script>
@endsection