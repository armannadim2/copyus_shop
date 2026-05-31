@extends('layouts.app')
@section('title', __('app.home_title'))

@section('content')

    {{-- ── Hero ─────────────────────────────────────────────────────────────── --}}
    <section class="relative bg-primary text-white overflow-hidden -mt-16 lg:-mt-20 pt-16 lg:pt-20">

        {{-- Decorative blobs --}}
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute -top-32 -right-40 w-[600px] h-[600px] rounded-full
                        bg-white/10 blur-3xl"></div>
            <div class="absolute -bottom-20 -left-32 w-[420px] h-[420px] rounded-full
                        bg-dark/10 blur-3xl"></div>
        </div>

        <div class="section relative py-20 md:py-28">
            <div class="grid lg:grid-cols-2 gap-12 items-center">

                {{-- Left: copy --}}
                <div>
                    <h1 class="font-alumni text-h1 text-white animate-reveal-up"
                        style="line-height: 1.05; letter-spacing: 0.01em;">
                        {{ __('app.home_hero_title_1') }}<br>
                        <em class="italic">{{ __('app.home_hero_title_2') }}</em>
                    </h1>

                    <p class="font-outfit font-light text-body-lg text-white/90 mt-6 max-w-xl
                              animate-reveal-up delay-100">
                        {{ __('app.home_hero_subtitle') }}
                    </p>

                    <div class="flex flex-wrap gap-3 mt-10 animate-reveal-up delay-200">
                        <a href="{{ route('print.index') }}"
                           class="inline-flex items-center justify-center
                                  bg-dark text-white font-outfit text-body-lg
                                  px-7 py-3.5 rounded-xl hover:brightness-110
                                  active:scale-95 transition-all duration-200">
                            {{ __('app.home_hero_btn_print') }}
                        </a>
                        <a href="{{ route('products.index') }}"
                           class="inline-flex items-center justify-center
                                  bg-dark text-primary font-outfit text-body-lg
                                  px-7 py-3.5 rounded-xl hover:brightness-110
                                  active:scale-95 transition-all duration-200">
                            {{ __('app.home_hero_btn_products') }}
                        </a>
                    </div>
                </div>

                {{-- Right: hero slider --}}
                <div class="relative animate-reveal-up delay-300">
                    @if($heroSlides->isNotEmpty())
                        <div
                            x-data="{
                                current: 0,
                                count: {{ $heroSlides->count() }},
                                _t: null,
                                init() {
                                    if (this.count > 1) this._start();
                                },
                                _start() {
                                    clearInterval(this._t);
                                    this._t = setInterval(() => this.next(), 5000);
                                },
                                next() { this.current = (this.current + 1) % this.count; },
                                prev() { this.current = (this.current - 1 + this.count) % this.count; },
                                goTo(i) { this.current = i; this._start(); }
                            }"
                            class="relative rounded-3xl overflow-hidden h-80 md:h-[28rem]
                                   shadow-[0_20px_50px_rgba(0,0,0,0.25)]"
                        >
                            {{-- Slides — all in DOM, cross-fade via CSS opacity --}}
                            @foreach($heroSlides as $i => $slide)
                            <div
                                class="absolute inset-0 flex items-end p-8"
                                style="transition: opacity 0.8s ease-in-out; {{ $i === 0 ? 'opacity:1;z-index:10' : 'opacity:0;z-index:0' }}"
                                :style="current === {{ $i }}
                                    ? 'opacity:1;z-index:10;transition:opacity 0.8s ease-in-out'
                                    : 'opacity:0;z-index:0;transition:opacity 0.8s ease-in-out'"
                            >
                                <img src="{{ $slide->imageUrl() }}"
                                     alt="{{ $slide->eyebrow ?? $slide->title ?? '' }}"
                                     class="absolute inset-0 w-full h-full object-cover">
                                <div class="absolute inset-x-0 bottom-0 h-2/5
                                            bg-gradient-to-t from-black/70 to-transparent"></div>
                                @if($slide->eyebrow || $slide->title)
                                <div class="relative z-10">
                                    @if($slide->eyebrow)
                                    <p class="font-outfit font-bold text-body-md text-white/90
                                              uppercase tracking-wider mb-1">
                                        {{ $slide->eyebrow }}
                                    </p>
                                    @endif
                                    @if($slide->title)
                                    <h3 class="font-alumni text-h4 text-white">
                                        {{ $slide->title }}
                                    </h3>
                                    @endif
                                </div>
                                @endif
                            </div>
                            @endforeach

                            @if($heroSlides->count() > 1)
                            {{-- Prev arrow — z-30 to always sit above slides --}}
                            <button @click="prev()"
                                    class="absolute left-4 top-1/2 -translate-y-1/2 z-30
                                           w-10 h-10 flex items-center justify-center
                                           rounded-full bg-black/40 hover:bg-black/65
                                           text-white transition-colors duration-200
                                           focus:outline-none focus:ring-2 focus:ring-white/50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            {{-- Next arrow --}}
                            <button @click="next()"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 z-30
                                           w-10 h-10 flex items-center justify-center
                                           rounded-full bg-black/40 hover:bg-black/65
                                           text-white transition-colors duration-200
                                           focus:outline-none focus:ring-2 focus:ring-white/50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>

                            {{-- Dot indicators — centred at bottom, z-30 --}}
                            <div class="absolute bottom-5 left-1/2 -translate-x-1/2 flex gap-2.5 z-30">
                                @foreach($heroSlides as $i => $slide)
                                <button
                                    @click="goTo({{ $i }})"
                                    :class="current === {{ $i }}
                                        ? 'bg-white w-6 h-2.5'
                                        : 'bg-white/50 hover:bg-white/80 w-2.5 h-2.5'"
                                    class="rounded-full transition-all duration-400 focus:outline-none"
                                    aria-label="Slide {{ $i + 1 }}"
                                ></button>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    @else
                        {{-- Fallback: static image when no slides configured --}}
                        <div class="relative rounded-3xl overflow-hidden h-80 md:h-[28rem]
                                    flex items-end p-8 shadow-[0_20px_50px_rgba(0,0,0,0.25)]">
                            <img src="{{ asset('images/hero_1.png') }}"
                                 alt="{{ __('app.home_hero_card_eyebrow') }}"
                                 class="absolute inset-0 w-full h-full object-cover">
                            <div class="absolute inset-x-0 bottom-0 h-2/5
                                        bg-gradient-to-t from-black/70 to-transparent"></div>
                            <div class="relative">
                                <p class="font-outfit font-bold text-body-md text-white/90
                                          uppercase tracking-wider mb-1">
                                    {{ __('app.home_hero_card_eyebrow') }}
                                </p>
                                <h3 class="font-alumni text-h4 text-white">
                                    {{ __('app.home_hero_card_title') }}
                                </h3>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ── Sobre Nosaltres ──────────────────────────────────────────────────── --}}
    <section id="qui-som" class="relative">

        {{-- Coral top divider --}}
        <div class="h-3 bg-primary"></div>

        <div class="relative bg-dark text-white py-24 md:py-32 overflow-hidden">

            {{-- Background image + dark overlay --}}
            <img src="{{ asset('images/about_us_header.png') }}"
                 alt="" aria-hidden="true"
                 class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-dark/70"></div>

            <div class="section relative text-center max-w-3xl mx-auto">
                <h2 class="font-alumni italic text-h2">
                    {{ __('app.home_about_title') }}
                </h2>
                <p class="font-outfit text-body-lg text-white/80 mt-6 leading-relaxed max-w-2xl mx-auto">
                    {{ __('app.home_about_body') }}
                </p>
            </div>
        </div>
    </section>

    {{-- ── Compromesos amb l'Excel·lència ──────────────────────────────────── --}}
    <section class="bg-light py-24">
        <div class="section grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

            <div>
                <h2 class="font-alumni text-h2 text-dark mb-10">
                    {{ __('app.home_excellence_title_1') }}<br>{{ __('app.home_excellence_title_2') }}
                </h2>

                <div class="space-y-8">
                    <div class="border-l-2 border-dark/40 pl-6">
                        <p class="font-alumni italic text-sm-header text-dark mb-2">
                            {{ __('app.home_mission_label') }}
                        </p>
                        <p class="font-outfit text-body-lg text-primary leading-relaxed">
                            {{ __('app.home_mission_body') }}
                        </p>
                    </div>

                    <div class="border-l-2 border-dark/40 pl-6">
                        <p class="font-alumni italic text-sm-header text-dark mb-2">
                            {{ __('app.home_vision_label') }}
                        </p>
                        <p class="font-outfit text-body-lg text-primary leading-relaxed">
                            {{ __('app.home_vision_body') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="bg-white rounded-3xl p-3 shadow-[0_20px_60px_rgba(36,48,46,0.08)]
                            relative overflow-hidden">
                    <img src="{{ asset('images/about_us.png') }}"
                         alt="Copyus"
                         class="rounded-2xl w-full h-80 md:h-96 object-cover">
                </div>

                {{-- Floating badge --}}
                <div class="absolute -bottom-5 -right-5 bg-white rounded-2xl
                            shadow-[0_10px_30px_rgba(36,48,46,0.15)] px-6 py-4 border border-gray-100">
                    <p class="font-alumni font-extrabold text-h3 text-secondary leading-none text-center">15+</p>
                    <p class="font-outfit text-body-sm font-bold text-primary mt-1.5 text-center
                              uppercase tracking-wider">{{ __('app.home_years_active') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Els nostres Valors ──────────────────────────────────────────────── --}}
    <section class="bg-light py-24">
        <div class="section">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <h2 class="font-alumni text-h2 text-dark">
                    {{ __('app.home_values_title') }}
                </h2>
                <p class="font-outfit text-body-lg text-gray-500 mt-3">
                    {{ __('app.home_values_subtitle') }}
                </p>
            </div>

            @php
                $values = [
                    ['icon' => '⭐', 'title' => __('app.home_value_1_title'), 'desc' => __('app.home_value_1_desc')],
                    ['icon' => '💡', 'title' => __('app.home_value_2_title'), 'desc' => __('app.home_value_2_desc')],
                    ['icon' => '🌱', 'title' => __('app.home_value_3_title'), 'desc' => __('app.home_value_3_desc')],
                    ['icon' => '🤝', 'title' => __('app.home_value_4_title'), 'desc' => __('app.home_value_4_desc')],
                    ['icon' => '⚡', 'title' => __('app.home_value_5_title'), 'desc' => __('app.home_value_5_desc')],
                    ['icon' => '🏆', 'title' => __('app.home_value_6_title'), 'desc' => __('app.home_value_6_desc')],
                ];
            @endphp

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($values as $value)
                    <div class="bg-white rounded-3xl p-7 border border-gray-100
                                hover:border-primary/40 hover:shadow-[0_10px_30px_rgba(242,96,82,0.10)]
                                transition-all duration-300">
                        <div class="w-14 h-14 rounded-2xl bg-light flex items-center
                                    justify-center text-3xl mb-5">
                            {{ $value['icon'] }}
                        </div>
                        <h3 class="font-alumni text-h5 text-dark mb-2">
                            {{ $value['title'] }}
                        </h3>
                        <p class="font-outfit text-body-md text-gray-500 leading-relaxed">
                            {{ $value['desc'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── Serveis ─────────────────────────────────────────────────────────── --}}
    <section id="serveis" class="bg-light py-24">
        <div class="section">
            <div class="text-center max-w-2xl mx-auto mb-4">
                <h2 class="font-alumni text-h2 text-dark">
                    {{ __('app.home_services_title') }}
                </h2>
                <p class="font-outfit text-body-lg text-gray-500 mt-3">
                    {{ __('app.home_services_subtitle') }}
                </p>
            </div>

            <div class="text-left max-w-3xl mx-auto mt-12 mb-10">
                <h3 class="font-alumni text-h3 text-secondary">
                    {{ __('app.home_services_subhead_1') }}<em class="italic">{{ __('app.home_services_subhead_em') }}</em>
                </h3>
                <p class="font-outfit text-body-md text-gray-500 mt-3">
                    {{ __('app.home_services_intro') }}
                </p>
                <div class="mt-4 h-0.5 bg-gradient-to-r from-secondary via-primary to-transparent w-32"></div>
            </div>

            @php
                $homeServices = [
                    [
                        'title' => __('app.home_service_corp_title'),
                        'desc'  => __('app.home_service_corp_desc'),
                        'image' => 'images/corporate_business.png',
                        'href'  => 'request-quote',
                    ],
                    [
                        'title' => __('app.home_service_largeformat_title'),
                        'desc'  => __('app.home_service_largeformat_desc'),
                        'image' => 'images/gran_format.png',
                        'href'  => 'request-quote',
                    ],
                    [
                        'title' => __('app.home_service_merch_title'),
                        'desc'  => __('app.home_service_merch_desc'),
                        'image' => 'images/merchandising.png',
                        'href'  => 'request-quote',
                    ],
                    [
                        'title' => __('app.home_service_editorial_title'),
                        'desc'  => __('app.home_service_editorial_desc'),
                        'image' => 'images/editorial.png',
                        'href'  => 'request-quote',
                    ],
                ];
            @endphp

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($homeServices as $svc)
                    <div class="group bg-white rounded-3xl p-3 border border-gray-100
                                hover:border-primary/40 hover:-translate-y-1
                                hover:shadow-[0_12px_30px_rgba(36,48,46,0.08)]
                                transition-all duration-300 flex flex-col">
                        <div class="relative rounded-2xl h-48 overflow-hidden mb-4">
                            <img src="{{ asset($svc['image']) }}"
                                 alt="{{ $svc['title'] }}"
                                 class="absolute inset-0 w-full h-full object-cover
                                        group-hover:scale-105 transition-transform duration-500">
                        </div>
                        <div class="px-3 pb-4 flex-1 flex flex-col">
                            <h3 class="font-alumni text-h6 text-dark mb-1.5">
                                {{ $svc['title'] }}
                            </h3>
                            <p class="font-outfit text-body-md text-gray-500 leading-relaxed mb-4 flex-1">
                                {{ $svc['desc'] }}
                            </p>
                            <a href="{{ route($svc['href']) }}"
                               class="inline-flex items-center gap-1.5 font-outfit text-body-md
                                      font-semibold text-primary group-hover:gap-2.5 transition-all">
                                {{ __('app.home_services_learn_more') }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── Confiat per Marques Líders ──────────────────────────────────────── --}}
    <section class="bg-white py-16 border-y border-gray-100 overflow-hidden">
        <div class="section mb-10">
            <p class="text-center font-outfit text-xs font-semibold text-gray-400
                      uppercase tracking-[0.3em]">
                {{ __('app.home_brands_title') }}
            </p>
        </div>

        @php
            $brands = [
                ['name' => "El moli d'en Puigvert", 'image' => 'images/clients-logo/logo-moli.png'],
                ['name' => 'EDMAFOOD',              'image' => 'images/clients-logo/edmafood-logo.png'],
                ['name' => 'FVNDARES',              'image' => 'images/clients-logo/fundares-logo.png'],
                ['name' => 'CPL Aromas',            'image' => 'images/clients-logo/cpl-logo.png'],
                ['name' => 'Imagina Sounds',        'image' => 'images/clients-logo/imagina_logo.png'],
                ['name' => 'TecnoCampus',           'image' => 'images/clients-logo/tecnocampus.png'],
                ['name' => 'SolarTradex',           'image' => 'images/clients-logo/solartradex.svg'],
            ];
        @endphp

        <div class="relative w-full overflow-hidden">

            {{-- Edge fade masks --}}
            <div class="absolute left-0 top-0 bottom-0 w-24 z-10 pointer-events-none
                        bg-gradient-to-r from-white to-transparent"></div>
            <div class="absolute right-0 top-0 bottom-0 w-24 z-10 pointer-events-none
                        bg-gradient-to-l from-white to-transparent"></div>

            <div class="flex w-max items-center marquee-track">
                @foreach(array_merge($brands, $brands) as $brand)
                    <div class="flex items-center justify-center px-10 lg:px-14 shrink-0">
                        <img src="{{ asset($brand['image']) }}"
                             alt="{{ $brand['name'] }}"
                             loading="lazy"
                             class="h-10 md:h-12 w-auto max-w-[180px] object-contain
                                    grayscale opacity-60 hover:grayscale-0 hover:opacity-100
                                    transition duration-300">
                    </div>
                @endforeach
            </div>
        </div>

        @push('styles')
        <style>
            @keyframes marquee-scroll {
                0%   { transform: translateX(0); }
                100% { transform: translateX(-50%); }
            }
            .marquee-track {
                animation: marquee-scroll 40s linear infinite;
            }
            .marquee-track:hover {
                animation-play-state: paused;
            }
            @media (prefers-reduced-motion: reduce) {
                .marquee-track { animation: none; }
            }
        </style>
        @endpush
    </section>

    {{-- ── Mantén-te al Corrent ────────────────────────────────────────────── --}}
    <section class="relative bg-dark py-24 md:py-28 overflow-hidden">

        {{-- Subtle background glow --}}
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px]
                        rounded-full bg-secondary/10 blur-3xl"></div>
        </div>

        <div class="section relative text-center max-w-2xl mx-auto">

            {{-- Pill badge --}}
            <span class="inline-flex items-center gap-2 bg-secondary/15
                         font-outfit text-xs font-semibold tracking-[0.25em] uppercase
                         text-secondary px-4 py-1.5 rounded-full mb-7
                         border border-secondary/25">
                <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                {{ __('app.home_newsletter_badge') }}
            </span>

            <h2 class="font-alumni italic text-h2 text-white">
                {{ __('app.home_newsletter_title_1') }} <span class="text-secondary">{{ __('app.home_newsletter_title_em') }}</span>
            </h2>

            <p class="font-outfit text-body-lg text-white/65 mt-5 max-w-xl mx-auto leading-relaxed">
                {{ __('app.home_newsletter_subtitle') }}
            </p>

            <form
                x-data="{
                    email: '',
                    status: null,
                    loading: false,
                    async submit() {
                        if (!this.email || this.loading) return;
                        this.loading = true;
                        this.status  = null;
                        try {
                            const res  = await fetch('{{ route('newsletter.subscribe') }}', {
                                method:  'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept':        'application/json',
                                },
                                body: JSON.stringify({ email: this.email }),
                            });
                            const data = await res.json();
                            this.status = data.status ?? 'error';
                            if (this.status === 'success') this.email = '';
                        } catch (e) {
                            this.status = 'error';
                        }
                        this.loading = false;
                    }
                }"
                @submit.prevent="submit()"
                class="mt-10 max-w-2xl mx-auto">

                <div class="flex flex-col sm:flex-row gap-3">

                    {{-- Email input — pill, dark, with trailing icon --}}
                    <div class="relative flex-1">
                        <input type="email" x-model="email" required
                               :disabled="status === 'success' || loading"
                               placeholder="{{ __('app.home_newsletter_placeholder') }}"
                               class="w-full bg-white/[0.04] border border-primary/30
                                      rounded-full pl-7 pr-12 py-4 font-outfit text-sm
                                      text-white placeholder:text-white/40
                                      focus:outline-none focus:ring-2 focus:ring-secondary/50
                                      focus:border-secondary/60 focus:bg-white/[0.06]
                                      transition disabled:opacity-50">
                        <svg class="absolute right-5 top-1/2 -translate-y-1/2 w-5 h-5 text-white/40 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>

                    {{-- Subscribe button — pill, secondary blue --}}
                    <button type="submit"
                            :disabled="status === 'success' || loading"
                            class="bg-secondary text-white font-alumni font-bold italic text-sm-header
                                   px-9 py-4 rounded-full hover:brightness-110
                                   active:scale-95 transition-all whitespace-nowrap
                                   disabled:opacity-60">
                        <span x-show="!loading && status !== 'success'">{{ __('app.home_newsletter_subscribe') }}</span>
                        <span x-show="loading" x-cloak>
                            <svg class="w-5 h-5 animate-spin inline" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </span>
                        <span x-show="status === 'success'" x-cloak>{{ __('app.home_newsletter_thanks') }}</span>
                    </button>
                </div>

                {{-- Feedback messages --}}
                <div class="mt-4 min-h-[1.5rem] text-center">
                    <p x-show="status === 'success'" x-cloak
                       class="font-outfit text-sm text-green-400">
                        {{ __('app.home_newsletter_success') }}
                    </p>
                    <p x-show="status === 'duplicate'" x-cloak
                       class="font-outfit text-sm text-white/60">
                        {{ __('app.home_newsletter_duplicate') }}
                    </p>
                    <p x-show="status === 'error'" x-cloak
                       class="font-outfit text-sm text-red-400">
                        {{ __('app.home_newsletter_error') }}
                    </p>
                </div>
            </form>

            <p class="font-outfit text-body-sm text-white/45 mt-4">
                {{ __('app.home_newsletter_disclaimer_1') }}
                <a href="{{ route('privacy') }}" class="underline font-semibold text-white/70
                                   hover:text-white transition-colors">{{ __('app.home_newsletter_disclaimer_link') }}</a>.
            </p>
        </div>
    </section>

    {{-- ── Papeleria Announcement Popup ───────────────────────────────────── --}}
    <div
        x-data="{
            open: false,
            init() {
                const key  = 'copyus_papeleria_v1';
                const last = localStorage.getItem(key);
                if (!last || Date.now() - parseInt(last) > 86400000) {
                    setTimeout(() => { this.open = true; }, 900);
                }
            },
            close() {
                this.open = false;
                localStorage.setItem('copyus_papeleria_v1', Date.now());
            }
        }"
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display:none"
        @keydown.escape.window="close()"
    >
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-dark/75 backdrop-blur-sm" @click="close()"></div>

        {{-- Card --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-3"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-3"
            class="relative w-full max-w-md bg-white rounded-3xl overflow-hidden shadow-2xl z-10"
        >
            {{-- ── Header — solid dark, no gradient ── --}}
            <div class="relative bg-dark px-8 pt-9 pb-9 overflow-hidden">

                {{-- Decorative ring top-right --}}
                <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full
                            border-[36px] border-white/[0.05]"></div>
                {{-- Decorative ring bottom-left --}}
                <div class="absolute -bottom-8 -left-8 w-32 h-32 rounded-full
                            border-[24px] border-white/[0.04]"></div>
                {{-- Accent dot --}}
                <div class="absolute top-9 right-9 w-2 h-2 rounded-full bg-primary"></div>

                {{-- Close button --}}
                <button @click="close()"
                        class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center
                               rounded-full text-white/40 hover:text-white hover:bg-white/10
                               transition-all focus:outline-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                {{-- Eyebrow label --}}
                <p class="font-outfit text-xs font-semibold uppercase tracking-[0.2em]
                           text-primary mb-5 relative">
                    {{ __('app.popup_eyebrow') }}
                </p>

                {{-- Main headline --}}
                <h2 class="font-alumni leading-none relative" style="font-size: 3rem;">
                    <span class="text-white/70">{{ __('app.popup_headline_1') }}</span><br>
                    <span class="text-primary">{{ __('app.popup_headline_2') }}</span>
                </h2>

                {{-- Subtitle --}}
                <p class="font-outfit text-sm text-white/55 mt-4 leading-relaxed relative max-w-xs">
                    {{ __('app.popup_subtitle') }}
                </p>
            </div>

            {{-- ── Features ── --}}
            <div class="px-8 py-7 space-y-5">

                {{-- Feature 1: Online order --}}
                <div class="flex items-start gap-4">
                    <div class="w-9 h-9 rounded-xl bg-light flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-outfit text-sm font-semibold text-dark leading-tight">
                            {{ __('app.popup_f1_title') }}
                        </p>
                        <p class="font-outfit text-xs text-gray-400 mt-1 leading-relaxed">
                            {{ __('app.popup_f1_desc') }}
                        </p>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t border-gray-100"></div>

                {{-- Feature 2: In-store --}}
                <div class="flex items-start gap-4">
                    <div class="w-9 h-9 rounded-xl bg-light flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-outfit text-sm font-semibold text-dark leading-tight">
                            {{ __('app.popup_f2_title') }}
                        </p>
                        <p class="font-outfit text-xs text-gray-400 mt-1 leading-relaxed">
                            {{ __('app.popup_f2_desc') }}
                        </p>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t border-gray-100"></div>

                {{-- Feature 3: 24-48h --}}
                <div class="flex items-start gap-4">
                    <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-outfit text-sm font-semibold text-dark leading-tight">
                            {{ __('app.popup_f3_title') }}
                        </p>
                        <p class="font-outfit text-xs text-gray-400 mt-1 leading-relaxed">
                            {{ __('app.popup_f3_desc') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- ── Footer CTA ── --}}
            <div class="px-8 pb-8 pt-1">
                <a href="{{ route('products.index') }}"
                   @click="close()"
                   class="block w-full text-center bg-primary text-white
                          font-alumni text-sm-header tracking-wide
                          py-4 rounded-2xl hover:brightness-110 active:scale-[0.98]
                          transition-all duration-200">
                    {{ __('app.popup_cta') }} →
                </a>
                <button @click="close()"
                        class="block w-full text-center font-outfit text-xs text-gray-400
                               hover:text-dark transition-colors mt-3">
                    {{ __('app.popup_dismiss') }}
                </button>
            </div>
        </div>
    </div>

@endsection
