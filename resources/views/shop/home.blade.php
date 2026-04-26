@extends('layouts.app')
@section('title', 'Impressió digital · Copyus')

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
                        Excel·lència en cada<br>
                        <em class="italic">Impressió Digital.</em>
                    </h1>

                    <p class="font-outfit font-light text-body-lg text-white/90 mt-6 max-w-xl
                              animate-reveal-up delay-100">
                        Oferim solucions de producció d'elit que eleven la presència
                        de la teva marca mitjançant precisió tècnica i materials de
                        primera qualitat.
                    </p>

                    <div class="flex flex-wrap gap-3 mt-10 animate-reveal-up delay-200">
                        <a href="{{ route('print.index') }}"
                           class="inline-flex items-center justify-center
                                  bg-dark text-white font-outfit text-body-lg
                                  px-7 py-3.5 rounded-xl hover:brightness-110
                                  active:scale-95 transition-all duration-200">
                            Començar a imprimir
                        </a>
                        <a href="{{ route('products.index') }}"
                           class="inline-flex items-center justify-center
                                  bg-dark text-primary font-outfit text-body-lg
                                  px-7 py-3.5 rounded-xl hover:brightness-110
                                  active:scale-95 transition-all duration-200">
                            Veure productes
                        </a>
                    </div>
                </div>

                {{-- Right: featured card (no white frame) --}}
                <div class="relative animate-reveal-up delay-300">
                    <div class="relative rounded-3xl overflow-hidden h-80 md:h-[28rem]
                                flex items-end p-8 shadow-[0_20px_50px_rgba(0,0,0,0.25)]">
                        {{-- Hero image --}}
                        <img src="{{ asset('images/hero_1.png') }}"
                             alt="Impressió tèxtil"
                             class="absolute inset-0 w-full h-full object-cover">
                        {{-- Bottom gradient for legibility --}}
                        <div class="absolute inset-x-0 bottom-0 h-2/5
                                    bg-gradient-to-t from-black/70 to-transparent"></div>

                        <div class="relative">
                            <p class="font-outfit font-bold text-body-md text-white/90
                                      uppercase tracking-wider mb-1">
                                Impressió Tèxtil d'Alta Resolució
                            </p>
                            <h3 class="font-alumni text-h4 text-white">
                                Vesteix la Teva Marca
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
                    Sobre Nosaltres
                </h2>
                <p class="font-outfit text-body-lg text-white/80 mt-6 leading-relaxed max-w-2xl mx-auto">
                    Des dels nostres inicis, COPYUS ha unit la creativitat digital amb la
                    realitat tangible, oferint solucions d'impressió de classe mundial.
                </p>
            </div>
        </div>
    </section>

    {{-- ── Compromesos amb l'Excel·lència ──────────────────────────────────── --}}
    <section class="bg-light py-24">
        <div class="section grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

            <div>
                <h2 class="font-alumni text-h2 text-dark mb-10">
                    Compromesos amb<br>l'Excel·lència
                </h2>

                <div class="space-y-8">
                    <div class="border-l-2 border-dark/40 pl-6">
                        <p class="font-alumni italic text-sm-header text-dark mb-2">
                            La nostra Missió
                        </p>
                        <p class="font-outfit text-body-lg text-primary leading-relaxed">
                            Empoderar marques i individus oferint serveis d'impressió
                            digital innovadors que donin vida a les seves idees.
                        </p>
                    </div>

                    <div class="border-l-2 border-dark/40 pl-6">
                        <p class="font-alumni italic text-sm-header text-dark mb-2">
                            La nostra Visió
                        </p>
                        <p class="font-outfit text-body-lg text-primary leading-relaxed">
                            Convertir-nos en l'estàndard global d'impressió digital i
                            marxandatge personalitzat.
                        </p>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="bg-white rounded-3xl p-3 shadow-[0_20px_60px_rgba(36,48,46,0.08)]
                            relative overflow-hidden">
                    <img src="{{ asset('images/about_us.png') }}"
                         alt="Productes Copyus"
                         class="rounded-2xl w-full h-80 md:h-96 object-cover">
                </div>

                {{-- Floating badge --}}
                <div class="absolute -bottom-5 -right-5 bg-white rounded-2xl
                            shadow-[0_10px_30px_rgba(36,48,46,0.15)] px-6 py-4 border border-gray-100">
                    <p class="font-alumni font-extrabold text-h3 text-secondary leading-none text-center">15+</p>
                    <p class="font-outfit text-body-sm font-bold text-primary mt-1.5 text-center
                              uppercase tracking-wider">Years Active</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Els nostres Valors ──────────────────────────────────────────────── --}}
    <section class="bg-light py-24">
        <div class="section">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <h2 class="font-alumni text-h2 text-dark">
                    Els nostres Valors
                </h2>
                <p class="font-outfit text-body-lg text-gray-500 mt-3">
                    Els principis que guien tot el que fem a COPYUS.
                </p>
            </div>

            @php
                $values = [
                    ['icon' => '⭐', 'title' => 'Qualitat Primer',  'desc' => 'Mai comprometem el resultat final. Cada píxel i cada gota de tinta importa.'],
                    ['icon' => '💡', 'title' => 'Innovació',         'desc' => 'Invertim en les últimes tecnologies d\'impressió per oferir solucions d\'avantguarda.'],
                    ['icon' => '🌱', 'title' => 'Sostenibilitat',    'desc' => 'Prioritzem tintes ecològiques i materials reciclables per minimitzar la petjada.'],
                    ['icon' => '🤝', 'title' => 'Integritat',        'desc' => 'Preus transparents i comunicació honesta són la base de les nostres relacions.'],
                    ['icon' => '⚡', 'title' => 'Velocitat',         'desc' => 'En l\'era digital, el temps és or. Ens encarreguem dels nostres terminis ràpids.'],
                    ['icon' => '🏆', 'title' => 'Èxit del Client',   'desc' => 'El nostre èxit es mesura per l\'impacte que les nostres impressions tenen en tu.'],
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
                    Serveis
                </h2>
                <p class="font-outfit text-body-lg text-gray-500 mt-3">
                    Impulsem la teva marca amb un conjunt estratègic que connecta,
                    convenç i converteix.
                </p>
            </div>

            <div class="text-left max-w-3xl mx-auto mt-12 mb-10">
                <h3 class="font-alumni text-h3 text-secondary">
                    Enginyeria d'<em class="italic">Impressió d'Èlit.</em>
                </h3>
                <p class="font-outfit text-body-md text-gray-500 mt-3">
                    Des de sistemes d'identitat corporativa fins a actius de
                    màrqueting físics massius, oferim un espectre complet de
                    serveis de producció.
                </p>
                <div class="mt-4 h-0.5 bg-gradient-to-r from-secondary via-primary to-transparent w-32"></div>
            </div>

            @php
                $homeServices = [
                    [
                        'title' => 'Empreses i Corporatiu',
                        'desc'  => 'Impressió professional per a la teva marca.',
                        'image' => 'images/corporate_business.png',
                        'href'  => 'request-quote',
                    ],
                    [
                        'title' => 'Gran Format',
                        'desc'  => 'Causa un gran impacte amb impressions massives.',
                        'image' => 'images/gran_format.png',
                        'href'  => 'request-quote',
                    ],
                    [
                        'title' => 'Marxandatge',
                        'desc'  => 'Marca\'t el que puguis imaginar.',
                        'image' => 'images/merchandising.png',
                        'href'  => 'request-quote',
                    ],
                    [
                        'title' => 'Editorial i Llibres',
                        'desc'  => 'Servei complet d\'enquadernació i impressió.',
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
                                Saber-ne més →
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
                Confiat per Marques Líders
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

    {{-- ── Contacta amb Nosaltres ──────────────────────────────────────────── --}}
    <section id="contacte" class="bg-light py-24">
        <div class="section">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <h2 class="font-alumni italic text-h2 text-dark">
                    Contacta amb Nosaltres
                </h2>
                <p class="font-outfit text-body-md text-primary mt-3">
                    Tant si tens una comanda corporativa com un projecte personal,
                    el nostre equip t'ajudarà.
                </p>
            </div>

            @if(session('success'))
                <div class="max-w-3xl mx-auto mb-8 px-5 py-4 bg-green-50 border
                            border-green-200 rounded-2xl font-outfit text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid lg:grid-cols-2 gap-6 lg:gap-10 max-w-6xl mx-auto">

                {{-- ─── LEFT: Form card ──────────────────────────────────────── --}}
                <div class="bg-white rounded-3xl border border-gray-100 p-8 md:p-10
                            shadow-[0_4px_24px_rgba(36,48,46,0.04)]">

                    @if($errors->any())
                        <div class="mb-6 bg-red-50 border border-red-200 text-red-600
                                    font-outfit text-body-sm px-4 py-3 rounded-xl">
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    @php
                        $contactLabel = 'block font-outfit text-xs font-semibold uppercase
                                         tracking-widest text-primary mb-2';
                        $contactInput = 'w-full border border-gray-200 rounded-xl px-4 py-3
                                         font-outfit text-sm bg-white focus:outline-none
                                         focus:ring-2 focus:ring-primary/30 focus:border-primary
                                         transition placeholder:text-gray-300';
                    @endphp

                    <form method="POST" action="{{ route('contact.store') }}" class="space-y-5">
                        @csrf

                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="{{ $contactLabel }}">Nom complet</label>
                                <input type="text" name="name" required maxlength="120"
                                       value="{{ old('name', auth()->user()->name ?? '') }}"
                                       class="{{ $contactInput }}">
                            </div>
                            <div>
                                <label class="{{ $contactLabel }}">Correu electrònic</label>
                                <div class="relative">
                                    <input type="email" name="email" required maxlength="160"
                                           value="{{ old('email', auth()->user()->email ?? '') }}"
                                           class="{{ $contactInput }} pr-11">
                                    <svg class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4
                                                text-gray-300 pointer-events-none"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="{{ $contactLabel }}">Servei d'interès</label>
                            <select name="subject" required class="{{ $contactInput }}">
                                <option value="">Selecciona un servei…</option>
                                @foreach([
                                    'Impressió Corporativa',
                                    'Gran Format',
                                    'Marxandatge personalitzat',
                                    'Editorial i Llibres',
                                    'Papereria d\'oficina',
                                    'Altres',
                                ] as $opt)
                                    <option value="{{ $opt }}" @selected(old('subject') === $opt)>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="{{ $contactLabel }}">Missatge</label>
                            <textarea name="message" required rows="5" maxlength="5000"
                                      placeholder="Explica'ns què necessites…"
                                      class="{{ $contactInput }}">{{ old('message') }}</textarea>
                        </div>

                        {{-- Decorative reCAPTCHA-style placeholder --}}
                        <div class="flex items-center gap-3 bg-gray-50 border border-gray-200
                                    rounded-xl px-4 py-3">
                            <input type="checkbox" required
                                   class="w-5 h-5 rounded border-gray-300 text-primary
                                          focus:ring-primary cursor-pointer">
                            <span class="font-outfit text-sm text-gray-600">No sóc un robot</span>
                            <span class="ml-auto font-outfit text-[10px] text-gray-400 leading-tight text-right">
                                reCAPTCHA<br>
                                <span class="text-gray-300">Privacitat · Termes</span>
                            </span>
                        </div>

                        <button type="submit"
                                class="w-full bg-dark text-white font-alumni text-sm-header
                                       py-4 rounded-full hover:bg-primary
                                       active:scale-[0.99] transition-all">
                            Enviar missatge
                        </button>
                    </form>
                </div>

                {{-- ─── RIGHT: stacked info cards ──────────────────────────── --}}
                <div class="space-y-5">

                    {{-- Card 1: Visita el nostre taller --}}
                    <div class="bg-white rounded-3xl border border-gray-100 p-7 md:p-8
                                shadow-[0_4px_24px_rgba(36,48,46,0.04)]">

                        <h3 class="font-alumni italic text-h5 text-dark mb-6">
                            Visita el nostre taller
                        </h3>

                        {{-- Address row --}}
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-2xl bg-dark flex items-center
                                        justify-center shrink-0">
                                <svg class="w-5 h-5 text-secondary" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-outfit text-xs font-semibold uppercase tracking-widest text-primary mb-1">
                                    Adreça
                                </p>
                                <address class="not-italic font-outfit text-body-md text-gray-600 leading-relaxed">
                                    Parc TecnoCampus Mataró-Maresme, TCM3, Local 2<br>
                                    Carrer d'Ernest Lluch, 32, 08302<br>
                                    Mataró, Barcelona
                                </address>
                                <a href="https://www.google.com/maps/search/?api=1&query=Parc+TecnoCampus+Mataró"
                                   target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1 mt-3 font-outfit text-body-sm
                                          font-semibold text-secondary hover:text-primary transition-colors">
                                    Veure a Google Maps
                                </a>
                            </div>
                        </div>

                        {{-- Hours row --}}
                        <div class="flex items-start gap-4 mt-7 pt-7 border-t border-gray-100">
                            <div class="w-11 h-11 rounded-2xl bg-light border border-gray-100
                                        flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-outfit text-body-md text-gray-500 mb-2">
                                    Horari laboral
                                </p>
                                <ul class="font-outfit text-body-md space-y-1">
                                    <li class="flex items-center gap-3">
                                        <span class="text-gray-600">Dl – Dj:</span>
                                        <span class="text-primary font-semibold">9:00 – 17:00</span>
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <span class="text-gray-600">Dv:</span>
                                        <span class="text-primary font-semibold">9:00 – 14:00</span>
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <span class="text-gray-600">Ds i Dm:</span>
                                        <span class="text-gray-400">Tancat</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2: AI assistant — dark --}}
                    <div x-data="{ origin: '' }"
                         class="bg-dark rounded-3xl p-7 md:p-8 relative overflow-hidden">

                        <div class="absolute -top-16 -right-16 w-[220px] h-[220px] rounded-full
                                    bg-secondary/15 blur-3xl pointer-events-none"></div>

                        <div class="relative">
                            <span class="inline-flex items-center gap-2 font-outfit text-xs
                                         font-semibold uppercase tracking-[0.25em]
                                         text-secondary mb-4">
                                <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                                Expert en Ubicació
                            </span>

                            <h5 class="font-alumni italic text-h5 text-white">
                                Necessites ajuda per trobar-nos?
                            </h5>
                            <p class="font-outfit text-body-md text-white/60 mt-2 mb-6">
                                Pregunta a la nostra IA sobre pàrquing o transport públic.
                            </p>

                            <div class="flex gap-3">
                                <input type="text" x-model="origin"
                                       placeholder="On és el pàrquing més proper?"
                                       @keydown.enter.prevent="if(origin){window.open('https://www.google.com/maps/dir/?api=1&origin='+encodeURIComponent(origin)+'&destination=Carrer+d%27Ernest+Lluch+32+Matar%C3%B3','_blank')}"
                                       class="flex-1 bg-white/[0.04] border border-white/10
                                              rounded-full px-5 py-3 font-outfit text-sm text-white
                                              placeholder:text-white/40 focus:outline-none
                                              focus:ring-2 focus:ring-secondary/50
                                              focus:border-secondary/60 focus:bg-white/[0.06]
                                              transition">
                                <button type="button"
                                        @click="if(origin){window.open('https://www.google.com/maps/dir/?api=1&origin='+encodeURIComponent(origin)+'&destination=Carrer+d%27Ernest+Lluch+32+Matar%C3%B3','_blank')}"
                                        class="w-12 h-12 shrink-0 bg-secondary text-white
                                               rounded-full flex items-center justify-center
                                               hover:brightness-110 active:scale-95 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                Exclusive Intelligence
            </span>

            <h2 class="font-alumni italic text-h2 text-white">
                Mantén-te al <span class="text-secondary">Corrent</span>
            </h2>

            <p class="font-outfit text-body-lg text-white/65 mt-5 max-w-xl mx-auto leading-relaxed">
                Rep actualitzacions exclusives sobre noves tecnologies d'impressió,
                ofertes de material d'oficina i inspiració per al teu marxandatge.
            </p>

            <form x-data="{ email: '', sent: false }"
                  @submit.prevent="if(email){sent=true; email=''}"
                  class="mt-10 flex flex-col sm:flex-row gap-3 max-w-2xl mx-auto">

                {{-- Email input — pill, dark, with trailing icon --}}
                <div class="relative flex-1">
                    <input type="email" x-model="email" required
                           :disabled="sent"
                           placeholder="Introdueix el teu correu electrònic"
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
                    <span x-show="!sent">Subscriure's ara</span>
                    <span x-show="sent" x-cloak>✓ Gràcies!</span>
                </button>
            </form>

            <p class="font-outfit text-body-sm text-white/45 mt-6">
                En subscriure't, acceptes la nostra
                <a href="{{ route('privacy') }}" class="underline font-semibold text-white/70
                                   hover:text-white transition-colors">Privadesa</a>.
            </p>
        </div>
    </section>

@endsection
