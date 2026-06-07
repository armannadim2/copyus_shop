@extends('layouts.app')
@section('full_title', __('app.papereria_full_title'))
@section('meta_description', __('app.papereria_meta_description'))

@section('content')

    {{-- ── Hero ─────────────────────────────────────────────────────────────── --}}
    <section class="relative bg-dark overflow-hidden -mt-16 lg:-mt-20">

        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute -top-32 -left-32 w-[500px] h-[500px] rounded-full
                        bg-secondary opacity-10 blur-3xl"></div>
            <div class="absolute -bottom-20 -right-20 w-[400px] h-[400px] rounded-full
                        bg-primary opacity-10 blur-3xl"></div>
        </div>

        <div class="section relative py-28 md:py-36">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 bg-white/10 rounded-full
                            px-4 py-1.5 mb-6 animate-reveal-up">
                    <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                    <span class="font-outfit text-body-md text-white/70">
                        {{ __('app.papereria_badge') }}
                    </span>
                </div>

                <h1 class="font-alumni text-h1 text-white animate-reveal-up delay-100">
                    {{ __('app.papereria_h1') }}
                </h1>

                <p class="font-outfit text-body-lg text-white/60 mt-6 max-w-xl leading-relaxed
                          animate-reveal-up delay-200">
                    {{ __('app.papereria_intro') }}
                </p>

                <div class="flex flex-wrap gap-4 mt-10 animate-reveal-up delay-300">
                    <a href="{{ route('products.index') }}" class="btn-primary">
                        {{ __('app.papereria_cta_products') }}
                    </a>
                    <a href="{{ route('request-quote') }}" class="btn-outline-white">
                        {{ __('app.papereria_cta_quote') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Corporate section ───────────────────────────────────────────────── --}}
    <section id="empreses" class="py-20">
        <div class="section">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

                <div>
                    <p class="font-outfit text-body-md font-semibold text-primary uppercase
                              tracking-widest mb-3">
                        {{ __('app.papereria_badge') }}
                    </p>
                    <h2 class="font-alumni text-h2 text-dark mb-6">
                        {{ __('app.papereria_h2_corp') }}
                    </h2>
                    <p class="font-outfit text-body-lg text-gray-500 leading-relaxed mb-8">
                        {{ __('app.papereria_corp_desc') }}
                    </p>

                    <div class="grid grid-cols-2 gap-3 mb-8">
                        @foreach([
                            ['icon' => '🖊️', 'label' => 'Bolígrafs i llapis'],
                            ['icon' => '📓', 'label' => 'Quaderns i llibretes'],
                            ['icon' => '🗂️', 'label' => 'Carpetes i arxivadors'],
                            ['icon' => '🖨️', 'label' => 'Consumibles d\'impressora'],
                        ] as $item)
                        <div class="flex items-center gap-2 bg-light rounded-xl px-4 py-3">
                            <span class="text-xl">{{ $item['icon'] }}</span>
                            <span class="font-outfit text-body-sm text-dark font-medium">{{ $item['label'] }}</span>
                        </div>
                        @endforeach
                    </div>

                    <a href="{{ route('request-quote') }}"
                       class="inline-flex items-center gap-2 font-outfit text-body-md
                              font-semibold text-primary hover:gap-3 transition-all">
                        {{ __('app.papereria_cta_quote') }} →
                    </a>
                </div>

                <div class="relative">
                    <div class="bg-white rounded-3xl p-3 shadow-[0_20px_60px_rgba(36,48,46,0.08)]
                                overflow-hidden">
                        <img src="{{ asset('images/corporate_business.png') }}"
                             alt="{{ __('app.papereria_h2_corp') }}"
                             class="rounded-2xl w-full h-80 md:h-96 object-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Students section ────────────────────────────────────────────────── --}}
    <section id="estudiants" class="py-20 bg-light">
        <div class="section">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

                <div class="order-2 lg:order-1 relative">
                    <div class="bg-white rounded-3xl p-3 shadow-[0_20px_60px_rgba(36,48,46,0.08)]
                                overflow-hidden">
                        <img src="{{ asset('images/editorial.png') }}"
                             alt="{{ __('app.papereria_h2_students') }}"
                             class="rounded-2xl w-full h-80 md:h-96 object-cover">
                    </div>

                    {{-- Floating badge --}}
                    <div class="absolute -bottom-5 -right-5 bg-white rounded-2xl
                                shadow-[0_10px_30px_rgba(36,48,46,0.15)] px-5 py-3 border border-gray-100">
                        <p class="font-alumni font-extrabold text-h4 text-secondary leading-none text-center">TecnoCampus</p>
                        <p class="font-outfit text-body-sm font-bold text-primary mt-1 text-center
                                  uppercase tracking-wider">Material escolar</p>
                    </div>
                </div>

                <div class="order-1 lg:order-2">
                    <p class="font-outfit text-body-md font-semibold text-secondary uppercase
                              tracking-widest mb-3">
                        {{ __('app.papereria_badge') }}
                    </p>
                    <h2 class="font-alumni text-h2 text-dark mb-6">
                        {{ __('app.papereria_h2_students') }}
                    </h2>
                    <p class="font-outfit text-body-lg text-gray-500 leading-relaxed mb-8">
                        {{ __('app.papereria_students_desc') }}
                    </p>

                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center gap-2 font-outfit text-body-md
                              font-semibold text-secondary hover:gap-3 transition-all">
                        {{ __('app.papereria_cta_products') }} →
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ── How it works ─────────────────────────────────────────────────────── --}}
    <section class="py-20">
        <div class="section">
            <div class="max-w-2xl mb-14">
                <p class="font-outfit text-body-md font-semibold text-primary uppercase
                          tracking-widest mb-3">
                    Copyus Papereria
                </p>
                <h2 class="font-alumni text-h2 text-dark">
                    {{ __('app.papereria_h2_how') }}
                </h2>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                @foreach([
                    [
                        'n'     => '01',
                        'title' => __('app.papereria_how_step1_title'),
                        'desc'  => __('app.papereria_how_step1_desc'),
                        'color' => 'primary',
                    ],
                    [
                        'n'     => '02',
                        'title' => __('app.papereria_how_step2_title'),
                        'desc'  => __('app.papereria_how_step2_desc'),
                        'color' => 'secondary',
                    ],
                    [
                        'n'     => '03',
                        'title' => __('app.papereria_how_step3_title'),
                        'desc'  => __('app.papereria_how_step3_desc'),
                        'color' => 'dark',
                    ],
                ] as $step)
                <div class="relative bg-white rounded-3xl p-7 border border-gray-100
                            hover:border-primary/40 hover:shadow-lg transition-all">
                    <p class="font-alumni text-h2 text-{{ $step['color'] }}/20 leading-none">
                        {{ $step['n'] }}
                    </p>
                    <h3 class="font-alumni text-h5 text-dark mt-3">
                        {{ $step['title'] }}
                    </h3>
                    <p class="font-outfit text-body-sm text-gray-500 mt-2 leading-relaxed">
                        {{ $step['desc'] }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── Internal links ───────────────────────────────────────────────────── --}}
    <section class="py-16 bg-light border-t border-gray-100">
        <div class="section">
            <div class="grid md:grid-cols-3 gap-4">

                <a href="{{ route('print.index') }}"
                   class="group flex items-start gap-4 bg-white rounded-2xl p-6
                          border border-gray-100 hover:border-primary/40
                          hover:shadow-md transition-all">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center
                                justify-center shrink-0 mt-0.5">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-outfit text-body-md font-semibold text-dark
                                  group-hover:text-primary transition-colors">
                            {{ __('app.papereria_cta_print') }}
                        </p>
                    </div>
                </a>

                <a href="{{ route('services') }}"
                   class="group flex items-start gap-4 bg-white rounded-2xl p-6
                          border border-gray-100 hover:border-secondary/40
                          hover:shadow-md transition-all">
                    <div class="w-10 h-10 rounded-xl bg-secondary/10 flex items-center
                                justify-center shrink-0 mt-0.5">
                        <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-outfit text-body-md font-semibold text-dark
                                  group-hover:text-secondary transition-colors">
                            {{ __('app.papereria_cta_services') }}
                        </p>
                    </div>
                </a>

                <a href="{{ route('request-quote') }}"
                   class="group flex items-start gap-4 bg-white rounded-2xl p-6
                          border border-gray-100 hover:border-dark/40
                          hover:shadow-md transition-all">
                    <div class="w-10 h-10 rounded-xl bg-dark/10 flex items-center
                                justify-center shrink-0 mt-0.5">
                        <svg class="w-5 h-5 text-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-outfit text-body-md font-semibold text-dark
                                  group-hover:text-dark/70 transition-colors">
                            {{ __('app.papereria_cta_quote') }}
                        </p>
                    </div>
                </a>
            </div>
        </div>
    </section>

@endsection
