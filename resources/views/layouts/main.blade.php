<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Theme Color -->
    <meta name="theme-color" content="#0F172A">

    <!-- Flatpickr for advanced date/time selection -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        @yield('title', ($settings?->site_name ?? 'Watered') . ' - ' . ($settings?->tagline ?? 'The Ancient Spirits'))
    </title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if($settings?->favicon_url)
        <link rel="icon" type="image/x-icon" href="{{ $settings->favicon_url }}?v={{ time() }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('images/watered-logo.png') }}">
    @endif
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @yield('head')
</head>

<body class="min-h-screen flex flex-col">
    <style>
        :root {
            --color-app-blue:
                {{ $settings->primary_color ?? '#0077BE' }}
            ;
        }

        [x-cloak] {
            display: none !important;
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
    <nav class="border-b border-parchment/10 backdrop-blur-md bg-sea-deep/80 sticky top-0 z-50"
        x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-4">
                    @if($settings?->logo_url)
                        <img src="{{ $settings->logo_url }}?v={{ time() }}" alt="Logo" class="h-10 w-10 object-contain">
                    @else
                        <img src="{{ asset('images/watered-logo.png') }}" alt="Watered Logo"
                            class="h-10 w-10 object-contain">
                        <span
                            class="font-heading text-xl text-app-blue tracking-wider">{{ $settings?->site_name ?? 'Watered' }}</span>
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
                    class="text-parchment/70 hover:text-app-blue text-sm transition uppercase tracking-widest font-medium">Events</a>
                <a href="{{ route('home') }}#blog"
                    class="text-parchment/70 hover:text-app-blue text-sm transition uppercase tracking-widest font-medium">Blog</a>
                <a href="{{ route('home') }}#download"
                    class="px-5 py-2.5 bg-app-blue text-white text-xs font-bold rounded-full hover:bg-app-blue/90 transition shadow-lg shadow-app-blue/10 uppercase tracking-tighter">Get
                    Watered App</a>
            </div>

            <!-- Mobile Menu Button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-app-blue focus:outline-none">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"></path>
                    <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Mobile Menu Overlay -->
        <div x-show="mobileMenuOpen" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-5" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-5"
            class="md:hidden absolute top-full left-0 w-full bg-sea-deep border-b border-parchment/10 shadow-2xl py-6 px-6 flex flex-col gap-6">
            <a href="{{ route('home') }}#features" @click="mobileMenuOpen = false"
                class="text-parchment/70 hover:text-app-blue text-lg uppercase tracking-widest font-medium">Features</a>
            <a href="{{ route('home') }}#appointments" @click="mobileMenuOpen = false"
                class="text-parchment/70 hover:text-app-blue text-lg uppercase tracking-widest font-medium">Book
                Appointment</a>
            <a href="{{ route('events.index') }}"
                class="text-parchment/70 hover:text-app-blue text-lg uppercase tracking-widest font-medium">Events</a>
            <a href="{{ route('home') }}#blog" @click="mobileMenuOpen = false"
                class="text-parchment/70 hover:text-app-blue text-lg uppercase tracking-widest font-medium">Blog</a>
            <a href="{{ route('home') }}#download" @click="mobileMenuOpen = false"
                class="text-center px-5 py-3 bg-app-blue text-white text-sm font-bold rounded-full hover:bg-app-blue/90 transition shadow-lg shadow-app-blue/10 uppercase tracking-tighter">Get
                Watered App</a>
        </div>
    </nav>

    <main class="flex-grow">
        @yield('content')
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
                        <li><a href="{{ route('home') }}#features" class="hover:text-app-blue transition">Features</a>
                        </li>
                        <li><a href="{{ route('home') }}#blog" class="hover:text-app-blue transition">Insights</a></li>
                        <li><a href="{{ route('home') }}#download" class="hover:text-app-blue transition">Get the
                                App</a></li>
                    </ul>
                </div>
                <div class="space-y-6">
                    <h4 class="text-parchment font-bold uppercase tracking-widest text-sm">Support</h4>
                    <ul class="space-y-4 text-parchment/50 text-sm">
                        <li><a href="{{ route('privacy') }}" class="hover:text-app-blue transition lowercase">Privacy
                                Policy</a></li>
                        <li><a href="{{ route('terms') }}" class="hover:text-app-blue transition lowercase">Terms of
                                Service</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-app-blue transition lowercase">Contact
                                Us</a></li>
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

    @yield('scripts')
</body>

</html>