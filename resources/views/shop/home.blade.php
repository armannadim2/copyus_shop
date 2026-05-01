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

                {{-- Right: featured card (no white frame) --}}
                <div class="relative animate-reveal-up delay-300">
                    <div class="relative rounded-3xl overflow-hidden h-80 md:h-[28rem]
                                flex items-end p-8 shadow-[0_20px_50px_rgba(0,0,0,0.25)]">
                        {{-- Hero image --}}
                        <img src="{{ asset('images/hero_1.png') }}"
                             alt="{{ __('app.home_hero_card_eyebrow') }}"
                             class="absolute inset-0 w-full h-full object-cover">
                        {{-- Bottom gradient for legibility --}}
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

            <form x-data="{ email: '', sent: false }"
                  @submit.prevent="if(email){sent=true; email=''}"
                  class="mt-10 flex flex-col sm:flex-row gap-3 max-w-2xl mx-auto">

                {{-- Email input — pill, dark, with trailing icon --}}
                <div class="relative flex-1">
                    <input type="email" x-model="email" required
                           :disabled="sent"
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
                <button type="submit" :disabled="sent"
                        class="bg-secondary text-white font-alumni font-bold italic text-sm-header
                               px-9 py-4 rounded-full hover:brightness-110
                               active:scale-95 transition-all whitespace-nowrap
                               disabled:opacity-50">
                    <span x-show="!sent">{{ __('app.home_newsletter_subscribe') }}</span>
                    <span x-show="sent" x-cloak>{{ __('app.home_newsletter_thanks') }}</span>
                </button>
            </form>

            <p class="font-outfit text-body-sm text-white/45 mt-6">
                {{ __('app.home_newsletter_disclaimer_1') }}
                <a href="{{ route('privacy') }}" class="underline font-semibold text-white/70
                                   hover:text-white transition-colors">{{ __('app.home_newsletter_disclaimer_link') }}</a>.
            </p>
        </div>
    </section>

@endsection
