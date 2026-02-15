<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $event->title }} - {{ $settings?->site_name ?? 'Watered' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if($settings?->favicon_url)
        <link rel="icon" type="image/x-icon" href="{{ $settings->favicon_url }}?v={{ time() }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('images/watered-logo.png') }}">
    @endif
</head>

<body class="bg-sea-deep text-parchment font-sans selection:bg-app-blue selection:text-white min-h-screen flex flex-col">
    <style>
        :root {
            --color-app-blue:
                {{ $settings->primary_color ?? '#0077BE' }}
            ;
        }
    </style>
    {{-- Background --}}
    <div class="fixed inset-0 -z-10 bg-sea-deep">
        <div class="absolute inset-0 opacity-5"
            style="background-image: radial-gradient(circle at 2px 2px, rgba(0,119,190,0.1) 1px, transparent 0); background-size: 40px 40px;">
        </div>
        <div class="absolute top-1/4 right-0 w-[800px] h-[800px] bg-app-blue/10 blur-[200px] rounded-full"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-app-blue/5 blur-[180px] rounded-full"></div>
    </div>

    {{-- Navigation --}}
    <nav class="border-b border-parchment/10 backdrop-blur-md bg-sea-deep/80 sticky top-0 z-50" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-4">
                    @if($settings?->logo_url)
                        <img src="{{ $settings->logo_url }}?v={{ time() }}" alt="Logo" class="h-10 w-10 object-contain">
                    @else
                        <img src="{{ asset('images/watered-logo.png') }}" alt="Watered Logo" class="h-10 w-10 object-contain">
                        <span class="font-heading text-xl text-app-blue tracking-wider">{{ $settings?->site_name ?? 'Watered' }}</span>
                    @endif
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ route('home') }}#features"
                    class="text-parchment/70 hover:text-app-blue text-sm transition uppercase tracking-widest font-medium">Features</a>
                <a href="{{ route('home') }}#appointments"
                    class="text-parchment/70 hover:text-app-blue text-sm transition uppercase tracking-widest font-medium">Book
                    Appointment</a>
                <a href="{{ route('events.index') }}"
                    class="text-app-blue text-sm transition uppercase tracking-widest font-medium">Events</a>
                <a href="{{ route('home') }}#blog"
                    class="text-parchment/70 hover:text-app-blue text-sm transition uppercase tracking-widest font-medium">Blog</a>
                <a href="{{ route('home') }}#download"
                    class="px-8 py-3.5 bg-app-blue text-white text-xs font-bold rounded-full hover:bg-app-blue/90 transition shadow-lg shadow-app-blue/10 uppercase tracking-tighter">Get
                    Watered App</a>
            </div>

            <!-- Mobile Menu Button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-app-blue focus:outline-none">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"></path>
                    <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Mobile Menu Overlay -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-5"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-5"
             class="md:hidden absolute top-full left-0 w-full bg-sea-deep border-b border-parchment/10 shadow-2xl py-6 px-6 flex flex-col gap-6"
             style="display: none;">
            <a href="{{ route('home') }}#features" @click="mobileMenuOpen = false"
                class="text-parchment/70 hover:text-app-blue text-lg uppercase tracking-widest font-medium">Features</a>
            <a href="{{ route('home') }}#appointments" @click="mobileMenuOpen = false"
                class="text-parchment/70 hover:text-app-blue text-lg uppercase tracking-widest font-medium">Book
                Appointment</a>
            <a href="{{ route('events.index') }}"
                class="text-app-blue text-lg uppercase tracking-widest font-medium">Events</a>
            <a href="{{ route('home') }}#blog" @click="mobileMenuOpen = false"
                class="text-parchment/70 hover:text-app-blue text-lg uppercase tracking-widest font-medium">Blog</a>
            <a href="{{ route('home') }}#download" @click="mobileMenuOpen = false"
                class="text-center px-8 py-3.5 bg-app-blue text-white text-sm font-bold rounded-full hover:bg-app-blue/90 transition shadow-lg shadow-app-blue/10 uppercase tracking-tighter">Get
                Watered App</a>
        </div>
    </nav>

    {{-- Content --}}
    <main class="pt-32 pb-64 grow">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-start">
                <!-- Left: Event Info -->
                <div class="space-y-12">
                    <div class="rounded-[3rem] overflow-hidden border border-parchment/10 shadow-3xl bg-parchment/5 aspect-16/10">
                        <img src="{{ $event->banner_image_url ?? asset('images/watered-logo.png') }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                    </div>
                    
                    <div class="space-y-6">
                        <div class="flex items-center gap-4 text-app-blue text-sm font-bold uppercase tracking-[0.3em]">
                            <span>{{ $event->event_date?->format('l, F j, Y') ?? 'TBA' }}</span>
                            <span class="w-1.5 h-1.5 bg-parchment/20 rounded-full"></span>
                            <span>{{ $event->event_time ? date('g:i A', strtotime($event->event_time)) : 'TBA' }}</span>
                        </div>

                        <h1 class="font-heading text-5xl md:text-7xl text-parchment tracking-tighter leading-none uppercase">{{ $event->title }}</h1>
                        
                        <div class="prose prose-invert max-w-none text-parchment/60 text-lg leading-relaxed">
                            {!! nl2br(e($event->description)) !!}
                        </div>

                        <div class="grid grid-cols-2 gap-10 pt-10 border-t border-parchment/10">
                            <div class="space-y-2">
                                <span class="block text-parchment/30 text-[10px] font-bold uppercase tracking-widest">Location</span>
                                <p class="text-parchment text-lg font-medium">{{ $event->location ?? 'Online / TBA' }}</p>
                            </div>
                            <div class="space-y-2">
                                <span class="block text-parchment/30 text-[10px] font-bold uppercase tracking-widest">Access</span>
                                <p class="text-app-blue font-heading text-2xl uppercase tracking-wider">
                                    @if($event->is_paid)
                                        @if(($settings->currency_position ?? 'before') === 'before')
                                            {{ $settings->currency_symbol ?? '$' }}{{ number_format($event->price, 2) }}
                                        @else
                                            {{ number_format($event->price, 2) }}{{ $settings->currency_symbol ?? '$' }}
                                        @endif
                                    @else
                                        FREE
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Registration Form -->
                <div class="lg:sticky lg:top-32">
                    <div class="bg-parchment/5 border border-parchment/10 rounded-[3rem] p-10 lg:p-16 backdrop-blur-sm relative overflow-hidden group">
                        <div class="absolute inset-0 bg-app-blue/5 opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                        
                        <h2 class="relative font-heading text-4xl text-parchment mb-10 tracking-tight uppercase">Reserve Your Spot</h2>
                        
                        @if(session('success'))
                            <div class="relative bg-green-500/10 border border-green-500/30 text-green-400 p-6 rounded-2xl mb-10 text-sm animate-pulse">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="relative bg-red-500/10 border border-red-500/30 text-red-400 p-6 rounded-2xl mb-10 text-sm">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form action="{{ route('events.register', $event) }}" method="POST" class="relative space-y-8">
                            @csrf
                            <div class="space-y-2">
                                <label class="block text-parchment/40 text-[10px] font-bold uppercase tracking-widest ml-1">Full Name</label>
                                <input type="text" name="full_name" value="{{ auth()->user()?->name }}" required 
                                       class="w-full bg-parchment/5 border border-parchment/10 rounded-2xl px-6 py-4 text-parchment focus:outline-none focus:border-app-blue transition-all placeholder:text-parchment/20"
                                       placeholder="Enter your name">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-parchment/40 text-[10px] font-bold uppercase tracking-widest ml-1">Email Address</label>
                                <input type="email" name="email" value="{{ auth()->user()?->email }}" required 
                                       class="w-full bg-parchment/5 border border-parchment/10 rounded-2xl px-6 py-4 text-parchment focus:outline-none focus:border-app-blue transition-all placeholder:text-parchment/20"
                                       placeholder="you@example.com">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-parchment/40 text-[10px] font-bold uppercase tracking-widest ml-1">Phone Number</label>
                                <input type="tel" name="phone" value="{{ auth()->user()?->phone }}" required 
                                       class="w-full bg-parchment/5 border border-parchment/10 rounded-2xl px-6 py-4 text-parchment focus:outline-none focus:border-app-blue transition-all placeholder:text-parchment/20"
                                       placeholder="+1 (555) 000-0000">
                            </div>

                            <button type="submit" class="w-full py-4 px-8 bg-app-blue text-white font-bold rounded-full hover:bg-white hover:text-sea-deep transition-all transform hover:-translate-y-1 shadow-2xl shadow-app-blue/20 uppercase tracking-[0.2em] mt-10">
                                {{ $event->is_paid ? 'Pay & Register' : 'Join Now' }}
                            </button>
                            
                            <div class="flex items-center justify-center gap-3 pt-6 opacity-30">
                                @if($event->is_paid)
                                    <span class="text-[10px] font-bold uppercase tracking-widest">Secure Payment via Paystack</span>
                                @else
                                    <span class="text-[10px] font-bold uppercase tracking-widest">Community Event &bull; Open to All</span>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="border-t border-parchment/10 bg-sea-deep py-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-16 mb-20 text-center md:text-left">
                <div class="md:col-span-2 space-y-6">
                    <div class="flex items-center gap-4 justify-center md:justify-start">
                        @if($settings?->logo_url)
                            <img src="{{ $settings->logo_url }}" alt="Logo" class="h-12 w-12 object-contain">
                        @else
                            <img src="{{ asset('images/watered-logo.png') }}" alt="Watered Logo"
                                class="h-12 w-12 object-contain">
                            <span
                                class="font-heading text-2xl text-app-blue tracking-wider uppercase">{{ $settings?->site_name ?? 'Watered' }}</span>
                        @endif
                    </div>
                    <p class="text-parchment/50 max-w-sm">Elevating humanity through ancient wisdom, community, and
                        divine connection. Your journey to clarity starts here.</p>
                </div>
                <div class="space-y-6">
                    <h4 class="text-parchment font-bold uppercase tracking-widest text-sm">Navigation</h4>
                    <ul class="space-y-4 text-parchment/50 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-app-blue transition">Home</a></li>
                        <li><a href="{{ route('home') }}#features" class="hover:text-app-blue transition">Features</a></li>
                        <li><a href="{{ route('home') }}#blog" class="hover:text-app-blue transition">Insights</a></li>
                        <li><a href="{{ route('home') }}#download" class="hover:text-app-blue transition">Get the App</a></li>
                    </ul>
                </div>
                <div class="space-y-6">
                    <h4 class="text-parchment font-bold uppercase tracking-widest text-sm">Support</h4>
                    <ul class="space-y-4 text-parchment/50 text-sm">
                        <li><a href="#" class="hover:text-app-blue transition">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-app-blue transition">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-app-blue transition">Contact Us</a></li>
                    </ul>
                </div>
            </div>
            <div class="pt-10 border-t border-parchment/5 flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-parchment/30 text-xs tracking-widest">&copy; {{ date('Y') }}
                    {{ $settings?->site_name ?? 'Watered' }}. ALL RIGHTS RESERVED.
                </p>
                <div class="flex gap-6">
                    {{-- Social Icons could go here --}}
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
