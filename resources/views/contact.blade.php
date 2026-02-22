@extends('layouts.main')

@section('title', 'Contact Us - ' . ($settings?->site_name ?? 'Watered'))

@section('content')
    <div class="py-24 max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-20 items-center">
            <div class="space-y-10">
                <div>
                    <p class="text-app-blue/90 mb-4 text-sm uppercase tracking-[0.3em] font-bold">Reach Out to Us</p>
                    <h1 class="text-5xl md:text-6xl text-parchment font-heading leading-tight italic">Contact Us</h1>
                    <p class="text-xl text-parchment/60 mt-6 leading-relaxed">
                        Have questions about the Watered path? Need assistance with the app? Our scribes and spiritual
                        advisors are here to help.
                    </p>
                </div>

                <div class="space-y-6">
                    <div class="flex items-center gap-6">
                        <div
                            class="w-12 h-12 rounded-full bg-app-blue/10 flex items-center justify-center text-app-blue shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-parchment">Email Us</h4>
                            <p class="text-parchment/50">support@mywatered.com</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-6">
                        <div
                            class="w-12 h-12 rounded-full bg-app-blue/10 flex items-center justify-center text-app-blue shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-parchment">Temple Location</h4>
                            <p class="text-parchment/50">Lagos, Nigeria</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-sea-deep/80 backdrop-blur-xl border border-parchment/10 rounded-[3rem] p-10 md:p-14 shadow-3xl">
                <form action="#" method="POST" class="space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-parchment/40 uppercase tracking-widest">Name</label>
                            <input type="text" name="name" required
                                class="w-full px-5 py-4 bg-parchment/5 border border-parchment/10 rounded-xl focus:border-app-blue outline-none transition text-parchment">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-parchment/40 uppercase tracking-widest">Email</label>
                            <input type="email" name="email" required
                                class="w-full px-5 py-4 bg-parchment/5 border border-parchment/10 rounded-xl focus:border-app-blue outline-none transition text-parchment">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-bold text-parchment/40 uppercase tracking-widest">Subject</label>
                        <input type="text" name="subject" required
                            class="w-full px-5 py-4 bg-parchment/5 border border-parchment/10 rounded-xl focus:border-app-blue outline-none transition text-parchment">
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-bold text-parchment/40 uppercase tracking-widest">Message</label>
                        <textarea name="message" rows="5" required
                            class="w-full px-5 py-4 bg-parchment/5 border border-parchment/10 rounded-xl focus:border-app-blue outline-none transition text-parchment"
                            placeholder="How can we help you on your journey?"></textarea>
                    </div>

                    <button type="submit"
                        class="w-full py-5 bg-app-blue text-white font-bold rounded-xl hover:bg-app-blue/90 transition shadow-xl shadow-app-blue/20 uppercase tracking-widest">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection