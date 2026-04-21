@extends('layouts.app')
@section('title', __('app.welcome'))

@section('content')

    {{-- ── Hero ─────────────────────────────────────────────────────────────── --}}
    <section class="relative bg-dark overflow-hidden -mt-16 lg:-mt-20">

        {{-- Decorative gradient blobs --}}
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute -top-32 -right-32 w-[500px] h-[500px] rounded-full
                        bg-secondary opacity-10 blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 w-[400px] h-[400px] rounded-full
                        bg-primary opacity-10 blur-3xl"></div>
        </div>

        <div class="section relative py-28 md:py-36">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 bg-white/10 rounded-full
                            px-4 py-1.5 mb-6 animate-reveal-up">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    <span class="font-outfit text-body-md text-white/70">
                        Tecnocampus Mataró · Papereria
                    </span>
                </div>

                <h1 class="font-alumni font-black text-h1 text-white leading-tight
                           animate-reveal-up delay-100">
                    {{ __('app.hero_title') }}<br>
                    <span class="text-gradient">Copyus</span>
                </h1>

                <p class="font-outfit text-body-lg text-white/60 mt-6 max-w-xl leading-relaxed
                          animate-reveal-up delay-200">
                    {{ __('app.hero_subtitle') }}
                </p>

                <div class="flex flex-wrap gap-4 mt-10 animate-reveal-up delay-300">
                    <a href="{{ route('products.index') }}" class="btn-primary">
                        {{ __('app.browse_products') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="btn-outline-white">
                            {{ __('app.register') }}
                        </a>
                    @endguest
                </div>

                {{-- Stats --}}
                <div class="flex flex-wrap gap-8 mt-14 animate-reveal-up delay-400">
                    <div>
                        <p class="font-alumni font-black text-h4 text-primary">500+</p>
                        <p class="font-outfit text-body-md text-white/50">Productes</p>
                    </div>
                    <div class="w-px bg-white/10"></div>
                    <div>
                        <p class="font-alumni font-black text-h4 text-white">100%</p>
                        <p class="font-outfit text-body-md text-white/50">Satisfacció</p>
                    </div>
                    <div class="w-px bg-white/10"></div>
                    <div>
                        <p class="font-alumni font-black text-h4 text-secondary">24h</p>
                        <p class="font-outfit text-body-md text-white/50">Entrega ràpida</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Register Banner (guests) ─────────────────────────────────────────────── --}}
    @guest
        <section class="bg-secondary">
            <div class="section py-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <p class="font-alumni text-sm-header font-semibold text-white">
                        {{ __('app.prices_hidden') }}
                    </p>
                </div>
                <a href="{{ route('register') }}"
                   class="shrink-0 bg-white text-secondary font-alumni text-sm-header font-bold
                          px-5 py-2 rounded-xl hover:bg-light transition-colors">
                    {{ __('app.register') }} →
                </a>
            </div>
        </section>
    @endguest

    {{-- ── Featured Products ───────────────────────────────────────────────── --}}
    @if($featuredProducts->isNotEmpty())
        <section class="py-20">
            <div class="section">
                <div class="flex items-end justify-between mb-10">
                    <div>
                        <p class="font-outfit text-body-md text-primary font-semibold uppercase tracking-widest mb-2">
                            Catàleg
                        </p>
                        <h2 class="font-alumni font-black text-h2 text-dark">
                            Productes destacats
                        </h2>
                    </div>
                    <a href="{{ route('products.index') }}"
                       class="hidden md:flex items-center gap-2 font-alumni text-sm-header
                              font-semibold text-secondary hover:text-primary transition-colors">
                        Veure tot
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                    @foreach($featuredProducts as $product)
                        <a href="{{ route('products.show', $product->slug) }}"
                           class="card group overflow-hidden">
                            {{-- Thumbnail --}}
                            <div class="aspect-square bg-light flex items-center justify-center overflow-hidden">
                                @if($product->thumbnail)
                                    <img src="{{ asset('storage/'.$product->thumbnail) }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-12 h-12 text-dark/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            {{-- Info --}}
                            <div class="p-4">
                                @if($product->brand)
                                    <p class="font-outfit text-body-sm text-primary font-semibold uppercase
                                               tracking-wider mb-1">
                                        {{ $product->brand }}
                                    </p>
                                @endif
                                <h3 class="font-alumni text-sm-header font-bold text-dark leading-tight
                                           group-hover:text-primary transition-colors line-clamp-2">
                                    {{ $product->name }}
                                </h3>
                                @auth
                                    @if(auth()->user()->canSeePrices())
                                        <p class="mt-2 font-alumni text-h6 text-primary font-black">
                                            {{ number_format($product->price, 2, ',', '.') }} €
                                            <span class="font-outfit text-body-sm text-dark/40 font-normal">
                                                / {{ $product->unit }}
                                            </span>
                                        </p>
                                    @endif
                                @endauth
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-8 text-center md:hidden">
                    <a href="{{ route('products.index') }}" class="btn-outline">
                        Veure tots els productes
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- ── Categories ──────────────────────────────────────────────────────── --}}
    @if($categories->isNotEmpty())
        <section class="py-16 bg-white">
            <div class="section">
                <div class="text-center mb-10">
                    <p class="font-outfit text-body-md text-secondary font-semibold uppercase tracking-widest mb-2">
                        Categories
                    </p>
                    <h2 class="font-alumni font-black text-h3 text-dark">
                        Tot el que necessites
                    </h2>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($categories as $category)
                        <a href="{{ route('products.category', $category->slug) }}"
                           class="group flex flex-col items-center text-center p-6
                                  rounded-2xl border-2 border-transparent bg-light
                                  hover:border-primary hover:bg-primary/5 transition-all duration-200">
                            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center
                                        mb-3 group-hover:bg-primary group-hover:scale-110 transition-all duration-200">
                                <svg class="w-6 h-6 text-primary group-hover:text-white transition-colors duration-200"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <h3 class="font-alumni text-sm-header font-bold text-dark
                                       group-hover:text-primary transition-colors">
                                {{ $category->name }}
                            </h3>
                            @if($category->active_products_count > 0)
                                <p class="font-outfit text-body-sm text-dark/40 mt-1">
                                    {{ $category->active_products_count }} productes
                                </p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ── Features ─────────────────────────────────────────────────────────── --}}
    <section class="py-20">
        <div class="section">
            <div class="text-center mb-12">
                <p class="font-outfit text-body-md text-primary font-semibold uppercase tracking-widest mb-2">
                    Per què Copyus
                </p>
                <h2 class="font-alumni font-black text-h2 text-dark">
                    {{ __('app.why_copyus') }}
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="card p-8 group">
                    <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center mb-5
                                group-hover:bg-primary transition-colors duration-300">
                        <svg class="w-7 h-7 text-primary group-hover:text-white transition-colors duration-300"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <h3 class="font-alumni font-bold text-h5 text-dark mb-2">
                        {{ __('app.feature_catalog_title') }}
                    </h3>
                    <p class="font-outfit text-body-lg text-dark/60 leading-relaxed">
                        {{ __('app.feature_catalog_desc') }}
                    </p>
                </div>

                <div class="card p-8 group">
                    <div class="w-14 h-14 rounded-2xl bg-secondary/10 flex items-center justify-center mb-5
                                group-hover:bg-secondary transition-colors duration-300">
                        <svg class="w-7 h-7 text-secondary group-hover:text-white transition-colors duration-300"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="font-alumni font-bold text-h5 text-dark mb-2">
                        {{ __('app.feature_b2b_title') }}
                    </h3>
                    <p class="font-outfit text-body-lg text-dark/60 leading-relaxed">
                        {{ __('app.feature_b2b_desc') }}
                    </p>
                </div>

                <div class="card p-8 group">
                    <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center mb-5
                                group-hover:bg-primary transition-colors duration-300">
                        <svg class="w-7 h-7 text-primary group-hover:text-white transition-colors duration-300"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="font-alumni font-bold text-h5 text-dark mb-2">
                        {{ __('app.feature_invoice_title') }}
                    </h3>
                    <p class="font-outfit text-body-lg text-dark/60 leading-relaxed">
                        {{ __('app.feature_invoice_desc') }}
                    </p>
                </div>

            </div>
        </div>
    </section>

    {{-- ── Print Services ─────────────────────────────────────────────────── --}}
    @if($printTemplates->isNotEmpty())
        <section class="py-20 bg-dark overflow-hidden relative">
            {{-- Decorative blobs --}}
            <div class="absolute inset-0 pointer-events-none overflow-hidden">
                <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-secondary opacity-10 blur-3xl"></div>
                <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-primary opacity-10 blur-3xl"></div>
            </div>

            <div class="section relative">
                <div class="flex flex-col md:flex-row md:items-end md:justify-between mb-12 gap-4">
                    <div>
                        <p class="font-outfit text-body-md text-secondary font-semibold uppercase tracking-widest mb-2">
                            Impressió a mida
                        </p>
                        <h2 class="font-alumni font-black text-h2 text-white">
                            Servei d'impressió professional
                        </h2>
                        <p class="font-outfit text-body-lg text-white/50 mt-3 max-w-xl leading-relaxed">
                            Configura el teu encàrrec en temps real, obté el preu a l'instant i rep-ho a l'oficina.
                        </p>
                    </div>
                    @auth
                        @if(auth()->user()->canSeePrices())
                            <a href="{{ route('print.index') }}"
                               class="shrink-0 btn-primary">
                                Veure totes les plantilles
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                        @endif
                    @endauth
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    @foreach($printTemplates as $template)
                        <div class="bg-white/5 border border-white/10 rounded-3xl p-6 hover:bg-white/10 hover:border-white/20 transition-all duration-200 group">
                            <div class="w-12 h-12 rounded-2xl bg-secondary/20 flex items-center justify-center mb-5 group-hover:bg-secondary transition-colors duration-300">
                                <svg class="w-6 h-6 text-secondary group-hover:text-white transition-colors duration-300"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                            </div>

                            <h3 class="font-alumni font-bold text-h5 text-white mb-2">
                                {{ $template->getTranslation('name', app()->getLocale()) }}
                            </h3>

                            @if($template->getTranslation('description', app()->getLocale()))
                                <p class="font-outfit text-body-md text-white/50 mb-4 leading-relaxed line-clamp-2">
                                    {{ $template->getTranslation('description', app()->getLocale()) }}
                                </p>
                            @endif

                            <div class="flex items-center justify-between mt-4">
                                @auth
                                    @if(auth()->user()->canSeePrices())
                                        @php $tier = $template->quantityTiers->first(); @endphp
                                        @if($tier)
                                            <p class="font-outfit text-body-sm text-white/40">
                                                Des de <span class="text-secondary font-semibold">{{ number_format($tier->unit_price, 2, ',', '.') }} €/u</span>
                                            </p>
                                        @endif
                                        <a href="{{ route('print.builder', $template->slug) }}"
                                           class="shrink-0 font-alumni text-sm-header bg-secondary text-white px-4 py-2 rounded-xl hover:brightness-110 transition-all">
                                            Configurar →
                                        </a>
                                    @else
                                        <p class="font-outfit text-body-sm text-white/40">Registra't per veure preus</p>
                                    @endif
                                @else
                                    <p class="font-outfit text-body-sm text-white/40">Registra't per accedir</p>
                                    <a href="{{ route('register') }}"
                                       class="shrink-0 font-alumni text-sm-header bg-white/10 text-white px-4 py-2 rounded-xl hover:bg-white/20 transition-all">
                                        Registrar-se →
                                    </a>
                                @endauth
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Benefits strip --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach([
                        ['icon' => '⚡', 'title' => 'Preu en temps real', 'desc' => 'Veu el cost mentre configures'],
                        ['icon' => '🎨', 'title' => 'Múltiples acabats', 'desc' => 'Glossat, mat, plastificat...'],
                        ['icon' => '📦', 'title' => 'Entrega a oficina', 'desc' => 'Recollida al campus'],
                        ['icon' => '✅', 'title' => 'Qualitat garantida', 'desc' => 'Impressió professional'],
                    ] as $benefit)
                        <div class="flex items-start gap-3 bg-white/5 rounded-2xl px-4 py-4">
                            <span class="text-2xl shrink-0">{{ $benefit['icon'] }}</span>
                            <div>
                                <p class="font-alumni text-sm-header text-white">{{ $benefit['title'] }}</p>
                                <p class="font-outfit text-body-sm text-white/40 mt-0.5">{{ $benefit['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ── CTA ──────────────────────────────────────────────────────────────── --}}
    <section class="py-20">
        <div class="section">
            <div class="relative bg-dark rounded-3xl overflow-hidden px-8 py-16 text-center">
                {{-- Blobs --}}
                <div class="absolute inset-0 pointer-events-none overflow-hidden">
                    <div class="absolute -top-16 -right-16 w-72 h-72 rounded-full
                                bg-secondary opacity-15 blur-3xl"></div>
                    <div class="absolute -bottom-16 -left-16 w-72 h-72 rounded-full
                                bg-primary opacity-15 blur-3xl"></div>
                </div>

                <div class="relative max-w-2xl mx-auto">
                    <h2 class="font-alumni font-black text-h2 text-white mb-4">
                        {{ __('app.cta_title') }}
                    </h2>
                    <p class="font-outfit text-body-lg text-white/60 mb-10 leading-relaxed">
                        {{ __('app.cta_subtitle') }}
                    </p>
                    @guest
                        <a href="{{ route('register') }}" class="btn-primary">
                            {{ __('app.register_now') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('products.index') }}" class="btn-primary">
                            {{ __('app.browse_products') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

@endsection
