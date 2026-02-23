@extends('layouts.main')

@section('title', 'Teachings - ' . ($settings?->site_name ?? 'Watered'))

@section('content')
    <section class="py-32 bg-parchment/[0.02]">
        <div class="max-w-7xl mx-auto px-6">
            <div class="max-w-3xl mb-16">
                <h1 class="text-5xl md:text-7xl text-parchment font-heading mb-6">Lords Uzih's Teachings</h1>
                <p class="text-xl text-parchment/60 leading-relaxed">Discover deep spiritual insights, ancient wisdom, and
                    modern explorations from our community leaders.</p>
            </div>

            @if($teachings->count() > 0)
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-12">
                    @foreach($teachings as $post)
                        <article class="group space-y-6">
                            <a href="{{ route('teachings.show', $post->slug) }}" class="block">
                                <div class="relative aspect-video rounded-3xl overflow-hidden border border-parchment/10 shadow-lg">
                                    @if($post->featured_image)
                                        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    @else
                                        <div class="w-full h-full bg-parchment/10 flex items-center justify-center">
                                            <svg class="w-16 h-16 text-app-blue/30" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM3 8h10M9 12h5M9 16h5" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </a>
                            <div class="space-y-3">
                                <p class="text-xs text-app-blue font-bold uppercase tracking-widest">
                                    {{ $post->published_at?->format('F d, Y') ?? $post->created_at->format('F d, Y') }}
                                </p>
                                <a href="{{ route('teachings.show', $post->slug) }}" class="block group">
                                    <h3 class="text-2xl text-parchment font-heading group-hover:text-app-blue transition">
                                        {{ $post->title }}
                                    </h3>
                                </a>
                                <p class="text-parchment/60 text-sm line-clamp-3 leading-relaxed">{{ $post->summary }}</p>
                            </div>
                            <a href="{{ route('teachings.show', $post->slug) }}"
                                class="inline-block pt-2 text-app-blue font-semibold border-b border-transparent hover:border-app-blue transition">Explore
                                Teaching &rarr;</a>
                        </article>
                    @endforeach
                </div>

                <div class="mt-20">
                    {{ $teachings->links() }}
                </div>
            @else
                <div class="text-center py-24 bg-parchment/5 rounded-3xl border border-dashed border-parchment/10">
                    <p class="text-parchment/40 text-lg">Our scribes are currently crafting new teachings. Please check back
                        soon.</p>
                </div>
            @endif
        </div>
    </section>
@endsection