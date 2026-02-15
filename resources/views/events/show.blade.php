<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->title }} - Watered</title>
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
                <a href="{{ route('events.index') }}" class="text-sm font-medium hover:text-app-blue transition-colors">Events</a>
            </div>
            <a href="{{ route('home') }}#download" class="px-6 py-2 bg-app-blue text-sea-deep font-heading text-sm tracking-widest hover:bg-white transition-all transform hover:-translate-y-1">GET THE APP</a>
        </div>
    </nav>

    <main class="pt-32 pb-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                <!-- Left: Event Info -->
                <div>
                    <img src="{{ $event->banner_image_url ?? asset('images/watered-logo.png') }}" alt="{{ $event->title }}" class="w-full aspect-video object-cover border border-white/10 mb-8">
                    
                    <div class="flex items-center gap-4 text-app-blue text-sm font-bold uppercase tracking-widest mb-6">
                        <span>{{ $event->event_date?->format('l, F j, Y') ?? 'TBA' }}</span>
                        <span class="w-1 h-1 bg-white/20 rounded-full"></span>
                        <span>{{ $event->event_time ? date('g:i A', strtotime($event->event_time)) : 'TBA' }}</span>
                    </div>

                    <h1 class="font-heading text-5xl lg:text-7xl text-white mb-8 tracking-tighter">{{ $event->title }}</h1>
                    
                    <div class="prose prose-invert max-w-none text-white/60 leading-relaxed mb-12">
                        {!! nl2br(e($event->description)) !!}
                    </div>

                    <div class="grid grid-cols-2 gap-8 border-t border-white/10 pt-8">
                        <div>
                            <span class="block text-white/30 text-[10px] font-bold uppercase tracking-widest mb-2">Location</span>
                            <p class="text-white font-medium">{{ $event->location ?? 'Online / To be announced' }}</p>
                        </div>
                        <div>
                            <span class="block text-white/30 text-[10px] font-bold uppercase tracking-widest mb-2">Registration</span>
                            <p class="text-app-blue font-heading text-xl">{{ $event->is_paid ? '$' . number_format($event->price, 2) : 'FREE' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Right: Registration Form -->
                <div class="lg:sticky lg:top-32 h-fit">
                    <div class="bg-white/5 border border-white/10 p-8 lg:p-12">
                        <h2 class="font-heading text-3xl text-app-blue mb-8">REGISTER NOW</h2>
                        
                        @if(session('success'))
                            <div class="bg-green-500/20 border border-green-500/50 text-green-400 p-4 mb-8 text-sm">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="bg-red-500/20 border border-red-500/50 text-red-400 p-4 mb-8 text-sm">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form action="{{ route('events.register', $event->slug) }}" method="POST" class="space-y-6">
                            @csrf
                            <div>
                                <label class="block text-white/40 text-[10px] font-bold uppercase tracking-widest mb-2">Full Name</label>
                                <input type="text" name="full_name" value="{{ auth()->user()?->name }}" required class="w-full bg-white/5 border border-white/10 px-4 py-3 text-white focus:outline-none focus:border-app-blue transition-colors">
                            </div>
                            <div>
                                <label class="block text-white/40 text-[10px] font-bold uppercase tracking-widest mb-2">Email Address</label>
                                <input type="email" name="email" value="{{ auth()->user()?->email }}" required class="w-full bg-white/5 border border-white/10 px-4 py-3 text-white focus:outline-none focus:border-app-blue transition-colors">
                            </div>
                            <div>
                                <label class="block text-white/40 text-[10px] font-bold uppercase tracking-widest mb-2">Phone Number</label>
                                <input type="tel" name="phone" value="{{ auth()->user()?->phone }}" required class="w-full bg-white/5 border border-white/10 px-4 py-3 text-white focus:outline-none focus:border-app-blue transition-colors">
                            </div>

                            <button type="submit" class="w-full py-4 bg-app-blue text-sea-deep font-heading tracking-[0.2em] hover:bg-white transition-all transform hover:-translate-y-1">
                                {{ $event->is_paid ? 'PAY & REGISTER' : 'CONFIRM REGISTRATION' }}
                            </button>
                            
                            <p class="text-[10px] text-white/20 text-center uppercase tracking-widest">
                                @if($event->is_paid)
                                    Secure checkout via Paystack
                                @else
                                    Free events are open to all community members
                                @endif
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="py-12 border-t border-white/10 text-center">
        <p class="text-white/20 text-xs tracking-widest uppercase">&copy; {{ date('Y') }} Watered. All rights reserved.</p>
    </footer>
</body>
</html>
