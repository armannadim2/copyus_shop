@extends('layouts.app')
@section('title', __('app.search_results_for') . ': ' . $query)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="font-alumni text-h2 text-dark">
            {{ __('app.search_results_for') }}
            <span class="text-secondary">«{{ $query }}»</span>
        </h1>
        <p class="font-outfit text-sm text-gray-400 mt-1">
            {{ $products->total() }} {{ __('app.results_found') }}
        </p>
    </div>

    {{-- Search Bar --}}
    <form method="GET" action="{{ route('search') }}" class="flex gap-3 mb-8 max-w-lg">
        <input type="text" name="q" value="{{ $query }}"
               class="flex-1 border border-gray-300 rounded-xl px-4 py-3
                      font-outfit text-sm focus:outline-none focus:ring-2 focus:ring-primary"
               placeholder="{{ __('app.search') }}..." />
        <button type="submit"
                class="bg-primary text-white font-alumni text-sm-header
                       px-6 py-3 rounded-xl hover:brightness-110 transition-all">
            {{ __('app.search') }}
        </button>
    </form>

    @if($products->isEmpty())
        <div class="text-center py-24 bg-white rounded-3xl shadow-sm">
            <p class="text-6xl mb-4">🔍</p>
            <p class="font-alumni text-h4 text-dark mb-2">{{ __('app.no_products_found') }}</p>
            <p class="font-outfit text-sm text-gray-400 mb-8">{{ __('app.search_no_results_hint') }}</p>
            <a href="{{ route('products.index') }}"
               class="inline-flex items-center gap-2 border-2 border-primary text-primary
                      font-alumni text-sm-header px-8 py-3 rounded-2xl hover:bg-primary hover:text-white
                      transition-all">
                {{ __('app.browse_products') }}
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
                @php
                    $locale     = app()->getLocale();
                    $name       = $product->getTranslation('name', $locale);
                    $productUrl = route('products.show', $product->slug);
                    $inWl       = isset($wishlistIds) && $wishlistIds->has($product->id);
                @endphp

                <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow overflow-hidden flex flex-col">
                    {{-- Image --}}
                    <div class="h-40 bg-light flex items-center justify-center overflow-hidden">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}"
                                 alt="{{ $name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <span class="text-5xl">📦</span>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="p-4 flex flex-col flex-1">
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

                        <div class="mt-auto pt-3 flex gap-2">
                            <a href="{{ $productUrl }}"
                               class="flex-1 flex items-center justify-center gap-1.5
                                      border-2 border-dark/20 text-dark font-outfit text-xs font-medium
                                      py-2 rounded-xl hover:border-primary hover:text-primary transition-all">
                                {{ __('app.view') }}
                            </a>

                            @auth
                                <form method="POST" action="{{ route('wishlist.toggle', $product->id) }}">
                                    @csrf
                                    <button type="submit"
                                            title="{{ $inWl ? __('app.remove_from_wishlist') : __('app.add_to_wishlist') }}"
                                            class="flex items-center justify-center w-10 h-10 rounded-xl border-2 transition-all
                                                   {{ $inWl
                                                        ? 'border-red-400 text-red-500 bg-red-50'
                                                        : 'border-gray-200 text-gray-400 hover:border-red-300 hover:text-red-400' }}">
                                        <svg class="w-4 h-4" fill="{{ $inWl ? 'currentColor' : 'none' }}"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    </button>
                                </form>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $products->appends(['q' => $query])->links() }}
        </div>
    @endif
</div>
@endsection
