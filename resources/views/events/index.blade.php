<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Events - {{ $settings?->site_name ?? 'Watered' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if($settings?->favicon_url)
        <link rel="icon" type="image/x-icon" href="{{ $settings->favicon_url }}?v={{ time() }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('images/watered-logo.png') }}">
    @endif
</head>

<body
    class="bg-sea-deep text-parchment font-sans selection:bg-app-blue selection:text-white min-h-screen flex flex-col">
    <style>
        :root {
            --color-app-blue: #0077BE;
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
    <nav class="border-b border-parchment/10 backdrop-blur-md bg-sea-deep/80 sticky top-0 z-50">
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
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}#features"
                    class="text-parchment/70 hover:text-app-blue text-sm transition hidden md:block uppercase tracking-widest font-medium">Features</a>
                <a href="{{ route('home') }}#appointments"
                    class="text-parchment/70 hover:text-app-blue text-sm transition hidden md:block uppercase tracking-widest font-medium">Book
                    Appointment</a>
                <a href="{{ route('events.index') }}"
                    class="text-app-blue text-sm transition hidden md:block uppercase tracking-widest font-medium">Events</a>
                <a href="{{ route('home') }}#blog"
                    class="text-parchment/70 hover:text-app-blue text-sm transition hidden md:block uppercase tracking-widest font-medium">Blog</a>
                <a href="{{ route('home') }}#download"
                    class="px-8 py-3.5 bg-app-blue text-white text-xs font-bold rounded-full hover:bg-app-blue/90 transition shadow-lg shadow-app-blue/10 uppercase tracking-tighter">Get
                    Watered App</a>
            </div>
        </div>
    </nav>

    {{-- Content --}}
    <main class="pt-32 pb-64 flex-grow">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-20">
                <p class="text-app-blue/90 mb-4 text-sm uppercase tracking-[0.3em] font-bold">Community Gatherings</p>
                <h1 class="font-heading text-6xl md:text-8xl text-parchment mb-6 tracking-tighter uppercase">Events</h1>
                <p class="text-parchment/60 text-lg max-w-2xl mx-auto leading-relaxed">Discover spiritual gatherings,
                    rituals, and educational events hosted by the Watered community.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                @forelse($events as $event)
                    <div
                        class="group bg-parchment/5 border border-parchment/10 overflow-hidden hover:border-app-blue/50 transition-all duration-500 rounded-3xl mb-12">
                        <div class="aspect-16/10 overflow-hidden">
                            <img src="{{ $event->banner_image_url ?? asset('images/watered-logo.png') }}"
                                alt="{{ $event->title }}"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        </div>
                        <div class="p-8">
                            <div
                                class="flex items-center gap-3 text-app-blue text-[10px] font-bold uppercase tracking-widest mb-4">
                                <span>{{ $event->event_date?->format('M d, Y') ?? 'TBA' }}</span>
                                <span class="w-1 h-1 bg-parchment/20 rounded-full"></span>
                                <span>{{ $event->location ?? 'Online' }}</span>
                            </div>
                            <h3 class="font-heading text-2xl mb-4 group-hover:text-app-blue transition-colors">
                                {{ $event->title }}
                            </h3>
                            <p class="text-parchment/40 text-sm line-clamp-2 mb-8 leading-relaxed">
                                {{ Str::limit($event->description, 100) }}
                            </p>

                            <div class="flex items-center justify-between pt-6 border-t border-parchment/5">
                                <span class="text-sm font-bold {{ $event->is_paid ? 'text-parchment' : 'text-green-400' }}">
                                    @if($event->is_paid)
                                        @if(($settings->currency_position ?? 'before') === 'before')
                                            {{ $settings->currency_symbol ?? '$' }}{{ number_format($event->price, 2) }}
                                        @else
                                            {{ number_format($event->price, 2) }}{{ $settings->currency_symbol ?? '$' }}
                                        @endif
                                    @else
                                        FREE
                                    @endif
                                </span>
                                <a href="{{ route('events.show', $event) }}"
                                    class="text-app-blue text-xs font-bold uppercase tracking-widest hover:text-parchment transition-colors flex items-center gap-2">
                                    Details <span class="text-lg">&rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-32 text-center bg-parchment/5 rounded-[3rem] border border-parchment/10">
                        <p class="text-parchment/30 uppercase tracking-[0.2em] font-medium">No events scheduled at the
                            moment.</p>
                    </div>
                @endforelse
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