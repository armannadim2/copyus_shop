@php
    $currentLocale = app()->getLocale();
    $isHome = request()->routeIs('home');
    $localeInfo = [
        'ca' => ['label' => 'CA', 'flag' => '<svg viewBox="0 0 20 14" width="20" height="14" style="border-radius:2px;display:inline-block;vertical-align:middle"><rect width="20" height="14" fill="#FCDD09"/><rect y="0"  width="20" height="2.33" fill="#DA121A"/><rect y="4"  width="20" height="2.33" fill="#DA121A"/><rect y="8"  width="20" height="2.33" fill="#DA121A"/><rect y="11.67" width="20" height="2.33" fill="#DA121A"/></svg>'],
        'es' => ['label' => 'ES', 'flag' => '<svg viewBox="0 0 20 14" width="20" height="14" style="border-radius:2px;display:inline-block;vertical-align:middle"><rect width="20" height="14" fill="#AA151B"/><rect y="3.5" width="20" height="7" fill="#F1BF00"/></svg>'],
        'en' => ['label' => 'EN', 'flag' => '<svg viewBox="0 0 20 14" width="20" height="14" style="border-radius:2px;display:inline-block;vertical-align:middle"><rect width="20" height="14" fill="#012169"/><path d="M0,0 L20,14 M20,0 L0,14" stroke="white" stroke-width="3.5"/><path d="M0,0 L20,14 M20,0 L0,14" stroke="#C8102E" stroke-width="1.8"/><path d="M10,0 V14 M0,7 H20" stroke="white" stroke-width="4.5"/><path d="M10,0 V14 M0,7 H20" stroke="#C8102E" stroke-width="2.8"/></svg>'],
    ];
@endphp

<nav id="main-nav"
     x-data="{
         mobileOpen: false,
         scrolled: false,
         userOpen: false,
         langOpen: false,
         searchOpen: false,
         isHome: {{ $isHome ? 'true' : 'false' }},
         init() {
             this.scrolled = window.scrollY > 50;
             window.addEventListener('scroll', () => {
                 this.scrolled = window.scrollY > 50;
             }, { passive: true });
         },
         get lightMode() { return this.scrolled; },
         openSearch() {
             this.searchOpen = true;
             this.$nextTick(() => document.getElementById('search-overlay-input')?.focus());
         },
         closeSearch() { this.searchOpen = false; },
     }"
     @keydown.escape.window="closeSearch()"
     class="fixed top-0 inset-x-0 z-50 transition-all duration-300 px-4 sm:px-6"
     :class="(scrolled || !isHome) ? 'pt-3' : 'pt-0'">

    {{-- ────────────────────────────────────────────────────────────────────
         Inner container:
           home  + not scrolled  → transparent (full-bleed hero)
           home  + scrolled      → cream glass floating card
           other + not scrolled  → red rounded card
           other + scrolled      → cream glass floating card
    ──────────────────────────────────────────────────────────────────────── --}}
    <div class="max-w-6xl mx-auto transition-all duration-300 px-4 sm:px-6"
         :style="scrolled
             ? 'background:rgba(240,239,235,0.8);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border:1px solid rgba(50,74,95,0.1);border-radius:1rem;box-shadow:0 2px 24px rgba(0,0,0,0.08)'
             : (!isHome ? 'background:rgb(242,96,82);border-radius:1rem' : '')"
         :class="(isHome && !scrolled) ? 'bg-transparent' : ''">

        <div class="flex items-center justify-between" style="height:80px">

            {{-- ── Logo ──────────────────────────────────────────────────── --}}
            <a href="{{ route('home') }}" class="shrink-0">
                <img src="{{ asset('assets/images/logo/' . rawurlencode('FULL LOGO (Red Sun).svg')) }}"
                     alt="Copyus"
                     class="w-auto transition-all duration-300"
                     :style="lightMode ? 'height:64px;filter:none' : 'height:64px;filter:brightness(0) invert(1)'">
            </a>

            {{-- ── Desktop menu ──────────────────────────────────────────── --}}
            <ul class="hidden lg:flex items-center">
                @php
                    $navLinks = [
                        ['label' => 'Inici',       'href' => 'https://copyus.es',               'active' => false],
                        ['label' => 'Qui som',      'href' => 'https://copyus.es/about',         'active' => false],
                        ['label' => 'Serveis',      'href' => 'https://copyus.es/services',      'active' => false],
                        ['label' => 'Botiga',       'href' => route('products.index'),            'active' => request()->routeIs('products.*', 'home')],
                        ['label' => '🖨️ Impressió', 'href' => route('print.index'),              'active' => request()->routeIs('print.*')],
                        ['label' => 'Demanar Preu', 'href' => 'https://copyus.es/request-quote', 'active' => false],
                        ['label' => 'Contacte',     'href' => 'https://copyus.es/contact',       'active' => false],
                    ];
                @endphp

                @foreach($navLinks as $link)
                    <li>
                        <a href="{{ $link['href'] }}"
                           class="flex flex-col items-center px-4 py-1 font-outfit text-sm font-medium
                                  transition-colors duration-200 group"
                           :class="lightMode
                               ? '{{ $link['active'] ? 'text-primary' : 'text-dark/80 hover:text-primary' }}'
                               : '{{ $link['active'] ? 'text-white' : 'text-white/80 hover:text-white' }}'">
                            {{ $link['label'] }}
                            {{-- Active dot indicator --}}
                            <span class="mt-0.5 block w-1 h-1 rounded-full transition-all duration-200
                                         {{ $link['active'] ? 'scale-100' : 'scale-0 group-hover:scale-100 opacity-50' }}"
                                  :class="lightMode ? 'bg-dark' : 'bg-white'"></span>
                        </a>
                    </li>
                @endforeach
            </ul>

            {{-- ── Right actions ────────────────────────────────────────── --}}
            <div class="flex items-center gap-1">

                {{-- Search icon --}}
                <button type="button"
                        @click="openSearch()"
                        class="flex items-center justify-center p-2 rounded-lg transition-colors duration-200"
                        :class="lightMode ? 'text-dark/60 hover:text-primary hover:bg-gray-50' : 'text-white/70 hover:text-white'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                </button>

                @auth
                    @if(auth()->user()->canSeePrices())
                        @php
                            $cartItems  = auth()->user()->cartItems()->where('type','cart')->with('product')->get();
                            $cartCount  = $cartItems->sum('quantity');
                            $cartTotal  = $cartItems->sum(fn($i) => $i->product->price_with_vat * $i->quantity);
                            $quoteItems = auth()->user()->cartItems()->where('type','quote')->with('product')->get();
                            $quoteCount = $quoteItems->count();
                        @endphp

                        {{-- ── Cart dropdown ──────────────────────────────── --}}
                        <div class="relative hidden md:block" x-data="{ cartOpen: false }"
                             @mouseenter="cartOpen = true" @mouseleave="cartOpen = false">

                            <a href="{{ route('cart.index') }}"
                               class="relative flex items-center justify-center p-2 rounded-lg transition-colors duration-200"
                               :class="lightMode ? 'text-dark/60 hover:text-primary hover:bg-gray-50' : 'text-white/70 hover:text-white'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                @if($cartCount > 0)
                                    <span class="absolute top-1 right-1 bg-primary text-white
                                                 text-[9px] font-bold rounded-full w-3.5 h-3.5
                                                 flex items-center justify-center leading-none">
                                        {{ $cartCount > 9 ? '9+' : $cartCount }}
                                    </span>
                                @endif
                            </a>

                            {{-- Cart popup --}}
                            <div x-show="cartOpen"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 translate-y-1"
                                 class="absolute right-0 mt-1 w-80 bg-white rounded-2xl
                                        shadow-[0_8px_32px_rgba(0,0,0,0.14)]
                                        border border-gray-100 z-50 overflow-hidden"
                                 style="display:none">

                                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                    <span class="font-alumni text-h6 text-dark">{{ __('app.cart') }}</span>
                                    @if($cartCount > 0)
                                        <span class="font-outfit text-xs text-gray-400">{{ $cartCount }} {{ __('app.items') }}</span>
                                    @endif
                                </div>

                                @if($cartItems->isEmpty())
                                    <div class="px-4 py-8 text-center">
                                        <svg class="w-10 h-10 mx-auto text-gray-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <p class="font-outfit text-sm text-gray-400">{{ __('app.cart_empty') }}</p>
                                    </div>
                                @else
                                    <ul class="max-h-60 overflow-y-auto divide-y divide-gray-50">
                                        @foreach($cartItems as $item)
                                            <li class="flex items-center gap-3 px-4 py-2.5">
                                                {{-- Thumb --}}
                                                <div class="w-10 h-10 rounded-lg bg-light flex-shrink-0 overflow-hidden">
                                                    @if($item->product->image)
                                                        <img src="{{ asset('storage/'.$item->product->image) }}"
                                                             class="w-full h-full object-cover" alt="">
                                                    @else
                                                        <span class="flex items-center justify-center h-full text-lg">📦</span>
                                                    @endif
                                                </div>
                                                {{-- Info --}}
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-outfit text-xs font-medium text-dark truncate">
                                                        {{ $item->product->getTranslation('name', app()->getLocale()) }}
                                                    </p>
                                                    <p class="font-outfit text-xs text-gray-400">
                                                        {{ $item->quantity }} × {{ number_format($item->product->price_with_vat, 2, ',', '.') }} €
                                                    </p>
                                                </div>
                                                {{-- Line total --}}
                                                <span class="font-alumni text-sm text-primary shrink-0">
                                                    {{ number_format($item->product->price_with_vat * $item->quantity, 2, ',', '.') }} €
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div class="px-4 py-3 border-t border-gray-100 bg-gray-50/60">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="font-outfit text-sm font-medium text-dark">Total</span>
                                            <span class="font-alumni text-h6 text-primary">
                                                {{ number_format($cartTotal, 2, ',', '.') }} €
                                            </span>
                                        </div>
                                        <a href="{{ route('orders.checkout') }}"
                                           class="flex items-center justify-center gap-2 w-full
                                                  bg-primary text-white font-alumni text-sm-header
                                                  py-2.5 rounded-xl hover:brightness-110
                                                  active:scale-95 transition-all duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            {{ __('app.checkout') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- ── Quotation dropdown ──────────────────────────── --}}
                        <div class="relative hidden md:block" x-data="{ quoteOpen: false }"
                             @mouseenter="quoteOpen = true" @mouseleave="quoteOpen = false">

                            <a href="{{ route('quotations.basket') }}"
                               class="relative flex items-center justify-center p-2 rounded-lg transition-colors duration-200"
                               :class="lightMode ? 'text-dark/60 hover:text-primary hover:bg-gray-50' : 'text-white/70 hover:text-white'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                @if($quoteCount > 0)
                                    <span class="absolute top-1 right-1 bg-secondary text-white
                                                 text-[9px] font-bold rounded-full w-3.5 h-3.5
                                                 flex items-center justify-center leading-none">
                                        {{ $quoteCount > 9 ? '9+' : $quoteCount }}
                                    </span>
                                @endif
                            </a>

                            {{-- Quotation popup --}}
                            <div x-show="quoteOpen"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 translate-y-1"
                                 class="absolute right-0 mt-1 w-80 bg-white rounded-2xl
                                        shadow-[0_8px_32px_rgba(0,0,0,0.14)]
                                        border border-gray-100 z-50 overflow-hidden"
                                 style="display:none">

                                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                    <span class="font-alumni text-h6 text-dark">{{ __('app.quotation') }}</span>
                                    @if($quoteCount > 0)
                                        <span class="font-outfit text-xs text-gray-400">{{ $quoteCount }} {{ __('app.products') }}</span>
                                    @endif
                                </div>

                                @if($quoteItems->isEmpty())
                                    <div class="px-4 py-8 text-center">
                                        <svg class="w-10 h-10 mx-auto text-gray-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="font-outfit text-sm text-gray-400">{{ __('app.quote_empty') }}</p>
                                    </div>
                                @else
                                    <ul class="max-h-60 overflow-y-auto divide-y divide-gray-50">
                                        @foreach($quoteItems as $item)
                                            <li class="flex items-center gap-3 px-4 py-2.5">
                                                <div class="w-10 h-10 rounded-lg bg-light flex-shrink-0 overflow-hidden">
                                                    @if($item->product->image)
                                                        <img src="{{ asset('storage/'.$item->product->image) }}"
                                                             class="w-full h-full object-cover" alt="">
                                                    @else
                                                        <span class="flex items-center justify-center h-full text-lg">📦</span>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-outfit text-xs font-medium text-dark truncate">
                                                        {{ $item->product->getTranslation('name', app()->getLocale()) }}
                                                    </p>
                                                    <p class="font-outfit text-xs text-gray-400">
                                                        {{ __('app.qty') }}: {{ $item->quantity }} {{ $item->product->unit }}
                                                    </p>
                                                </div>
                                                <span class="font-outfit text-xs text-secondary shrink-0 font-medium">
                                                    {{ $item->product->sku }}
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div class="px-4 py-3 border-t border-gray-100 bg-gray-50/60">
                                        <p class="font-outfit text-xs text-gray-400 mb-3">
                                            {{ $quoteCount }} {{ __('app.products') }} · {{ __('app.quote_admin_prices') }}
                                        </p>
                                        <form method="POST" action="{{ route('quotations.submit') }}">
                                            @csrf
                                            <button type="submit"
                                                    class="flex items-center justify-center gap-2 w-full
                                                           bg-secondary text-white font-alumni text-sm-header
                                                           py-2.5 rounded-xl hover:brightness-110
                                                           active:scale-95 transition-all duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                </svg>
                                                {{ __('app.send_quotation') }}
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>

                    @endif

                    {{-- User dropdown --}}
                    <div class="relative hidden md:block" x-data="{ userOpen: false }">
                        <button @click="userOpen = !userOpen"
                                class="flex items-center gap-1.5 font-outfit text-sm font-medium
                                       rounded-xl px-3 py-2 transition-all duration-200"
                                :class="lightMode
                                    ? 'text-dark hover:text-primary bg-gray-100 hover:bg-gray-200'
                                    : 'text-white/80 hover:text-white bg-white/10 hover:bg-white/20'">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="max-w-[110px] truncate hidden sm:inline">
                                {{ auth()->user()->company_name ?? auth()->user()->name }}
                            </span>
                            <svg class="w-3 h-3 shrink-0 transition-transform duration-200"
                                 :class="{ 'rotate-180': userOpen }"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="userOpen"
                             @click.away="userOpen = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             class="absolute right-0 mt-2 w-56 bg-white rounded-2xl
                                    shadow-[0_4px_24px_rgba(0,0,0,0.12)]
                                    border border-gray-100 py-2 z-50"
                             style="display:none">
                            <div class="px-4 py-2.5 border-b border-gray-100 mb-1">
                                <p class="font-outfit text-sm text-dark font-semibold truncate">
                                    {{ auth()->user()->name }}
                                </p>
                                <p class="font-outfit text-xs text-gray-400 truncate">
                                    {{ auth()->user()->email }}
                                </p>
                            </div>
                            <a href="{{ route('dashboard') }}" class="dropdown-item">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                {{ __('app.dashboard') }}
                            </a>
                            <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ __('app.profile') }}
                            </a>
                            @if(auth()->user()->canSeePrices())
                                <a href="{{ route('wishlist.index') }}" class="dropdown-item">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                    {{ __('app.wishlist') }}
                                </a>
                                <a href="{{ route('orders.index') }}" class="dropdown-item">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                    {{ __('app.orders') }}
                                </a>
                                <a href="{{ route('quotations.index') }}" class="dropdown-item">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    {{ __('app.quotation') }}
                                </a>
                                <a href="{{ route('invoices.index') }}" class="dropdown-item">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    {{ __('app.invoices') }}
                                </a>
                            @endif
                            @if(auth()->user()->isAdmin())
                                <div class="border-t border-gray-100 mt-1 pt-1">
                                    <a href="{{ route('admin.index') }}" class="dropdown-item text-secondary font-semibold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        Admin Panel
                                    </a>
                                </div>
                            @endif
                            <div class="border-t border-gray-100 mt-1 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item w-full text-red-500 hover:bg-red-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        {{ __('app.logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                @else
                    {{-- Login dropdown --}}
                    <div class="hidden md:block relative" x-data="{ loginOpen: false }">
                        <button @click="loginOpen = !loginOpen"
                                title="{{ __('app.login') }}"
                                class="flex items-center justify-center w-9 h-9 rounded-xl transition-all duration-200"
                                :class="loginOpen
                                    ? 'bg-primary text-white'
                                    : (lightMode ? 'text-dark/70 hover:text-primary hover:bg-gray-100' : 'text-white/80 hover:text-white hover:bg-white/15')">
                            {{-- Login / enter icon --}}
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                        </button>

                        {{-- Login panel --}}
                        <div x-show="loginOpen"
                             @click.away="loginOpen = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                             class="absolute right-0 mt-3 w-80 bg-white rounded-2xl
                                    shadow-[0_8px_32px_rgba(0,0,0,0.14)]
                                    border border-gray-100 p-5 z-50"
                             style="display:none">

                            <p class="font-alumni text-h6 text-dark mb-4">{{ __('app.login') }}</p>

                            {{-- Session / validation errors --}}
                            @if(session('login_error'))
                                <div class="mb-3 bg-red-50 border border-red-200 text-red-600
                                            font-outfit text-body-sm px-3 py-2 rounded-xl">
                                    {{ session('login_error') }}
                                </div>
                            @endif
                            @error('email')
                                <div class="mb-3 bg-red-50 border border-red-200 text-red-600
                                            font-outfit text-body-sm px-3 py-2 rounded-xl">
                                    {{ $message }}
                                </div>
                            @enderror

                            <form method="POST" action="{{ route('login') }}" class="space-y-3">
                                @csrf

                                <div>
                                    <label for="nav_email"
                                           class="block font-outfit text-xs font-medium text-dark/70 mb-1">
                                        {{ __('app.email') }}
                                    </label>
                                    <input type="email"
                                           id="nav_email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           autocomplete="email"
                                           required
                                           placeholder="correu@exemple.com"
                                           class="w-full border border-gray-200 rounded-xl px-3 py-2
                                                  font-outfit text-sm focus:outline-none
                                                  focus:ring-2 focus:ring-primary/40 focus:border-primary
                                                  transition placeholder:text-gray-300" />
                                </div>

                                <div>
                                    <label for="nav_password"
                                           class="block font-outfit text-xs font-medium text-dark/70 mb-1">
                                        {{ __('app.password') }}
                                    </label>
                                    <input type="password"
                                           id="nav_password"
                                           name="password"
                                           autocomplete="current-password"
                                           required
                                           placeholder="••••••••"
                                           class="w-full border border-gray-200 rounded-xl px-3 py-2
                                                  font-outfit text-sm focus:outline-none
                                                  focus:ring-2 focus:ring-primary/40 focus:border-primary
                                                  transition placeholder:text-gray-300" />
                                </div>

                                <div class="flex items-center justify-between pt-0.5">
                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                        <input type="checkbox" name="remember"
                                               class="w-3.5 h-3.5 rounded border-gray-300 text-primary focus:ring-primary" />
                                        <span class="font-outfit text-xs text-gray-400">{{ __('app.remember_me') }}</span>
                                    </label>
                                </div>

                                <button type="submit"
                                        class="w-full bg-primary text-white font-alumni text-sm-header
                                               py-2.5 rounded-xl hover:brightness-110 active:scale-95
                                               transition-all duration-200">
                                    {{ __('app.login') }} →
                                </button>
                            </form>

                            <div class="mt-4 pt-4 border-t border-gray-100 text-center">
                                <span class="font-outfit text-xs text-gray-400">{{ __('app.no_account') }}</span>
                                <a href="{{ route('register') }}"
                                   class="font-outfit text-xs font-semibold text-secondary hover:text-primary transition-colors ml-1">
                                    {{ __('app.register') }} →
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Register icon --}}
                    <a href="{{ route('register') }}"
                       title="{{ __('app.register') }}"
                       class="hidden md:flex items-center justify-center w-9 h-9 rounded-xl
                              transition-all duration-200"
                       :class="lightMode ? 'text-dark/70 hover:text-primary hover:bg-gray-100' : 'text-white/80 hover:text-white hover:bg-white/15'">
                        {{-- User-plus icon --}}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </a>
                @endauth

                {{-- ── Language switcher ────────────────────────────────── --}}
                <div class="hidden md:flex items-center ml-3 relative" x-data="{ langOpen: false }">
                    <button @click="langOpen = !langOpen"
                            class="flex items-center gap-1.5 font-outfit text-sm font-semibold
                                   transition-colors duration-200 py-1"
                            :class="lightMode ? 'text-primary' : 'text-white'">
                        {!! $localeInfo[$currentLocale]['flag'] !!}
                        <span class="ml-0.5">{{ $localeInfo[$currentLocale]['label'] }}</span>
                    </button>
                    <div x-show="langOpen"
                         @click.away="langOpen = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute right-0 top-full mt-2 w-24 bg-white rounded-xl
                                shadow-[0_4px_20px_rgba(0,0,0,0.10)]
                                border border-gray-100 py-1 z-50"
                         style="display:none">
                        @foreach($localeInfo as $code => $info)
                            <a href="{{ route('locale.switch', $code) }}"
                               class="flex items-center gap-2 px-3 py-1.5 font-outfit text-sm
                                      transition-colors {{ $currentLocale === $code ? 'text-primary font-semibold' : 'text-dark hover:text-primary' }}">
                                {!! $info['flag'] !!}
                                <span>{{ $info['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Mobile hamburger --}}
                <button @click="mobileOpen = !mobileOpen"
                        class="lg:hidden p-2 ml-1 rounded-lg transition-colors duration-200"
                        :class="lightMode ? 'text-dark hover:text-primary' : 'text-white/80 hover:text-white'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

            </div>
        </div>
    </div>

    {{-- ── Mobile menu ──────────────────────────────────────────────────── --}}
    <div x-show="mobileOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="lg:hidden mx-4 mt-1 rounded-2xl overflow-hidden
                bg-white shadow-[0_4px_24px_rgba(0,0,0,0.12)]"
         style="display:none">
        <div class="px-2 py-3">

            <a href="https://copyus.es"       class="mobile-menu-item">Inici</a>
            <a href="https://copyus.es/about"  class="mobile-menu-item">Qui som</a>
            <a href="https://copyus.es/services" class="mobile-menu-item">Serveis</a>
            <a href="{{ route('products.index') }}"
               class="mobile-menu-item {{ request()->routeIs('products.*') ? '!text-primary font-semibold' : '' }}">
                Botiga
            </a>
            <a href="https://copyus.es/request-quote" class="mobile-menu-item">Demanar Preu</a>
            <a href="https://copyus.es/contact"       class="mobile-menu-item border-b-0">Contacte</a>

            @auth
                <div class="mt-3 pt-3 border-t border-gray-100 space-y-0">
                    <a href="{{ route('dashboard') }}"   class="mobile-menu-item">{{ __('app.dashboard') }}</a>
                    <a href="{{ route('profile.edit') }}" class="mobile-menu-item">{{ __('app.profile') }}</a>
                    @if(auth()->user()->canSeePrices())
                        <a href="{{ route('orders.index') }}"     class="mobile-menu-item">{{ __('app.orders') }}</a>
                        <a href="{{ route('quotations.index') }}" class="mobile-menu-item">{{ __('app.quotation') }}</a>
                        <a href="{{ route('invoices.index') }}"   class="mobile-menu-item">{{ __('app.invoices') }}</a>
                    @endif
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.index') }}" class="mobile-menu-item text-secondary font-semibold">Admin Panel</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100 mt-2 pt-2">
                        @csrf
                        <button class="mobile-menu-item border-b-0 text-red-500 w-full text-left">{{ __('app.logout') }}</button>
                    </form>
                </div>
            @else
                <div class="mt-3 pt-3 border-t border-gray-100 flex gap-2 px-2">
                    <a href="{{ route('login') }}"
                       class="flex-1 flex items-center justify-center gap-2 font-outfit text-sm font-medium py-2.5 rounded-xl
                              border border-gray-200 text-dark hover:border-primary hover:text-primary transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        {{ __('app.login') }}
                    </a>
                    <a href="{{ route('register') }}"
                       class="flex-1 flex items-center justify-center gap-2 font-outfit text-sm font-semibold py-2.5 rounded-xl
                              bg-primary text-white hover:brightness-110 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        {{ __('app.register') }}
                    </a>
                </div>
            @endauth

            {{-- Language --}}
            <div class="mt-3 pt-3 border-t border-gray-100 flex gap-3 px-2">
                @foreach($localeInfo as $code => $info)
                    <a href="{{ route('locale.switch', $code) }}"
                       class="flex items-center gap-1.5 font-outfit text-sm px-2 py-1 rounded-lg transition-colors
                              {{ $currentLocale === $code ? 'text-primary font-semibold' : 'text-dark/50 hover:text-primary' }}">
                        {!! $info['flag'] !!}
                        <span>{{ $info['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Search overlay ────────────────────────────────────────────────── --}}
    <div x-show="searchOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[60] flex flex-col"
         style="display:none"
         @click.self="closeSearch()">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-dark/60 backdrop-blur-sm"></div>

        {{-- Search panel --}}
        <div x-show="searchOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="relative z-10 w-full bg-white shadow-2xl"
             x-data="navSearch()"
             @click.away="$dispatch('close-search-noop')">

            <div class="max-w-2xl mx-auto px-4 py-6">

                {{-- Input row --}}
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    <input id="search-overlay-input"
                           type="text"
                           x-model="q"
                           @input.debounce.250ms="search()"
                           @keydown.enter.prevent="submitSearch(); $parent.closeSearch()"
                           @keydown.arrow-down.prevent="moveDown()"
                           @keydown.arrow-up.prevent="moveUp()"
                           placeholder="Cerca per nom, SKU o marca…"
                           autocomplete="off"
                           class="w-full pl-12 pr-14 py-4 rounded-2xl border border-gray-200
                                  font-outfit text-base text-dark placeholder-gray-400
                                  focus:outline-none focus:ring-2 focus:ring-primary/40
                                  focus:border-primary transition-all bg-light">
                    <button type="button"
                            @click="$parent.closeSearch()"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400
                                   hover:text-dark transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Autocomplete results --}}
                <div x-show="results.length > 0"
                     class="mt-2 bg-white rounded-2xl border border-gray-100
                            shadow-[0_8px_32px_rgba(0,0,0,0.10)] overflow-hidden"
                     style="display:none">
                    <template x-for="(r, i) in results" :key="r.url">
                        <a :href="r.url"
                           @click="$parent.closeSearch()"
                           class="flex items-center gap-3 px-4 py-3 hover:bg-light transition-colors"
                           :class="i === activeIdx ? 'bg-light' : ''"
                           @mouseenter="activeIdx = i">
                            <div class="w-12 h-12 rounded-xl bg-light flex-shrink-0 overflow-hidden border border-gray-100">
                                <template x-if="r.image">
                                    <img :src="r.image" :alt="r.label" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!r.image">
                                    <span class="flex items-center justify-center h-full text-xl">📦</span>
                                </template>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-outfit text-sm font-medium text-dark truncate" x-text="r.label"></p>
                                <p class="font-outfit text-xs text-gray-400 mt-0.5"
                                   x-text="r.sku + (r.brand ? ' · ' + r.brand : '')"></p>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </template>
                    <a :href="`{{ route('search') }}?q=${encodeURIComponent(q)}`"
                       @click="$parent.closeSearch()"
                       class="flex items-center gap-2 px-4 py-3 border-t border-gray-100
                              font-outfit text-xs text-primary hover:bg-light transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                        Veure tots els resultats per "<span x-text="q"></span>"
                    </a>
                </div>

                {{-- Empty state hint --}}
                <div x-show="q.length >= 2 && results.length === 0"
                     class="mt-2 px-2 py-3 font-outfit text-sm text-gray-400 text-center"
                     style="display:none">
                    Sense resultats per "<span x-text="q"></span>"
                </div>

                <p class="mt-4 font-outfit text-xs text-gray-400 text-center">
                    Prem <kbd class="px-1.5 py-0.5 bg-gray-100 rounded text-gray-500 font-mono text-[11px]">ESC</kbd>
                    per tancar
                </p>
            </div>
        </div>
    </div>

</nav>
