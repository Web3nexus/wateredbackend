<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - Watered</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-sea-deep text-white font-sans selection:bg-app-blue selection:text-sea-deep">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-sea-deep/80 backdrop-blur-md border-b border-white/10">
        <div class="max-w-7xl mx-auto px-6 h-18 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="font-heading text-xl text-app-blue tracking-wider uppercase">Watered</span>
            </a>
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ route('home') }}" class="text-sm font-medium hover:text-app-blue transition-colors">Home</a>
                <a href="{{ route('events.index') }}" class="text-sm font-medium text-app-blue transition-colors">Events</a>
            </div>
            <a href="{{ route('home') }}#download" class="px-6 py-2 bg-app-blue text-sea-deep font-heading text-sm tracking-widest hover:bg-white transition-all transform hover:-translate-y-1">GET THE APP</a>
        </div>
    </nav>

    <!-- Content -->
    <main class="pt-32 pb-20">
        <div class="max-w-7xl mx-auto px-6">
            <h1 class="font-heading text-5xl text-app-blue mb-4 tracking-tighter">UPCOMING EVENTS</h1>
            <p class="text-white/60 text-lg max-w-2xl mb-12">Discover spiritual gatherings, rituals, and educational events hosted by the Watered community.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($events as $event)
                    <div class="group bg-white/5 border border-white/10 overflow-hidden hover:border-app-blue/50 transition-all">
                        <div class="aspect-video overflow-hidden">
                            <img src="{{ $event->banner_image_url ?? asset('images/watered-logo.png') }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                        <div class="p-6">
                            <div class="flex items-center gap-3 text-app-blue text-xs font-bold uppercase tracking-widest mb-3">
                                <span>{{ $event->event_date?->format('M d, Y') ?? 'TBA' }}</span>
                                <span class="w-1 h-1 bg-white/20 rounded-full"></span>
                                <span>{{ $event->location ?? 'Online' }}</span>
                            </div>
                            <h3 class="font-heading text-xl mb-3 group-hover:text-app-blue transition-colors">{{ $event->title }}</h3>
                            <p class="text-white/40 text-sm line-clamp-2 mb-6">{{ Str::limit($event->description, 100) }}</p>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold {{ $event->is_paid ? 'text-white' : 'text-green-400' }}">
                                    {{ $event->is_paid ? '$' . number_format($event->price, 2) : 'FREE' }}
                                </span>
                                <a href="{{ route('events.show', $event->slug) }}" class="text-app-blue text-xs font-bold uppercase tracking-widest hover:text-white transition-colors">Details →</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center">
                        <p class="text-white/40">No upcoming events scheduled at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </main>

    <footer class="py-12 border-t border-white/10 text-center">
        <p class="text-white/20 text-xs tracking-widest uppercase">&copy; {{ date('Y') }} Watered. All rights reserved.</p>
    </footer>
</body>
</html>
