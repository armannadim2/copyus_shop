@extends('layouts.app')
@section('title', $product->seo_title)

@push('meta')
@php
    $locale      = app()->getLocale();
    $seoTitle    = $product->seo_title;
    $seoDesc     = $product->seo_description;
    $seoKeywords = $product->getTranslation('meta_keywords', $locale, false);
    $canonical   = route('products.show', $product->slug);
    $ogImage     = $product->image_url ?? asset('images/og-default.png');
@endphp
<meta name="description" content="{{ $seoDesc }}">
@if($seoKeywords)
<meta name="keywords" content="{{ $seoKeywords }}">
@endif
<link rel="canonical" href="{{ $canonical }}">

{{-- Open Graph --}}
<meta property="og:type"        content="product">
<meta property="og:url"         content="{{ $canonical }}">
<meta property="og:title"       content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDesc }}">
<meta property="og:image"       content="{{ $ogImage }}">
<meta property="og:locale"      content="{{ str_replace('-', '_', app()->getLocale()) }}">
<meta property="og:site_name"   content="{{ config('app.name', 'Copyus') }}">

{{-- Twitter Card --}}
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:title"       content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDesc }}">
<meta name="twitter:image"       content="{{ $ogImage }}">

{{-- JSON-LD Product structured data --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Product",
    "name": {{ Js::from($product->getTranslation('name', $locale)) }},
    "description": {{ Js::from($seoDesc) }},
    "sku": {{ Js::from($product->sku) }},
    "brand": {
        "@@type": "Brand",
        "name": {{ Js::from($product->brand ?? config('app.name', 'Copyus')) }}
    },
    "image": {{ Js::from($ogImage) }},
    "url": {{ Js::from($canonical) }},
    "offers": {
        "@@type": "Offer",
        "priceCurrency": "EUR",
        "price": "{{ number_format($product->price_with_vat, 2, '.', '') }}",
        "availability": "https://schema.org/{{ $product->stock > 0 ? 'InStock' : 'OutOfStock' }}",
        "url": {{ Js::from($canonical) }}
    }
}
</script>
@endpush

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-20">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 font-outfit text-xs text-gray-400 mb-10">
        <a href="{{ route('products.index') }}" class="hover:text-primary transition-colors">
            {{ __('app.products') }}
        </a>
        <span>/</span>
        <a href="{{ route('products.index', ['category' => $product->category->slug]) }}"
           class="hover:text-primary transition-colors">
            {{ $product->category->getTranslation('name', app()->getLocale()) }}
        </a>
        <span>/</span>
        <span class="text-dark">{{ $product->getTranslation('name', app()->getLocale()) }}</span>
    </nav>

    @php
        $allImages = $product->all_images;
        $variantImagesMap = $product->variants
            ->where('is_active', true)
            ->filter(fn($v) => $v->image)
            ->mapWithKeys(fn($v) => [$v->id => asset('storage/' . $v->image)])
            ->toArray();
    @endphp
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-12"
         x-data="productPage()">

        {{-- ── Image Gallery ─────────────────────────────────────────── --}}
        <div class="space-y-3">
            {{-- Main image --}}
            <div class="bg-light rounded-3xl overflow-hidden aspect-square">
                <img x-show="displayUrl"
                     :src="displayUrl"
                     :alt="displayAlt"
                     class="w-full h-full object-cover">
                <div x-show="!displayUrl"
                     class="w-full h-full flex items-center justify-center text-8xl">📦</div>
            </div>

            {{-- Thumbnails (only if more than 1) --}}
            @if($allImages->count() > 1)
                <div class="flex gap-2 overflow-x-auto pb-1">
                    @foreach($allImages as $i => $img)
                        <button type="button"
                                @click="setGalleryImage({{ $i }})"
                                :class="activeIndex === {{ $i }} && !variantImageUrl ? 'ring-2 ring-primary' : 'ring-1 ring-gray-200'"
                                class="w-16 h-16 flex-shrink-0 bg-light rounded-xl overflow-hidden transition-all">
                            <img src="{{ $img->url }}" alt="{{ $img->alt }}"
                                 class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ── Product Details ───────────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Category + tags --}}
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-block bg-light text-secondary font-outfit text-xs font-semibold
                             tracking-widest uppercase px-3 py-1.5 rounded-full">
                    {{ $product->category->getTranslation('name', app()->getLocale()) }}
                </span>
                @foreach($product->tags as $tag)
                    <span class="inline-block bg-light text-gray-500 font-outfit text-xs
                                 px-2.5 py-1 rounded-full">
                        {{ $tag->name }}
                    </span>
                @endforeach
            </div>

            {{-- Name --}}
            <h1 class="font-alumni text-h2 text-dark leading-tight">
                {{ $product->getTranslation('name', app()->getLocale()) }}
            </h1>

            {{-- Short Description --}}
            @if($product->short_description)
                <p class="font-outfit text-sm text-gray-500 leading-relaxed">
                    {{ $product->getTranslation('short_description', app()->getLocale()) }}
                </p>
            @endif

            {{-- Meta grid --}}
            <div class="grid grid-cols-2 gap-3 bg-light rounded-2xl p-5">
                <div>
                    <p class="font-outfit text-xs text-gray-400 mb-0.5">{{ __('app.sku') }}</p>
                    <p class="font-alumni text-sm-header text-dark">{{ $product->sku }}</p>
                </div>
                <div>
                    <p class="font-outfit text-xs text-gray-400 mb-0.5">{{ __('app.brand') }}</p>
                    <p class="font-alumni text-sm-header text-dark">{{ $product->brand ?? '—' }}</p>
                </div>
                <div>
                    <p class="font-outfit text-xs text-gray-400 mb-0.5">{{ __('app.unit') }}</p>
                    <p class="font-alumni text-sm-header text-dark">{{ $product->unit }}</p>
                </div>
                <div>
                    <p class="font-outfit text-xs text-gray-400 mb-0.5">{{ __('app.status') }}</p>
                    @if($product->stock === 0)
                        <p class="font-alumni text-sm-header text-red-500">{{ __('app.out_of_stock') }}</p>
                    @elseif($product->is_low_stock)
                        <p class="font-alumni text-sm-header text-orange-500">⚠️ {{ __('app.low_stock') }}</p>
                    @else
                        <p class="font-alumni text-sm-header text-green-600">{{ __('app.in_stock') }}</p>
                    @endif
                </div>
            </div>

            {{-- ── Variants ──────────────────────────────────────────── --}}
            @php $variantTypes = $product->variant_types; @endphp
            @if(!empty($variantTypes))
                <div class="space-y-4">
                    @foreach($variantTypes as $type => $options)
                        <div>
                            <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-2">
                                {{ ucfirst($type) }}
                            </p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($options as $variantId => $value)
                                    <button type="button"
                                            @click="selectVariant('{{ $type }}', {{ $variantId }})"
                                            :class="selected['{{ $type }}'] === {{ $variantId }}
                                                ? 'bg-primary text-white border-primary'
                                                : 'bg-white text-dark border-gray-200 hover:border-primary hover:text-primary'"
                                            class="font-outfit text-xs px-4 py-2 rounded-xl border-2 transition-all">
                                        {{ $value }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    {{-- Hidden fields to submit selected variant --}}
                    <template x-for="[type, id] in Object.entries(selected)" :key="type">
                        <input type="hidden" :name="`variant[${type}]`" :value="id" class="variant-hidden">
                    </template>
                </div>
            @endif

            {{-- ── Price & Actions ───────────────────────────────────── --}}
            @auth
                @if(auth()->user()->canSeePrices())
                    <div class="bg-white rounded-3xl shadow-sm p-6 space-y-4">

                        {{-- User-specific tier highlight --}}
                        @if($userTier)
                            <div class="bg-secondary/10 border border-secondary/30 rounded-2xl px-4 py-3 flex items-center justify-between">
                                <div>
                                    <p class="font-outfit text-xs text-secondary font-semibold">
                                        {{ $userTier->label ?? __('app.your_price') }}
                                    </p>
                                    <p class="font-outfit text-xs text-gray-500">{{ __('app.price_tier_exclusive') }}</p>
                                </div>
                                <p class="font-alumni text-h4 text-secondary">
                                    {{ number_format($userTier->price * (1 + $product->vat_rate / 100), 2, ',', '.') }} €
                                </p>
                            </div>
                        @endif

                        {{-- Base price --}}
                        <div>
                            <div class="flex items-end gap-2 mb-1">
                                <p class="font-alumni text-h2 text-primary">
                                    {{ number_format($product->price_with_vat, 2, ',', '.') }} €
                                </p>
                                <p class="font-outfit text-xs text-gray-400 mb-2">/ {{ $product->unit }}</p>
                            </div>
                            <p class="font-outfit text-xs text-gray-400">
                                {{ __('app.price_without_vat') }}:
                                {{ number_format($product->price, 2, ',', '.') }} €
                                · IVA {{ $product->vat_rate }}%:
                                {{ number_format($product->vat_amount, 2, ',', '.') }} €
                            </p>
                            <p class="font-outfit text-xs text-gray-400 mt-1">
                                {{ __('app.min_order') }}: {{ $product->min_order_quantity }} {{ $product->unit }}
                            </p>
                        </div>

                        {{-- Volume price tiers --}}
                        @if($priceTiers->isNotEmpty())
                            <div class="border-t border-gray-100 pt-4">
                                <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-3">
                                    {{ __('app.volume_pricing') }}
                                </p>
                                <div class="space-y-1.5">
                                    @foreach($priceTiers as $tier)
                                        <div class="flex justify-between items-center">
                                            <span class="font-outfit text-xs text-gray-500">
                                                {{ $tier->label ? $tier->label . ' · ' : '' }}
                                                {{ __('app.from') }} {{ $tier->min_quantity }} {{ $product->unit }}
                                                @if($tier->valid_until)
                                                    <span class="text-gray-400">· {{ __('app.until') }} {{ $tier->valid_until->format('d/m/Y') }}</span>
                                                @endif
                                            </span>
                                            <span class="font-alumni text-h6 text-dark">
                                                {{ number_format($tier->price * (1 + $product->vat_rate / 100), 2, ',', '.') }} €
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Wishlist toggle --}}
                        <div class="flex justify-end">
                            <form method="POST" action="{{ route('wishlist.toggle', $product->id) }}">
                                @csrf
                                <button type="submit"
                                        class="flex items-center gap-2 font-outfit text-sm transition-all
                                               {{ $inWishlist ? 'text-red-500' : 'text-gray-400 hover:text-red-400' }}">
                                    <svg class="w-5 h-5" fill="{{ $inWishlist ? 'currentColor' : 'none' }}"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                    {{ $inWishlist ? __('app.remove_from_wishlist') : __('app.add_to_wishlist') }}
                                </button>
                            </form>
                        </div>

                        @if($product->stock > 0)
                            {{-- Add to Cart --}}
                            <form method="POST" action="{{ route('cart.add') }}" class="flex gap-3">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}" />
                                <input type="number" name="quantity"
                                       value="{{ $product->min_order_quantity }}"
                                       min="{{ $product->min_order_quantity }}"
                                       class="w-24 bg-light border-0 rounded-2xl px-4 py-3
                                              font-outfit text-sm text-dark text-center
                                              focus:outline-none focus:ring-2 focus:ring-primary/30 transition"
                                       id="qty-input" />
                                <button type="submit"
                                        class="flex-1 flex items-center justify-center gap-2
                                               bg-primary text-white font-alumni text-sm-header
                                               py-3 rounded-2xl hover:brightness-110 active:scale-95 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    {{ __('app.add_to_cart') }}
                                </button>
                            </form>

                            {{-- Add to Quote --}}
                            <form method="POST" action="{{ route('quotations.add') }}">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}" />
                                <input type="hidden" name="quantity" value="{{ $product->min_order_quantity }}" />
                                <button type="submit"
                                        class="w-full flex items-center justify-center gap-2
                                               border-2 border-secondary text-secondary font-alumni text-sm-header
                                               py-3 rounded-2xl hover:bg-secondary hover:text-white transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    {{ __('app.add_to_quote') }}
                                </button>
                            </form>
                        @else
                            <div class="bg-red-50 border border-red-100 rounded-2xl px-5 py-4 text-center">
                                <p class="font-outfit text-xs text-red-500">{{ __('app.out_of_stock') }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            @else
                <div class="bg-light rounded-3xl p-8 text-center">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <p class="font-alumni text-h6 text-dark mb-1">{{ __('app.register_to_see_price') }}</p>
                    <p class="font-outfit text-xs text-gray-400 mb-5">{{ __('app.register_to_buy_hint') }}</p>
                    <div class="flex gap-3 justify-center">
                        <a href="{{ route('login') }}"
                           class="border-2 border-dark text-dark font-alumni text-sm-header
                                  px-5 py-2.5 rounded-2xl hover:border-primary hover:text-primary transition-all">
                            {{ __('app.login') }}
                        </a>
                        <a href="{{ route('register') }}"
                           class="bg-primary text-white font-alumni text-sm-header
                                  px-5 py-2.5 rounded-2xl hover:brightness-110 transition-all">
                            {{ __('app.register') }}
                        </a>
                    </div>
                </div>
            @endauth
        </div>
    </div>

    {{-- Full Description --}}
    @if($product->description)
        <div class="bg-white rounded-3xl shadow-sm p-8 mb-10">
            <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-5">
                {{ __('app.product_description') }}
            </p>
            <div class="font-outfit text-sm text-gray-600 leading-relaxed">
                {!! nl2br(e($product->getTranslation('description', app()->getLocale()))) !!}
            </div>
        </div>
    @endif

    {{-- ── Reviews ────────────────────────────────────────────────── --}}
    <div id="reviews" class="mb-10">
        <div class="flex items-center justify-between mb-6">
            <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase">
                {{ __('app.reviews') }}
                @if($product->review_count > 0)
                    <span class="ml-2 text-gray-400 font-normal normal-case tracking-normal">
                        ({{ $product->review_count }})
                    </span>
                @endif
            </p>
            @if($product->average_rating)
                <div class="flex items-center gap-2">
                    <div class="flex gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= round($product->average_rating) ? 'text-yellow-400' : 'text-gray-200' }}"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="font-alumni text-h6 text-dark">{{ $product->average_rating }}</span>
                </div>
            @endif
        </div>

        {{-- Existing reviews --}}
        @php $approvedReviews = $product->approvedReviews()->with('user')->get(); @endphp

        @if($approvedReviews->isEmpty())
            <div class="bg-white rounded-3xl shadow-sm p-10 text-center mb-6">
                <p class="font-outfit text-sm text-gray-400">{{ __('app.no_reviews_yet') }}</p>
            </div>
        @else
            <div class="space-y-4 mb-8">
                @foreach($approvedReviews as $review)
                    <div class="bg-white rounded-3xl shadow-sm p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center flex-wrap gap-3 mb-2">
                                    <div class="flex gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}"
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    @if($review->is_verified_purchase)
                                        <span class="font-outfit text-xs text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-full">
                                            ✓ {{ __('app.verified_purchase') }}
                                        </span>
                                    @endif
                                </div>

                                @if($review->title)
                                    <h4 class="font-alumni text-h6 text-dark mb-1">{{ $review->title }}</h4>
                                @endif
                                <p class="font-outfit text-sm text-gray-600 leading-relaxed mb-3">{{ $review->body }}</p>

                                @if($review->photos)
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        @foreach($review->photos as $photo)
                                            <a href="{{ asset('storage/' . $photo) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $photo) }}"
                                                     alt="foto"
                                                     class="w-16 h-16 object-cover rounded-xl border border-gray-100 hover:opacity-80 transition-opacity">
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                <p class="font-outfit text-xs text-gray-400">
                                    {{ $review->user->name }}
                                    @if($review->user->company_name) · {{ $review->user->company_name }} @endif
                                    · {{ $review->created_at->format('d/m/Y') }}
                                </p>
                            </div>

                            {{-- Own review: delete button --}}
                            @auth
                                @if($review->user_id === Auth::id())
                                    <form method="POST" action="{{ route('reviews.destroy', $review->id) }}"
                                          onsubmit="return confirm('{{ __('app.confirm_delete_review') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="text-gray-300 hover:text-red-400 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Submit review form --}}
        @auth
            @php
                $userReview = $product->reviews()->where('user_id', Auth::id())->first();
                // Find a delivered order containing this product
                $deliveredOrder = Auth::user()->orders()
                    ->where('status', 'delivered')
                    ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
                    ->first();
            @endphp

            @if(!$userReview)
                <div class="bg-white rounded-3xl shadow-sm p-6">
                    <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-5">
                        {{ __('app.write_a_review') }}
                    </p>

                    @if(session('success') && str_contains(session('success'), 'ressenya'))
                        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 font-outfit text-sm px-4 py-3 rounded-2xl">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-600 font-outfit text-sm px-4 py-3 rounded-2xl">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-600 font-outfit text-sm px-4 py-3 rounded-2xl">
                            <ul class="space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('reviews.store', $product->slug) }}"
                          enctype="multipart/form-data"
                          x-data="{ rating: 0, hover: 0 }">
                        @csrf

                        @if($deliveredOrder)
                            <input type="hidden" name="order_id" value="{{ $deliveredOrder->id }}">
                        @endif

                        {{-- Star rating --}}
                        <div class="mb-4">
                            <label class="block font-outfit text-xs font-medium text-gray-600 mb-2">
                                {{ __('app.rating') }} *
                            </label>
                            <div class="flex gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button"
                                            @click="rating = {{ $i }}"
                                            @mouseenter="hover = {{ $i }}"
                                            @mouseleave="hover = 0"
                                            class="transition-colors">
                                        <svg class="w-7 h-7"
                                             :class="(hover || rating) >= {{ $i }} ? 'text-yellow-400' : 'text-gray-200'"
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" :value="rating">
                        </div>

                        {{-- Title --}}
                        <div class="mb-4">
                            <input type="text" name="title"
                                   value="{{ old('title') }}"
                                   placeholder="{{ __('app.review_title_placeholder') }}"
                                   maxlength="120"
                                   class="w-full border border-gray-200 rounded-2xl px-4 py-3
                                          font-outfit text-sm focus:outline-none focus:ring-2
                                          focus:ring-primary/40 focus:border-primary transition">
                        </div>

                        {{-- Body --}}
                        <div class="mb-4">
                            <textarea name="body" rows="4" required minlength="10" maxlength="2000"
                                      placeholder="{{ __('app.review_body_placeholder') }}"
                                      class="w-full border border-gray-200 rounded-2xl px-4 py-3
                                             font-outfit text-sm resize-none focus:outline-none
                                             focus:ring-2 focus:ring-primary/40 focus:border-primary transition">{{ old('body') }}</textarea>
                        </div>

                        {{-- Photos --}}
                        <div class="mb-5">
                            <label class="block font-outfit text-xs text-gray-500 mb-2">
                                {{ __('app.review_photos') }} ({{ __('app.optional') }}, màx. 4)
                            </label>
                            <input type="file" name="photos[]" multiple accept="image/*"
                                   class="font-outfit text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                                          file:rounded-xl file:border-0 file:font-outfit file:text-sm
                                          file:bg-light file:text-primary hover:file:bg-primary/10 transition">
                        </div>

                        <button type="submit"
                                :disabled="rating === 0"
                                :class="rating === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:brightness-110'"
                                class="bg-primary text-white font-alumni text-sm-header px-8 py-3
                                       rounded-2xl active:scale-95 transition-all">
                            {{ __('app.submit_review') }}
                        </button>
                    </form>
                </div>
            @else
                <div class="bg-light rounded-3xl p-6 text-center">
                    <p class="font-outfit text-sm text-gray-500">{{ __('app.already_reviewed') }}</p>
                </div>
            @endif
        @else
            <div class="bg-light rounded-3xl p-6 text-center">
                <p class="font-outfit text-sm text-gray-500">
                    <a href="{{ route('login') }}" class="text-primary hover:underline">{{ __('app.login') }}</a>
                    {{ __('app.login_to_review') }}
                </p>
            </div>
        @endauth
    </div>

    {{-- Customers also ordered --}}
    @if($alsoOrdered->isNotEmpty())
        <div class="mb-10">
            <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-6">
                {{ __('app.customers_also_ordered') }}
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($alsoOrdered as $also)
                    <a href="{{ route('products.show', $also->slug) }}"
                       class="bg-white rounded-3xl shadow-sm hover:shadow-md transition-shadow overflow-hidden group block">
                        <div class="h-40 bg-light flex items-center justify-center overflow-hidden">
                            @if($also->image)
                                <img src="{{ asset('storage/' . $also->image) }}"
                                     alt="{{ $also->getTranslation('name', app()->getLocale()) }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            @else
                                <span class="text-4xl">📦</span>
                            @endif
                        </div>
                        <div class="p-5">
                            <h3 class="font-alumni text-h6 text-dark line-clamp-2 mb-2 group-hover:text-primary transition-colors">
                                {{ $also->getTranslation('name', app()->getLocale()) }}
                            </h3>
                            @auth
                                @if(auth()->user()->canSeePrices())
                                    <p class="font-alumni text-h5 text-primary">
                                        {{ number_format($also->price_with_vat, 2, ',', '.') }} €
                                    </p>
                                @endif
                            @endauth
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Related Products --}}
    @if($related->isNotEmpty())
        <div>
            <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-6">
                {{ __('app.related_products') }}
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($related as $rel)
                    <a href="{{ route('products.show', $rel->slug) }}"
                       class="bg-white rounded-3xl shadow-sm hover:shadow-md transition-shadow overflow-hidden group block">
                        <div class="h-40 bg-light flex items-center justify-center overflow-hidden">
                            @if($rel->image)
                                <img src="{{ asset('storage/' . $rel->image) }}"
                                     alt="{{ $rel->getTranslation('name', app()->getLocale()) }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            @else
                                <span class="text-4xl">📦</span>
                            @endif
                        </div>
                        <div class="p-5">
                            <h3 class="font-alumni text-h6 text-dark line-clamp-2 mb-2 group-hover:text-primary transition-colors">
                                {{ $rel->getTranslation('name', app()->getLocale()) }}
                            </h3>
                            @auth
                                @if(auth()->user()->canSeePrices())
                                    <p class="font-alumni text-h5 text-primary">
                                        {{ number_format($rel->price_with_vat, 2, ',', '.') }} €
                                    </p>
                                @endif
                            @endauth
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function productPage() {
    return {
        images: @json($allImages->map(fn($img) => ['url' => $img->url, 'alt' => $img->alt])->values()),
        variantImages: @json($variantImagesMap),
        selected: {},
        activeIndex: 0,
        variantImageUrl: null,

        get displayUrl() {
            return this.variantImageUrl ?? this.images[this.activeIndex]?.url ?? '';
        },
        get displayAlt() {
            if (this.variantImageUrl) return '';
            return this.images[this.activeIndex]?.alt ?? '';
        },

        setGalleryImage(i) {
            this.activeIndex = i;
            this.variantImageUrl = null;
        },

        selectVariant(type, id) {
            this.selected[type] = id;
            this.variantImageUrl = this.variantImages[id] ?? null;
        },
    };
}
</script>
@endpush
