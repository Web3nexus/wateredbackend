@extends('layouts.main')

@section('title', $teaching->title . ' - ' . ($settings?->site_name ?? 'Watered'))

@section('content')
    <article class="py-32 bg-parchment/[0.02]">
        <div class="max-w-4xl mx-auto px-6">
            {{-- Header --}}
            <header class="mb-16 text-center">
                <p class="text-app-blue font-bold uppercase tracking-[0.2em] text-xs mb-6">
                    {{ $teaching->published_at?->format('F d, Y') ?? $teaching->created_at->format('F d, Y') }}
                </p>
                <h1 class="text-4xl md:text-6xl text-parchment font-heading mb-8 leading-tight">
                    {{ $teaching->title }}
                </h1>
                <div class="w-24 h-px bg-app-blue/30 mx-auto mb-10"></div>
                @if($teaching->summary)
                    <p class="text-xl text-parchment/60 leading-relaxed max-w-2xl mx-auto italic">
                        "{{ $teaching->summary }}"
                    </p>
                @endif
            </header>

            {{-- Featured Image --}}
            @if($teaching->featured_image)
                <div class="mb-20 rounded-[2.5rem] overflow-hidden border border-parchment/10 shadow-2xl relative group">
                    <img src="{{ asset('storage/' . $teaching->featured_image) }}" alt="{{ $teaching->title }}"
                        class="w-full aspect-[16/9] object-cover transition duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-sea-deep/40 to-transparent opacity-60"></div>
                </div>
            @endif

            {{-- Content --}}
            <div
                class="prose prose-invert prose-lg max-w-none prose-headings:font-heading prose-headings:text-parchment prose-p:text-parchment/70 prose-p:leading-relaxed prose-a:text-app-blue hover:prose-a:underline prose-strong:text-parchment prose-blockquote:border-app-blue prose-blockquote:bg-parchment/[0.03] prose-blockquote:py-2 prose-blockquote:px-8 prose-blockquote:rounded-r-2xl prose-img:rounded-3xl prose-img:border prose-img:border-parchment/10">
                {!! $teaching->content !!}
            </div>

            {{-- Share / Footer --}}
            <div
                class="mt-20 pt-10 border-t border-parchment/10 flex flex-col md:flex-row justify-between items-center gap-8">
                <a href="{{ route('teachings.index') }}"
                    class="flex items-center gap-3 text-parchment/40 hover:text-app-blue transition font-semibold group">
                    <span class="group-hover:-translate-x-1 transition">&larr;</span> Back to All Teachings
                </a>
                <div class="flex items-center gap-6">
                    <span class="text-parchment/30 text-xs uppercase tracking-widest font-bold">Share Wisdom</span>
                    <div class="flex gap-4">
                        {{-- Social Share Placeholder --}}
                        <button class="p-2 text-parchment/40 hover:text-app-blue transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                            </svg>
                        </button>
                        <button class="p-2 text-parchment/40 hover:text-app-blue transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.791-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.209-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </button>
                        <button class="p-2 text-parchment/40 hover:text-app-blue transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </article>

    {{-- Related Teachings --}}
    @if($related->count() > 0)
        <section class="py-32 bg-sea-deep border-t border-parchment/10">
            <div class="max-w-7xl mx-auto px-6">
                <h2 class="text-3xl text-parchment font-heading mb-12">More Divine Wisdom</h2>
                <div class="grid md:grid-cols-3 gap-10">
                    @foreach($related as $post)
                        <article class="group space-y-4">
                            <a href="{{ route('teachings.show', $post->slug) }}" class="block">
                                <div class="relative aspect-video rounded-2xl overflow-hidden border border-parchment/10">
                                    @if($post->featured_image)
                                        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    @endif
                                </div>
                            </a>
                            <p class="text-[10px] text-app-blue font-bold uppercase tracking-widest">
                                {{ $post->published_at?->format('M d, Y') }}
                            </p>
                            <a href="{{ route('teachings.show', $post->slug) }}" class="block">
                                <h3 class="text-lg text-parchment font-heading group-hover:text-app-blue transition">
                                    {{ $post->title }}
                                </h3>
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection