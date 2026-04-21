@extends('layouts.app')
@section('title', __('app.quote_number') . ' #' . $quotation->quote_number)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-20">

    {{-- Back + Header --}}
    <div class="mb-10">
        <a href="{{ route('quotations.index') }}"
           class="font-outfit text-xs text-gray-400 hover:text-primary transition-colors mb-4 inline-flex items-center gap-1">
            ← {{ __('app.quotation') }}
        </a>
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="font-alumni text-h1 text-dark leading-tight">
                    {{ __('app.quote_number') }}
                    <span class="text-secondary">#{{ $quotation->quote_number }}</span>
                </h1>
                <p class="font-alumni text-h5 text-primary mt-1">
                    {{ $quotation->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
            @php
                $statusColors = [
                    'pending'   => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                    'reviewing' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'quoted'    => 'bg-purple-50 text-purple-700 border-purple-200',
                    'accepted'  => 'bg-green-50 text-green-700 border-green-200',
                    'rejected'  => 'bg-red-50 text-red-600 border-red-200',
                    'expired'   => 'bg-gray-100 text-gray-500 border-gray-200',
                    'converted' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                ];
                $color = $statusColors[$quotation->status] ?? 'bg-gray-50 text-gray-600 border-gray-200';
            @endphp
            <span class="inline-block font-alumni text-sm-header border px-4 py-2 rounded-2xl {{ $color }}">
                {{ __('app.quote_status_' . $quotation->status) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Items --}}
        <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase">{{ __('app.products') }}</p>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($quotation->items as $item)
                    <div class="px-6 py-4 flex items-center gap-4">
                        <div class="w-14 h-14 bg-light rounded-2xl flex items-center justify-center flex-shrink-0">
                            <span class="text-2xl">📦</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-alumni text-h6 text-dark">
                                {{ $item->product?->getTranslation('name', app()->getLocale()) ?? '—' }}
                            </p>
                            <p class="font-outfit text-xs text-gray-400">
                                {{ $item->product?->sku }} · × {{ $item->quantity }} {{ $item->product?->unit }}
                            </p>
                        </div>
                        <div class="text-right">
                            @if($item->quoted_price)
                                <p class="font-alumni text-h6 text-primary">
                                    {{ number_format($item->quoted_price * $item->quantity, 2, ',', '.') }} €
                                </p>
                                <p class="font-outfit text-xs text-gray-400">
                                    {{ number_format($item->quoted_price, 2, ',', '.') }} € / {{ $item->product?->unit }}
                                </p>
                            @else
                                <p class="font-outfit text-xs text-gray-400">{{ __('app.quote_status_pending') }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @if($quotation->total_quoted)
                <div class="px-6 py-5 bg-light">
                    <div class="flex justify-between">
                        <span class="font-alumni text-h6 text-dark">{{ __('app.total') }}</span>
                        <span class="font-alumni text-h4 text-primary">{{ number_format($quotation->total_quoted, 2, ',', '.') }} €</span>
                    </div>
                    @if($quotation->valid_until)
                        <p class="font-outfit text-xs text-gray-400 mt-2">
                            {{ __('app.valid_until') }}: {{ \Carbon\Carbon::parse($quotation->valid_until)->format('d/m/Y') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Right --}}
        <div class="space-y-5">

            @if($quotation->status === 'quoted')
                <div class="bg-white rounded-3xl shadow-sm p-6">
                    <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-3">
                        {{ __('app.accept_quote') }}
                    </p>
                    <p class="font-outfit text-sm text-gray-500 mb-5">
                        {{ __('app.accept_quote_hint') }}
                    </p>
                    <form method="POST" action="{{ route('quotations.accept', $quotation->quote_number) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 bg-primary text-white
                                       font-alumni text-sm-header py-3 rounded-2xl hover:brightness-110
                                       active:scale-95 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ __('app.accept_quote') }}
                        </button>
                    </form>
                </div>
            @elseif($quotation->status === 'converted' && $quotation->convertedOrder)
                <div class="bg-indigo-50 border border-indigo-200 rounded-3xl p-6">
                    <p class="font-outfit text-xs font-semibold tracking-widest text-indigo-600 uppercase mb-3">
                        {{ __('app.converted_from_quote') }}
                    </p>
                    <p class="font-outfit text-sm text-indigo-700 mb-5">
                        {{ __('app.order_number') }}: <strong>#{{ $quotation->convertedOrder->order_number }}</strong>
                    </p>
                    <a href="{{ route('orders.show', $quotation->convertedOrder->order_number) }}"
                       class="w-full flex items-center justify-center gap-2 bg-indigo-600 text-white
                              font-alumni text-sm-header py-3 rounded-2xl hover:brightness-110
                              active:scale-95 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        {{ __('app.view_order') }}
                    </a>
                </div>
            @endif

            @if($quotation->admin_notes)
                <div class="bg-white rounded-3xl shadow-sm p-6">
                    <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-3">
                        {{ __('app.team_notes') }}
                    </p>
                    <p class="font-outfit text-sm text-gray-600">{{ $quotation->admin_notes }}</p>
                </div>
            @endif

            @if($quotation->customer_notes)
                <div class="bg-white rounded-3xl shadow-sm p-6">
                    <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-3">
                        {{ __('app.your_message') }}
                    </p>
                    <p class="font-outfit text-sm text-gray-600">{{ $quotation->customer_notes }}</p>
                </div>
            @endif

            <div class="bg-light rounded-3xl p-6 text-center">
                <p class="font-outfit text-sm text-gray-500 mb-4">{{ __('app.need_help_quote') }}</p>
                <a href="mailto:info@copyus.es"
                   class="inline-flex items-center gap-2 bg-white text-secondary font-alumni text-sm-header
                          px-5 py-2.5 rounded-2xl shadow-sm hover:shadow-md transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ __('app.contact_us') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
