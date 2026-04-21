@extends('layouts.app')
@section('title', __('app.your_quote_basket'))

@section('content')

{{-- ── Hero ─────────────────────────────────────────────────────────────────── --}}
<div class="text-center pt-16 pb-12 px-4">
    <h1 class="font-alumni text-h1 text-dark leading-tight mb-4">
        {{ __('app.quote_page_title_1') }}
        <span class="text-secondary">{{ __('app.quote_page_title_2') }}</span>
    </h1>
    <p class="font-alumni text-h5 text-primary max-w-2xl mx-auto leading-snug">
        {{ __('app.quote_page_subtitle') }}
    </p>
</div>

@if($quoteItems->isEmpty())

    {{-- ── Empty State ──────────────────────────────────────────────────────── --}}
    <div class="max-w-xl mx-auto px-4 pb-20 text-center">
        <div class="bg-white rounded-3xl shadow-sm p-16">
            <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h2 class="font-alumni text-h4 text-dark mb-3">{{ __('app.quote_basket_empty') }}</h2>
            <p class="font-outfit text-body-lg text-gray-500 mb-8">
                {{ __('app.cart_empty_message') }}
            </p>
            <a href="{{ route('products.index') }}"
               class="inline-flex items-center gap-2 bg-primary text-white font-alumni text-sm-header
                      px-8 py-3 rounded-2xl hover:brightness-110 active:scale-95 transition-all">
                {{ __('app.browse_products') }}
            </a>
        </div>
    </div>

@else

    {{-- ── Main Card ────────────────────────────────────────────────────────── --}}
    <div class="max-w-4xl mx-auto px-4 sm:px-6 pb-20">
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden">

            {{-- Items list --}}
            <div class="p-8 space-y-4">
                <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-6">
                    {{ $quoteItems->count() }} {{ __('app.products') }}
                </p>

                @foreach($quoteItems as $item)
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-light/60
                                hover:bg-light transition-colors duration-150">

                        {{-- Image --}}
                        <div class="w-16 h-16 rounded-xl bg-white flex items-center
                                    justify-center flex-shrink-0 overflow-hidden shadow-sm">
                            @if($item->product->image)
                                <img src="{{ asset('storage/' . $item->product->image) }}"
                                     alt="{{ $item->product->getTranslation('name', app()->getLocale()) }}"
                                     class="w-full h-full object-cover" />
                            @else
                                <span class="text-2xl">📦</span>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="font-alumni text-h6 text-dark leading-tight truncate">
                                {{ $item->product->getTranslation('name', app()->getLocale()) }}
                            </h3>
                            <p class="font-outfit text-xs text-gray-400 mt-0.5">
                                {{ $item->product->brand }} · {{ $item->product->sku }}
                            </p>
                        </div>

                        {{-- Quantity --}}
                        <form method="POST" action="{{ route('quotations.update', $item->id) }}"
                              class="flex items-center gap-2 shrink-0">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="quantity"
                                   value="{{ $item->quantity }}"
                                   min="{{ $item->product->min_order_quantity }}"
                                   class="w-20 bg-white border border-gray-200 rounded-xl px-3 py-1.5
                                          font-outfit text-sm text-center focus:outline-none
                                          focus:ring-2 focus:ring-primary/40 focus:border-primary transition" />
                            <span class="font-outfit text-xs text-gray-400">{{ $item->product->unit }}</span>
                            <button type="submit"
                                    class="font-outfit text-xs text-secondary hover:text-primary
                                           transition-colors underline-offset-2 hover:underline">
                                {{ __('app.save') }}
                            </button>
                        </form>

                        {{-- Remove --}}
                        <form method="POST" action="{{ route('quotations.remove', $item->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-8 h-8 flex items-center justify-center rounded-xl
                                           text-gray-300 hover:text-primary hover:bg-white
                                           transition-all duration-150"
                                    title="{{ __('app.remove_item') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            {{-- Divider --}}
            <div class="h-px bg-gray-100 mx-8"></div>

            {{-- Submit form --}}
            <form method="POST" action="{{ route('quotations.submit') }}" class="p-8">
                @csrf

                @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700
                                font-outfit text-sm px-4 py-3 rounded-2xl">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-600
                                font-outfit text-sm px-4 py-3 rounded-2xl">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="mb-6">
                    <label class="block font-outfit text-xs font-semibold tracking-widest
                                  text-primary uppercase mb-2">
                        {{ __('app.your_message') }}
                    </label>
                    <textarea name="customer_notes" rows="4"
                              placeholder="{{ __('app.quote_notes_placeholder') }}"
                              class="w-full bg-light border-0 rounded-2xl px-5 py-4
                                     font-outfit text-sm text-dark placeholder:text-gray-400
                                     focus:outline-none focus:ring-2 focus:ring-primary/30
                                     resize-none transition">{{ old('customer_notes') }}</textarea>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="font-outfit text-sm text-gray-400 flex items-center gap-2">
                        <svg class="w-4 h-4 text-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ __('app.quote_response_time') }}
                    </p>
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-secondary text-white
                                   font-alumni text-sm-header px-8 py-3 rounded-2xl
                                   hover:brightness-110 active:scale-95 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        {{ __('app.submit_quotation') }}
                    </button>
                </div>
            </form>

        </div>
    </div>

@endif
@endsection
