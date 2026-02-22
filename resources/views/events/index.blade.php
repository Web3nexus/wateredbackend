@extends('layouts.main')

@section('title', 'Events - ' . ($settings?->site_name ?? 'Watered'))

@section('content')
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
                                            {{ $settings->currency_symbol ?? '₦' }}{{ number_format($event->price, 2) }}
                                        @else
                                            {{ number_format($event->price, 2) }}{{ $settings->currency_symbol ?? '₦' }}
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
@endsection