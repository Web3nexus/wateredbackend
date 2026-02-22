@extends('layouts.main')

@section('title', $event->title . ' - ' . ($settings?->site_name ?? 'Watered'))

@section('content')
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
                                            {{ $settings->currency_symbol ?? '₦' }}{{ number_format($event->price, 2) }}
                                        @else
                                            {{ number_format($event->price, 2) }}{{ $settings->currency_symbol ?? '₦' }}
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
@endsection
