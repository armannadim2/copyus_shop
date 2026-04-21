@extends('layouts.app')
@section('title', __('app.products'))

@section('content')
{{-- ─── Quick-View Modal (page-level) ──────────────────────────────────────── --}}
<div x-data="{
        show: false,
        p: {},
        open(data) { this.p = data; this.show = true; document.body.style.overflow = 'hidden'; },
        close() { this.show = false; document.body.style.overflow = ''; }
     }"
     @quickview.window="open($event.detail)"
     @keydown.escape.window="close()">

    {{-- Backdrop --}}
    <div x-show="show"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="close()"
         class="fixed inset-0 bg-dark/50 z-50 backdrop-blur-sm"
         style="display:none"></div>

    {{-- Panel --}}
    <div x-show="show"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
         class="fixed inset-x-4 top-[5%] md:inset-x-auto md:left-1/2 md:-translate-x-1/2
                md:w-[680px] max-h-[90vh] overflow-y-auto
                bg-white rounded-2xl shadow-[0_24px_64px_rgba(0,0,0,0.18)] z-50"
         style="display:none">

        {{-- Close --}}
        <button @click="close()"
                class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center
                       rounded-full bg-gray-100 hover:bg-gray-200 text-dark/60
                       hover:text-dark transition-colors z-10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <div class="grid md:grid-cols-2 gap-0">
            {{-- Image --}}
            <div class="bg-light rounded-t-2xl md:rounded-l-2xl md:rounded-tr-none
                        flex items-center justify-center min-h-56 overflow-hidden">
                <template x-if="p.image">
                    <img :src="p.image" :alt="p.name"
                         class="w-full h-full object-cover max-h-72 md:max-h-none">
                </template>
                <template x-if="!p.image">
                    <span class="text-7xl">📦</span>
                </template>
            </div>

            {{-- Details --}}
            <div class="p-6 flex flex-col gap-3">
                <span class="inline-block text-xs font-outfit font-medium text-secondary
                             bg-secondary/10 px-2.5 py-0.5 rounded-full w-fit"
                      x-text="p.category"></span>

                <h2 class="font-alumni text-h5 text-dark leading-tight" x-text="p.name"></h2>

                <p class="font-outfit text-sm text-gray-500 leading-relaxed line-clamp-4"
                   x-text="p.description"></p>

                <div class="grid grid-cols-2 gap-2 bg-light rounded-xl p-3 text-xs font-outfit">
                    <div>
                        <span class="text-gray-400 block">SKU</span>
                        <span class="text-dark font-medium" x-text="p.sku"></span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">{{ __('app.brand') }}</span>
                        <span class="text-dark font-medium" x-text="p.brand"></span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">{{ __('app.min_order') }}</span>
                        <span class="text-dark font-medium" x-text="p.min_qty + ' ' + p.unit"></span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">{{ __('app.stock') }}</span>
                        <template x-if="p.stock > 0">
                            <span class="text-green-600 font-medium">{{ __('app.in_stock') }}</span>
                        </template>
                        <template x-if="p.stock <= 0">
                            <span class="text-red-500 font-medium">{{ __('app.out_of_stock') }}</span>
                        </template>
                    </div>
                </div>

                @auth
                    @if(auth()->user()->canSeePrices())
                        <div x-show="p.price">
                            <p class="font-alumni text-h4 text-primary" x-text="p.price_vat + ' €'"></p>
                            <p class="font-outfit text-xs text-gray-400" x-text="'{{ __('app.price_without_vat') }}: ' + p.price + ' €'"></p>
                        </div>
                    @endif
                @endauth

                <div class="flex gap-2 mt-auto pt-2">
                    <a :href="p.url"
                       class="flex-1 flex items-center justify-center gap-2 border-2 border-dark
                              text-dark font-alumni text-sm-header py-2.5 rounded-xl
                              hover:border-primary hover:text-primary transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        {{ __('app.view') }}
                    </a>

                    @auth
                        @if(auth()->user()->canSeePrices())
                            {{-- Add to quotation from modal --}}
                            <form method="POST" action="{{ route('quotations.add') }}">
                                @csrf
                                <input type="hidden" name="product_id" :value="p.id" />
                                <input type="hidden" name="quantity" :value="p.min_qty" />
                                <button type="submit"
                                        title="{{ __('app.add_to_quotation') }}"
                                        class="flex items-center justify-center w-11 h-11 rounded-xl
                                               border-2 border-secondary text-secondary
                                               hover:bg-secondary hover:text-white transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </button>
                            </form>

                            {{-- Add to cart from modal --}}
                            <template x-if="p.stock > 0">
                                <form method="POST" action="{{ route('cart.add') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" :value="p.id" />
                                    <input type="hidden" name="quantity" :value="p.min_qty" />
                                    <button type="submit"
                                            title="{{ __('app.add_to_cart') }}"
                                            class="flex items-center justify-center w-11 h-11 rounded-xl
                                                   bg-primary text-white hover:brightness-110
                                                   active:scale-95 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </button>
                                </form>
                            </template>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ─── Page Body ───────────────────────────────────────────────────────────── --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="font-alumni text-h2 text-dark">{{ __('app.products') }}</h1>
        @if(isset($category))
            <p class="font-outfit text-body-lg text-gray-500 mt-1">
                {{ $category->getTranslation('name', app()->getLocale()) }}
            </p>
        @endif
    </div>

    <div class="flex flex-col lg:flex-row gap-8">

        {{-- Sidebar: Categories --}}
        <aside class="lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-28">
                <h2 class="font-alumni text-h6 text-dark mb-4">
                    {{ __('app.all_categories') }}
                </h2>
                <ul class="space-y-1">
                    {{-- All categories --}}
                    <li>
                        <a href="{{ route('products.index') }}"
                           class="flex items-center justify-between font-outfit text-body-lg
                                  rounded-lg px-3 py-2 transition-colors
                                  {{ !request('category')
                                     ? 'bg-primary text-white'
                                     : 'text-dark hover:bg-light hover:text-primary' }}">
                            <span>{{ __('app.all_categories') }}</span>
                        </a>
                    </li>

                    @foreach($categories as $cat)
                        @php
                            $childSlugs   = $cat->children->pluck('slug');
                            $isActive     = request('category') === $cat->slug
                                         || $childSlugs->contains(request('category'));
                            $hasChildren  = $cat->children->isNotEmpty();
                        @endphp
                        <li x-data="{ open: false }"
                            @mouseenter="open = true"
                            @mouseleave="open = false"
                            class="relative">

                            <a href="{{ route('products.index', ['category' => $cat->slug]) }}"
                               class="flex items-center justify-between font-outfit text-body-lg
                                      rounded-lg px-3 py-2 transition-colors
                                      {{ $isActive
                                         ? 'bg-primary text-white'
                                         : 'text-dark hover:bg-light hover:text-primary' }}">
                                <span>{{ $cat->getTranslation('name', app()->getLocale()) }}</span>
                                <div class="flex items-center gap-1 shrink-0">
                                    <span class="text-body-sm opacity-60">({{ $cat->products_count }})</span>
                                    @if($hasChildren)
                                        <svg class="w-3 h-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    @endif
                                </div>
                            </a>

                            {{-- Subcategory flyout --}}
                            @if($hasChildren)
                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 translate-x-1"
                                     x-transition:enter-end="opacity-100 translate-x-0"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100 translate-x-0"
                                     x-transition:leave-end="opacity-0 translate-x-1"
                                     class="absolute left-full top-0 ml-2 w-52 bg-white rounded-2xl
                                            shadow-[0_8px_24px_rgba(0,0,0,0.10)] border border-gray-100
                                            py-2 z-50"
                                     style="display:none">
                                    @foreach($cat->children as $child)
                                        <a href="{{ route('products.index', ['category' => $child->slug]) }}"
                                           class="flex items-center justify-between px-4 py-2
                                                  font-outfit text-body-lg transition-colors
                                                  {{ request('category') === $child->slug
                                                     ? 'text-primary bg-light font-medium'
                                                     : 'text-dark hover:bg-light hover:text-primary' }}">
                                            <span>{{ $child->getTranslation('name', app()->getLocale()) }}</span>
                                            <span class="text-body-sm opacity-50 shrink-0">{{ $child->products_count }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>

                {{-- Filters --}}
                @php
                    $activeBrands       = (array) request('brand', []);
                    $hasActiveFilters   = request()->hasAny(['in_stock','price_min','price_max','brand','seasonal']);
                    $minPlaceholder     = $priceRange?->min_price ? number_format($priceRange->min_price, 2, '.', '') : '0';
                    $maxPlaceholder     = $priceRange?->max_price ? number_format($priceRange->max_price, 2, '.', '') : '—';
                @endphp

                <form method="GET" action="{{ route('products.index') }}" class="mt-6 space-y-5" id="filter-form">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}" />
                    @endif
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}" />
                    @endif

                    {{-- ── In Stock ───────────────────────────────── --}}
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="in_stock" id="in_stock"
                               value="1" class="w-4 h-4 accent-primary"
                               {{ request('in_stock') ? 'checked' : '' }}
                               onchange="this.form.submit()" />
                        <label for="in_stock" class="font-outfit text-sm text-dark cursor-pointer">
                            {{ __('app.in_stock') }}
                        </label>
                    </div>

                    {{-- ── Seasonal ────────────────────────────────── --}}
                    @if($hasSeasonalProducts)
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="seasonal" id="seasonal"
                                   value="1" class="w-4 h-4 accent-primary"
                                   {{ request('seasonal') ? 'checked' : '' }}
                                   onchange="this.form.submit()" />
                            <label for="seasonal" class="font-outfit text-sm text-dark cursor-pointer flex items-center gap-1.5">
                                <span>🌸</span> Temporada
                            </label>
                        </div>
                    @endif

                    {{-- ── Brand ──────────────────────────────────── --}}
                    @if($availableBrands->isNotEmpty())
                        <div x-data="{ open: {{ count($activeBrands) > 0 ? 'true' : 'false' }} }">
                            <button type="button"
                                    @click="open = !open"
                                    class="flex items-center justify-between w-full font-outfit text-xs
                                           font-semibold tracking-widest text-primary uppercase mb-2 group">
                                <span>{{ __('app.brand') }}</span>
                                <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200"
                                     :class="open ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="space-y-1.5 max-h-48 overflow-y-auto pr-1"
                                 style="display:none">
                                @foreach($availableBrands as $brandName => $cnt)
                                    <label class="flex items-center justify-between gap-2 cursor-pointer
                                                  rounded-lg px-2 py-1 hover:bg-light transition-colors group">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox"
                                                   name="brand[]"
                                                   value="{{ $brandName }}"
                                                   class="w-3.5 h-3.5 accent-primary rounded flex-shrink-0"
                                                   {{ in_array($brandName, $activeBrands) ? 'checked' : '' }}
                                                   onchange="this.form.submit()">
                                            <span class="font-outfit text-sm text-dark group-hover:text-primary transition-colors truncate">
                                                {{ $brandName }}
                                            </span>
                                        </div>
                                        <span class="font-outfit text-xs text-gray-400 flex-shrink-0">{{ $cnt }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ── Price Range ─────────────────────────────── --}}
                    <div>
                        <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-2">
                            Preu (€)
                        </p>
                        <div class="flex items-center gap-2">
                            <input type="number" name="price_min" min="0" step="0.01"
                                   value="{{ request('price_min') }}"
                                   placeholder="{{ $minPlaceholder }}"
                                   class="w-full border border-gray-200 rounded-lg px-2 py-1.5
                                          font-outfit text-xs focus:outline-none focus:ring-1
                                          focus:ring-primary transition-colors">
                            <span class="text-gray-300 text-xs flex-shrink-0">–</span>
                            <input type="number" name="price_max" min="0" step="0.01"
                                   value="{{ request('price_max') }}"
                                   placeholder="{{ $maxPlaceholder }}"
                                   class="w-full border border-gray-200 rounded-lg px-2 py-1.5
                                          font-outfit text-xs focus:outline-none focus:ring-1
                                          focus:ring-primary transition-colors">
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full bg-primary text-white font-outfit text-xs font-semibold
                                   py-2.5 rounded-xl hover:brightness-110 active:scale-95 transition-all">
                        Aplicar filtres
                    </button>

                    @if($hasActiveFilters)
                        <a href="{{ route('products.index', array_filter(['category' => request('category'), 'search' => request('search')])) }}"
                           class="flex items-center justify-center gap-1 font-outfit text-xs
                                  text-gray-400 hover:text-primary transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Esborrar filtres
                        </a>
                    @endif
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1">

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('products.index') }}" class="flex gap-3 mb-6">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}" />
                @endif
                <input type="text" name="search"
                       value="{{ request('search') }}"
                       placeholder="{{ __('app.search') }}..."
                       class="flex-1 border border-gray-300 rounded-lg px-4 py-2
                              font-outfit text-body-lg focus:outline-none
                              focus:ring-2 focus:ring-primary focus:border-transparent" />
                <button type="submit"
                        class="bg-primary text-white font-alumni text-sm-header
                               px-6 py-2 rounded-lg hover:brightness-110 transition-all">
                    {{ __('app.search') }}
                </button>
            </form>

            {{-- Product Grid --}}
            @if($products->isEmpty())
                <div class="text-center py-20">
                    <p class="text-5xl mb-4">📦</p>
                    <p class="font-alumni text-h5 text-dark">
                        {{ __('app.no_products_found') }}
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        @php
                            $locale     = app()->getLocale();
                            $name       = $product->getTranslation('name', $locale);
                            $desc       = $product->getTranslation('short_description', $locale) ?? '';
                            $category   = $product->category->getTranslation('name', $locale);
                            $imageUrl   = $product->image ? asset('storage/' . $product->image) : null;
                            $productUrl = route('products.show', $product->slug);
                        @endphp

                        <div x-data class="bg-white rounded-2xl shadow-sm hover:shadow-md
                                    transition-shadow overflow-hidden group flex flex-col">

                            {{-- Product Image --}}
                            <div class="relative h-44 bg-light flex items-center justify-center overflow-hidden">
                                @if($product->image)
                                    <img src="{{ $imageUrl }}"
                                         alt="{{ $name }}"
                                         class="w-full h-full object-cover
                                                group-hover:scale-105 transition-transform duration-300" />
                                @else
                                    <span class="text-5xl">📦</span>
                                @endif

                                {{-- Quick View overlay button --}}
                                <button
                                    @click="$dispatch('quickview', {
                                        id:          {{ $product->id }},
                                        name:        {{ Js::from($name) }},
                                        description: {{ Js::from($desc) }},
                                        category:    {{ Js::from($category) }},
                                        sku:         {{ Js::from($product->sku) }},
                                        brand:       {{ Js::from($product->brand) }},
                                        image:       {{ Js::from($imageUrl) }},
                                        url:         {{ Js::from($productUrl) }},
                                        stock:       {{ $product->stock }},
                                        min_qty:     {{ $product->min_order_quantity }},
                                        unit:        {{ Js::from($product->unit) }},
                                        price:       {{ Js::from(number_format($product->price, 2, '.', '')) }},
                                        price_vat:   {{ Js::from(number_format($product->price_with_vat, 2, ',', '.')) }}
                                    })"
                                    title="{{ __('app.quick_view') }}"
                                    class="absolute inset-0 w-full h-full flex items-center justify-center
                                           bg-dark/0 hover:bg-dark/30 transition-all duration-200
                                           opacity-0 group-hover:opacity-100">
                                    <span class="flex items-center gap-1.5 bg-white/90 text-dark
                                                 font-outfit text-xs font-medium px-3 py-1.5 rounded-full
                                                 shadow-sm backdrop-blur-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        {{ __('app.quick_view') }}
                                    </span>
                                </button>
                            </div>

                            {{-- Product Info --}}
                            <div class="p-5 flex flex-col flex-1">
                                {{-- Category Badge --}}
                                <span class="inline-block bg-light text-secondary font-alumni
                                             text-body-sm px-2 py-0.5 rounded-full mb-2 w-fit">
                                    {{ $category }}
                                </span>

                                {{-- Name --}}
                                <h3 class="font-alumni text-h6 text-dark leading-tight mb-1 line-clamp-2">
                                    {{ $name }}
                                </h3>

                                {{-- SKU & Brand --}}
                                <p class="font-outfit text-body-sm text-gray-400 mb-3">
                                    {{ $product->brand }} · {{ $product->sku }}
                                </p>

                                {{-- Stock Badge --}}
                                <div class="mb-3">
                                    @if($product->stock > 0)
                                        <span class="inline-flex items-center gap-1 font-outfit
                                                     text-body-sm text-green-700 bg-green-50
                                                     px-2 py-0.5 rounded-full">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ __('app.in_stock') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 font-outfit
                                                     text-body-sm text-red-600 bg-red-50
                                                     px-2 py-0.5 rounded-full">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ __('app.out_of_stock') }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Price --}}
                                <div class="mb-4 flex-1">
                                    @auth
                                        @if(auth()->user()->canSeePrices())
                                            <p class="font-alumni text-h5 text-primary">
                                                {{ number_format($product->price_with_vat, 2, ',', '.') }} €
                                            </p>
                                            <p class="font-outfit text-body-sm text-gray-400">
                                                {{ __('app.price_without_vat') }}:
                                                {{ number_format($product->price, 2, ',', '.') }} €
                                            </p>
                                            <p class="font-outfit text-body-sm text-gray-400">
                                                {{ __('app.min_order') }}: {{ $product->min_order_quantity }} {{ $product->unit }}
                                            </p>
                                        @endif
                                    @else
                                        <div class="bg-light rounded-lg px-3 py-2">
                                            <p class="font-outfit text-body-sm text-gray-500 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                </svg>
                                                {{ __('app.register_to_see_price') }}
                                            </p>
                                        </div>
                                    @endauth
                                </div>

                                {{-- Actions: View · Quotation · Cart --}}
                                <div class="flex gap-2">

                                    {{-- View --}}
                                    <a href="{{ $productUrl }}"
                                       title="{{ __('app.view') }}"
                                       class="flex items-center justify-center gap-1.5 flex-1
                                              border-2 border-dark/20 text-dark font-outfit text-xs font-medium
                                              py-2 rounded-xl hover:border-primary hover:text-primary
                                              transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        {{ __('app.view') }}
                                    </a>

                                    @auth
                                        @php $inWl = isset($wishlistIds) && $wishlistIds->has($product->id); @endphp

                                        {{-- Wishlist --}}
                                        <form method="POST" action="{{ route('wishlist.toggle', $product->id) }}">
                                            @csrf
                                            <button type="submit"
                                                    title="{{ $inWl ? __('app.remove_from_wishlist') : __('app.add_to_wishlist') }}"
                                                    class="flex items-center justify-center w-10 h-10 rounded-xl border-2 transition-all duration-200
                                                           {{ $inWl ? 'border-red-400 text-red-500 bg-red-50' : 'border-gray-200 text-gray-300 hover:border-red-300 hover:text-red-400' }}">
                                                <svg class="w-4 h-4" fill="{{ $inWl ? 'currentColor' : 'none' }}"
                                                     stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                                </svg>
                                            </button>
                                        </form>

                                        @if(auth()->user()->canSeePrices())

                                            {{-- Add to Quotation --}}
                                            <form method="POST" action="{{ route('quotations.add') }}">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}" />
                                                <input type="hidden" name="quantity" value="{{ $product->min_order_quantity }}" />
                                                <button type="submit"
                                                        title="{{ __('app.add_to_quotation') }}"
                                                        class="flex items-center justify-center w-10 h-10 rounded-xl
                                                               border-2 border-secondary/40 text-secondary
                                                               hover:border-secondary hover:bg-secondary hover:text-white
                                                               transition-all duration-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </button>
                                            </form>

                                            {{-- Add to Cart --}}
                                            @if($product->stock > 0)
                                                <form method="POST" action="{{ route('cart.add') }}">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $product->id }}" />
                                                    <input type="hidden" name="quantity" value="{{ $product->min_order_quantity }}" />
                                                    <button type="submit"
                                                            title="{{ __('app.add_to_cart') }}"
                                                            class="flex items-center justify-center w-10 h-10 rounded-xl
                                                                   bg-primary text-white hover:brightness-110
                                                                   active:scale-95 transition-all duration-200">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="flex items-center justify-center w-10 h-10 rounded-xl
                                                             bg-gray-100 text-gray-300 cursor-not-allowed"
                                                      title="{{ __('app.out_of_stock') }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                    </svg>
                                                </span>
                                            @endif

                                        @endif
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-10">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
