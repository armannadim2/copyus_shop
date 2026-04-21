@extends('layouts.app')
@section('title', __('app.your_cart'))

@section('content')

{{-- Hero --}}
<div class="text-center pt-16 pb-12 px-4">
    <h1 class="font-alumni text-h1 text-dark leading-tight mb-4">
        {{ __('app.cart_title_1') }}
        <span class="text-secondary">{{ __('app.cart_title_2') }}</span>
    </h1>
    <p class="font-alumni text-h5 text-primary max-w-xl mx-auto">
        {{ __('app.cart_subtitle') }}
    </p>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 pb-20">
    @if($cartItems->isEmpty())
        <div class="bg-white rounded-3xl shadow-sm p-16 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h2 class="font-alumni text-h4 text-dark mb-3">{{ __('app.cart_empty') }}</h2>
            <p class="font-outfit text-sm text-gray-500 mb-8">{{ __('app.cart_empty_message') }}</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center gap-2 bg-primary text-white font-alumni text-sm-header
                          px-8 py-3 rounded-2xl hover:brightness-110 active:scale-95 transition-all">
                    {{ __('app.browse_products') }}
                </a>
                <a href="{{ route('print.index') }}"
                   class="inline-flex items-center gap-2 border-2 border-primary text-primary font-alumni text-sm-header
                          px-8 py-3 rounded-2xl hover:bg-primary hover:text-white active:scale-95 transition-all">
                    🖨️ Serveis d'impressió
                </a>
            </div>
        </div>
    @else
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden">

            {{-- Header row --}}
            <div class="flex items-center justify-between px-8 py-5 border-b border-gray-100">
                <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase">
                    {{ $cartItems->sum('quantity') }} {{ __('app.items') }}
                </p>
                <form method="POST" action="{{ route('cart.clear') }}">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="font-outfit text-xs text-gray-400 hover:text-primary transition-colors">
                        {{ __('app.clear_cart') }}
                    </button>
                </form>
            </div>

            {{-- Items --}}
            <div class="p-8 space-y-3">
                @foreach($cartItems as $item)
                    @if($item->is_print_job)
                        {{-- ── Print job item ── --}}
                        @php
                            $job      = $item->printJob;
                            $tpl      = $job?->template;
                            $snapshot = $item->configuration_snapshot ?? [];
                            $locale   = app()->getLocale();
                        @endphp
                        <div class="flex items-start gap-4 p-4 rounded-2xl bg-primary/5 border border-primary/10 hover:bg-primary/8 transition-colors">
                            <div class="w-16 h-16 rounded-xl bg-white flex items-center justify-center flex-shrink-0 shadow-sm">
                                <span class="text-2xl">{{ $tpl?->icon ?? '🖨️' }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-alumni text-h6 text-dark leading-tight">
                                        {{ $item->display_name }}
                                    </h3>
                                    <span class="bg-primary/10 text-primary font-outfit text-xs px-2 py-0.5 rounded-full">
                                        🖨️ Impressió
                                    </span>
                                </div>
                                {{-- Config summary --}}
                                @if($job && $job->configuration)
                                    <p class="font-outfit text-xs text-gray-400 mt-1 leading-relaxed">
                                        @foreach($job->configuration as $key => $value)
                                            <span class="capitalize">{{ str_replace('_', ' ', $key) }}:</span>
                                            <span class="text-gray-600">{{ $value }}</span>
                                            @if(!$loop->last) · @endif
                                        @endforeach
                                    </p>
                                @endif
                                <p class="font-outfit text-xs text-gray-400 mt-1">
                                    {{ number_format($item->effective_unit_price, 4, ',', '.') }} € / ut.
                                    @if($tpl) · IVA {{ $tpl->vat_rate }}% @endif
                                    @if($job?->production_days) · {{ $job->production_days }}d producció @endif
                                </p>
                            </div>
                            <form method="POST" action="{{ route('cart.update', $item->id) }}"
                                  class="flex items-center gap-2 shrink-0">
                                @csrf @method('PATCH')
                                <input type="number" name="quantity"
                                       value="{{ $item->quantity }}"
                                       min="1"
                                       class="w-20 bg-white border border-gray-200 rounded-xl px-3 py-1.5
                                              font-outfit text-sm text-center focus:outline-none
                                              focus:ring-2 focus:ring-primary/40 focus:border-primary transition" />
                                <button type="submit"
                                        class="font-outfit text-xs text-secondary hover:text-primary
                                               transition-colors hover:underline underline-offset-2">
                                    {{ __('app.save') }}
                                </button>
                            </form>
                            <div class="text-right shrink-0 min-w-[80px]">
                                <p class="font-alumni text-h6 text-dark">
                                    {{ number_format($item->line_total, 2, ',', '.') }} €
                                </p>
                                <p class="font-outfit text-xs text-gray-400">IVA incl.</p>
                            </div>
                            <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-8 h-8 flex items-center justify-center rounded-xl
                                               text-gray-300 hover:text-primary hover:bg-white transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @else
                        {{-- ── Regular product item ── --}}
                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-light/60 hover:bg-light transition-colors">
                            <div class="w-16 h-16 rounded-xl bg-white flex items-center justify-center flex-shrink-0 overflow-hidden shadow-sm">
                                @if($item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}"
                                         alt="{{ $item->product->getTranslation('name', app()->getLocale()) }}"
                                         class="w-full h-full object-cover" />
                                @else
                                    <span class="text-2xl">📦</span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-alumni text-h6 text-dark leading-tight truncate">
                                    {{ $item->product->getTranslation('name', app()->getLocale()) }}
                                </h3>
                                <p class="font-outfit text-xs text-gray-400 mt-0.5">
                                    {{ $item->product->brand }} · {{ $item->product->sku }}
                                    · {{ number_format($item->product->price_with_vat, 2, ',', '.') }} € / {{ $item->product->unit }}
                                </p>
                            </div>
                            <form method="POST" action="{{ route('cart.update', $item->id) }}"
                                  class="flex items-center gap-2 shrink-0">
                                @csrf @method('PATCH')
                                <input type="number" name="quantity"
                                       value="{{ $item->quantity }}"
                                       min="{{ $item->product->min_order_quantity }}"
                                       class="w-20 bg-white border border-gray-200 rounded-xl px-3 py-1.5
                                              font-outfit text-sm text-center focus:outline-none
                                              focus:ring-2 focus:ring-primary/40 focus:border-primary transition" />
                                <button type="submit"
                                        class="font-outfit text-xs text-secondary hover:text-primary
                                               transition-colors hover:underline underline-offset-2">
                                    {{ __('app.save') }}
                                </button>
                            </form>
                            <div class="text-right shrink-0 min-w-[80px]">
                                <p class="font-alumni text-h6 text-dark">
                                    {{ number_format($item->line_total, 2, ',', '.') }} €
                                </p>
                                <p class="font-outfit text-xs text-gray-400">IVA incl.</p>
                            </div>
                            <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-8 h-8 flex items-center justify-center rounded-xl
                                               text-gray-300 hover:text-primary hover:bg-white transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Divider --}}
            <div class="h-px bg-gray-100 mx-8"></div>

            {{-- Summary + Checkout --}}
            <div class="p-8">

                {{-- Promo code --}}
                @php $appliedPromo = session('promo_code') ? \App\Models\PromoCode::where('code', session('promo_code'))->first() : null; @endphp
                <div class="mb-6">
                    @if(session('promo_error'))
                        <p class="font-outfit text-xs text-red-500 mb-2">{{ session('promo_error') }}</p>
                    @endif
                    @if(session('promo_success'))
                        <p class="font-outfit text-xs text-green-600 mb-2">{{ session('promo_success') }}</p>
                    @endif

                    @if($appliedPromo)
                        <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-xl px-4 py-2.5 mb-2">
                            <div>
                                <span class="font-outfit text-xs font-semibold text-green-700">{{ $appliedPromo->code }}</span>
                                <span class="font-outfit text-xs text-green-600 ml-2">
                                    − {{ $appliedPromo->type === 'percent' ? $appliedPromo->value.'%' : number_format($appliedPromo->value,2,',','.').' €' }}
                                </span>
                            </div>
                            <form method="POST" action="{{ route('promo.remove') }}">
                                @csrf
                                <button type="submit" class="font-outfit text-xs text-red-400 hover:text-red-600 transition-colors">Treure</button>
                            </form>
                        </div>
                    @else
                        <form method="POST" action="{{ route('promo.apply') }}" class="flex gap-2">
                            @csrf
                            <input type="text" name="code" placeholder="Codi de descompte"
                                   class="flex-1 border border-gray-200 rounded-xl px-3 py-2
                                          font-outfit text-sm uppercase focus:outline-none
                                          focus:ring-2 focus:ring-primary/40 transition">
                            <button type="submit"
                                    class="border-2 border-primary text-primary font-outfit text-sm
                                           px-4 py-2 rounded-xl hover:bg-primary hover:text-white transition-all">
                                Aplicar
                            </button>
                        </form>
                    @endif
                </div>

                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                    <div class="space-y-1">
                        <div class="flex items-center gap-6">
                            <span class="font-outfit text-xs text-gray-400">{{ __('app.subtotal') }}</span>
                            <span class="font-outfit text-sm text-dark">{{ number_format($subtotal, 2, ',', '.') }} €</span>
                        </div>
                        @if($appliedPromo)
                            @php $discount = $appliedPromo->calculateDiscount($subtotal); @endphp
                            <div class="flex items-center gap-6">
                                <span class="font-outfit text-xs text-green-600">Descompte ({{ $appliedPromo->code }})</span>
                                <span class="font-outfit text-sm text-green-600">− {{ number_format($discount, 2, ',', '.') }} €</span>
                            </div>
                        @endif
                        <div class="flex items-center gap-6">
                            <span class="font-outfit text-xs text-gray-400">{{ __('app.vat') }}</span>
                            <span class="font-outfit text-sm text-dark">{{ number_format($vatAmount, 2, ',', '.') }} €</span>
                        </div>
                        <div class="flex items-center gap-6 pt-2 border-t border-gray-100">
                            <span class="font-alumni text-h6 text-dark">{{ __('app.total') }}</span>
                            <span class="font-alumni text-h4 text-primary">
                                {{ number_format($total - ($discount ?? 0), 2, ',', '.') }} €
                            </span>
                        </div>
                    </div>
                    <div class="flex flex-col items-end gap-3">
                        <a href="{{ route('orders.checkout') }}"
                           class="inline-flex items-center gap-2 bg-primary text-white font-alumni text-sm-header
                                  px-8 py-3 rounded-2xl hover:brightness-110 active:scale-95 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            {{ __('app.proceed_to_checkout') }}
                        </a>
                        <div class="flex gap-4">
                            <a href="{{ route('products.index') }}"
                               class="font-outfit text-xs text-gray-400 hover:text-primary transition-colors">
                                ← {{ __('app.continue_shopping') }}
                            </a>
                            <a href="{{ route('print.index') }}"
                               class="font-outfit text-xs text-gray-400 hover:text-primary transition-colors">
                                + Afegir impressió
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
