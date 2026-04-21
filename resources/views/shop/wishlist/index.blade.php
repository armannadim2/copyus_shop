@extends('layouts.app')
@section('title', __('app.wishlist'))

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-20">

    <div class="mb-10">
        <h1 class="font-alumni text-h1 text-dark leading-tight">
            {{ __('app.wishlist_title_1') }}
            <span class="text-secondary">{{ __('app.wishlist_title_2') }}</span>
        </h1>
        <p class="font-alumni text-h5 text-primary mt-1">{{ __('app.wishlist_subtitle') }}</p>
    </div>

    @if(session('success'))
        <div class="mb-6 px-5 py-4 bg-green-50 border border-green-200 rounded-2xl font-outfit text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if($items->isEmpty())
        <div class="text-center py-24 bg-white rounded-3xl shadow-sm">
            <p class="text-6xl mb-6">🤍</p>
            <p class="font-alumni text-h4 text-dark mb-2">{{ __('app.wishlist_empty') }}</p>
            <p class="font-outfit text-sm text-gray-400 mb-8">{{ __('app.wishlist_empty_hint') }}</p>
            <a href="{{ route('products.index') }}"
               class="inline-flex items-center gap-2 bg-primary text-white font-alumni text-sm-header
                      px-8 py-3 rounded-2xl hover:brightness-110 active:scale-95 transition-all">
                {{ __('app.browse_products') }}
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($items as $item)
                @php
                    $product = $item->product;
                    if (!$product) continue;
                    $locale = app()->getLocale();
                    $name   = $product->getTranslation('name', $locale);
                @endphp
                <div class="bg-white rounded-3xl shadow-sm overflow-hidden flex flex-col">
                    {{-- Image --}}
                    <div class="h-44 bg-light flex items-center justify-center overflow-hidden">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}"
                                 alt="{{ $name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <span class="text-5xl">📦</span>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="p-5 flex flex-col flex-1">
                        <span class="font-outfit text-xs text-secondary mb-1">
                            {{ $product->category?->getTranslation('name', $locale) }}
                        </span>
                        <h3 class="font-alumni text-h6 text-dark leading-tight mb-1 line-clamp-2">
                            {{ $name }}
                        </h3>
                        <p class="font-outfit text-xs text-gray-400 mb-3">
                            {{ $product->brand }} · {{ $product->sku }}
                        </p>

                        @auth
                            @if(auth()->user()->canSeePrices())
                                <p class="font-alumni text-h5 text-primary mb-1">
                                    {{ number_format($product->price_with_vat, 2, ',', '.') }} €
                                </p>
                            @endif
                        @endauth

                        <div class="mt-auto pt-4 flex gap-2">
                            <a href="{{ route('products.show', $product->slug) }}"
                               class="flex-1 flex items-center justify-center gap-1.5
                                      border-2 border-dark/20 text-dark font-outfit text-xs font-medium
                                      py-2 rounded-xl hover:border-primary hover:text-primary transition-all">
                                {{ __('app.view') }}
                            </a>

                            @if($product->stock > 0 && auth()->user()->canSeePrices())
                                <form method="POST" action="{{ route('cart.add') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}" />
                                    <input type="hidden" name="quantity" value="{{ $product->min_order_quantity }}" />
                                    <button type="submit"
                                            title="{{ __('app.add_to_cart') }}"
                                            class="flex items-center justify-center w-10 h-10 rounded-xl
                                                   bg-primary text-white hover:brightness-110 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif

                            {{-- Remove from wishlist --}}
                            <form method="POST" action="{{ route('wishlist.destroy', $product->id) }}">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        title="{{ __('app.remove_from_wishlist') }}"
                                        class="flex items-center justify-center w-10 h-10 rounded-xl
                                               border-2 border-red-200 text-red-400
                                               hover:border-red-400 hover:text-red-600 transition-all">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.27 2 8.5 2 5.41 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.08C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.41 22 8.5c0 3.77-3.4 6.86-8.55 11.53L12 21.35z"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
