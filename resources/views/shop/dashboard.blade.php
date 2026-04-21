@extends('layouts.app')
@section('title', __('app.dashboard'))

@section('content')

{{-- Hero --}}
<div class="pt-16 pb-10 px-4 text-center">
    <h1 class="font-alumni text-h1 text-dark leading-tight mb-2">
        {{ __('app.welcome_back') }},
        <span class="text-secondary">{{ $user->name }}</span>
    </h1>
    <p class="font-alumni text-h5 text-primary">
        {{ $user->company_name ?? $user->email }}
    </p>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">

    {{-- Artwork upload needed --}}
    @if($pendingArtwork > 0)
        <div class="mb-6 bg-amber-50 border border-amber-200 rounded-3xl px-6 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-2xl bg-amber-100 flex items-center justify-center shrink-0">
                    <span class="text-xl">🖨️</span>
                </div>
                <div>
                    <p class="font-alumni text-h6 text-amber-800">
                        {{ $pendingArtwork }} {{ $pendingArtwork === 1 ? 'treball d\'impressió' : 'treballs d\'impressió' }} esperant el teu arxiu de disseny
                    </p>
                    <p class="font-outfit text-xs text-amber-600">Puja l'arxiu des de la teva comanda per no retardar la producció.</p>
                </div>
            </div>
            <a href="{{ route('orders.index') }}"
               class="shrink-0 font-alumni text-sm-header bg-amber-500 text-white px-5 py-2.5
                      rounded-2xl hover:brightness-110 active:scale-95 transition-all">
                Veure comandes →
            </a>
        </div>
    @endif

    {{-- Action Required Alert --}}
    @if($quotesAwaiting > 0)
        <div class="mb-8 bg-purple-50 border border-purple-200 rounded-3xl px-6 py-5 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-2xl bg-purple-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div>
                    <p class="font-alumni text-h6 text-purple-800">
                        {{ $quotesAwaiting }} {{ __('app.quotes_awaiting') }}
                    </p>
                    <p class="font-outfit text-xs text-purple-600">{{ __('app.accept_quote_hint') }}</p>
                </div>
            </div>
            <a href="{{ route('quotations.index') }}"
               class="shrink-0 font-alumni text-sm-header bg-purple-600 text-white px-5 py-2.5
                      rounded-2xl hover:brightness-110 active:scale-95 transition-all">
                {{ __('app.view') }} →
            </a>
        </div>
    @endif

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-10">

        {{-- Active orders --}}
        <div class="bg-white rounded-3xl shadow-sm p-6 text-center">
            <p class="font-alumni text-h2 {{ $activeOrders > 0 ? 'text-secondary' : 'text-dark' }}">
                {{ $activeOrders }}
            </p>
            <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mt-1">
                {{ __('app.active_orders') }}
            </p>
            @if($lastOrder)
                <p class="font-outfit text-xs text-gray-400 mt-2">
                    {{ __('app.last_order') }}: {{ $lastOrder->created_at->format('d/m/Y') }}
                </p>
            @endif
        </div>

        {{-- Total orders --}}
        <div class="bg-white rounded-3xl shadow-sm p-6 text-center">
            <p class="font-alumni text-h2 text-dark">{{ $totalOrders }}</p>
            <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mt-1">{{ __('app.orders') }}</p>
        </div>

        {{-- Quotes awaiting --}}
        <div class="bg-white rounded-3xl shadow-sm p-6 text-center {{ $quotesAwaiting > 0 ? 'border-2 border-purple-200' : '' }}">
            <p class="font-alumni text-h2 {{ $quotesAwaiting > 0 ? 'text-purple-600' : 'text-dark' }}">
                {{ $quotesAwaiting }}
            </p>
            <p class="font-outfit text-xs font-semibold tracking-widest {{ $quotesAwaiting > 0 ? 'text-purple-500' : 'text-primary' }} uppercase mt-1">
                {{ __('app.quotation') }}
            </p>
            @if($totalQuotes > $quotesAwaiting)
                <p class="font-outfit text-xs text-gray-400 mt-2">{{ $totalQuotes }} {{ __('app.total') }}</p>
            @endif
        </div>

        {{-- Spent this year --}}
        <div class="bg-white rounded-3xl shadow-sm p-6 text-center border-2 border-primary/20">
            <p class="font-alumni text-h2 text-primary">{{ number_format($spentThisYear, 2, ',', '.') }} €</p>
            <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mt-1">
                {{ __('app.total_spent_year') }}
            </p>
            @if($totalSpent > $spentThisYear)
                <p class="font-outfit text-xs text-gray-400 mt-2">
                    {{ number_format($totalSpent, 2, ',', '.') }} € {{ __('app.total') }}
                </p>
            @endif
        </div>

        {{-- Active print jobs --}}
        <a href="{{ route('print.index') }}"
           class="bg-white rounded-3xl shadow-sm p-6 text-center hover:shadow-md transition-shadow
                  {{ $activePrintJobs > 0 ? 'border-2 border-orange-200' : '' }}">
            <p class="font-alumni text-h2 {{ $activePrintJobs > 0 ? 'text-orange-500' : 'text-dark' }}">
                {{ $activePrintJobs }}
            </p>
            <p class="font-outfit text-xs font-semibold tracking-widest
                      {{ $activePrintJobs > 0 ? 'text-orange-500' : 'text-primary' }} uppercase mt-1">
                🖨️ Impressió
            </p>
            <p class="font-outfit text-xs text-gray-400 mt-2">treballs actius</p>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- Recent Orders --}}
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase">{{ __('app.recent_orders') }}</p>
                <a href="{{ route('orders.index') }}"
                   class="font-outfit text-xs text-secondary hover:text-primary transition-colors">{{ __('app.view_all') }} →</a>
            </div>
            @if($recentOrders->isEmpty())
                <div class="px-6 py-10 text-center">
                    <p class="font-outfit text-sm text-gray-400">{{ __('app.no_orders') }}</p>
                    <a href="{{ route('products.index') }}"
                       class="inline-block mt-4 font-alumni text-sm-header text-primary hover:underline">
                        {{ __('app.browse_products') }} →
                    </a>
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($recentOrders as $order)
                        @php $c = $order->status_color; @endphp
                        <a href="{{ route('orders.show', $order->order_number) }}"
                           class="flex items-center justify-between px-6 py-4 hover:bg-light transition-colors">
                            <div>
                                <p class="font-alumni text-sm-header text-dark">#{{ $order->order_number }}</p>
                                <p class="font-outfit text-xs text-gray-400">{{ $order->created_at->format('d/m/Y') }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="inline-block font-outfit text-xs px-2 py-0.5 rounded-full {{ $c }}">
                                    {{ __('app.status_' . $order->status) }}
                                </span>
                                <span class="font-alumni text-h6 text-dark">{{ number_format($order->total, 2, ',', '.') }} €</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Recent Print Jobs --}}
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase">🖨️ Treballs impressió</p>
                <div class="flex items-center gap-4">
                    <a href="{{ route('print.my-jobs') }}"
                       class="font-outfit text-xs text-gray-400 hover:text-primary transition-colors">Veure tots →</a>
                    <a href="{{ route('print.index') }}"
                       class="font-outfit text-xs text-secondary hover:text-primary transition-colors">+ Nou →</a>
                </div>
            </div>
            @if($recentPrintJobs->isEmpty())
                <div class="px-6 py-10 text-center">
                    <p class="font-outfit text-sm text-gray-400">Encara no has fet cap treball d'impressió.</p>
                    <a href="{{ route('print.index') }}"
                       class="inline-block mt-4 font-alumni text-sm-header text-primary hover:underline">
                        Descobrir serveis →
                    </a>
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($recentPrintJobs as $job)
                        @php
                            $badge = match($job->status) {
                                'ordered'       => 'bg-yellow-50 text-yellow-700',
                                'in_production' => 'bg-orange-50 text-orange-700',
                                'completed'     => 'bg-green-50 text-green-700',
                                'cancelled'     => 'bg-red-50 text-red-500',
                                default         => 'bg-gray-100 text-gray-500',
                            };
                            $label = match($job->status) {
                                'ordered'       => 'Pendent',
                                'in_production' => 'En producció',
                                'completed'     => 'Completat',
                                'cancelled'     => 'Cancel·lat',
                                default         => $job->status,
                            };
                        @endphp
                        <div class="flex items-center justify-between px-6 py-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="text-xl shrink-0">{{ $job->template->icon ?? '🖨️' }}</span>
                                <div class="min-w-0">
                                    <p class="font-outfit text-sm font-semibold text-dark truncate">
                                        {{ $job->template->getTranslation('name', app()->getLocale()) }}
                                    </p>
                                    <p class="font-outfit text-xs text-gray-400">
                                        {{ number_format($job->quantity, 0, ',', '.') }} ut.
                                        · {{ $job->created_at->format('d/m/Y') }}
                                        @if(!$job->artwork_path && in_array($job->status, ['ordered', 'in_production']))
                                            · <span class="text-amber-500 font-semibold">⚠ sense arxiu</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <span class="font-outfit text-xs px-2 py-0.5 rounded-full shrink-0 {{ $badge }}">
                                {{ $label }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Recent Quotations --}}
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase">{{ __('app.recent_quotes') }}</p>
                <a href="{{ route('quotations.index') }}"
                   class="font-outfit text-xs text-secondary hover:text-primary transition-colors">{{ __('app.view_all') }} →</a>
            </div>
            @if($recentQuotes->isEmpty())
                <div class="px-6 py-10 text-center">
                    <p class="font-outfit text-sm text-gray-400">{{ __('app.quote_basket_empty') }}</p>
                    <a href="{{ route('quotations.basket') }}"
                       class="inline-block mt-4 font-alumni text-sm-header text-secondary hover:underline">
                        {{ __('app.request_quote') }} →
                    </a>
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($recentQuotes as $quote)
                        @php $c = $quote->status_color; @endphp
                        <a href="{{ route('quotations.show', $quote->quote_number) }}"
                           class="flex items-center justify-between px-6 py-4 hover:bg-light transition-colors
                                  {{ $quote->status === 'quoted' ? 'bg-purple-50/40' : '' }}">
                            <div>
                                <p class="font-alumni text-sm-header text-dark">#{{ $quote->quote_number }}</p>
                                <p class="font-outfit text-xs text-gray-400">{{ $quote->created_at->format('d/m/Y') }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="inline-block font-outfit text-xs px-2 py-0.5 rounded-full {{ $c }}">
                                    {{ __('app.quote_status_' . $quote->status) }}
                                </span>
                                <span class="font-alumni text-h6 text-dark">
                                    {{ $quote->total_quoted ? number_format($quote->total_quoted, 2, ',', '.') . ' €' : '—' }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 sm:grid-cols-5 gap-4">
        <a href="{{ route('products.index') }}"
           class="bg-white rounded-3xl shadow-sm p-6 flex items-center gap-4
                  hover:shadow-md transition-shadow group">
            <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <div>
                <p class="font-alumni text-h6 text-dark group-hover:text-primary transition-colors">{{ __('app.browse_products') }}</p>
                <p class="font-outfit text-xs text-gray-400">{{ __('app.browse_products_hint') }}</p>
            </div>
        </a>
        <a href="{{ route('quotations.basket') }}"
           class="bg-white rounded-3xl shadow-sm p-6 flex items-center gap-4
                  hover:shadow-md transition-shadow group">
            <div class="w-12 h-12 rounded-2xl bg-secondary/10 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="font-alumni text-h6 text-dark group-hover:text-primary transition-colors">{{ __('app.request_quote') }}</p>
                <p class="font-outfit text-xs text-gray-400">{{ __('app.request_quote_hint') }}</p>
            </div>
        </a>
        <a href="{{ route('invoices.index') }}"
           class="bg-white rounded-3xl shadow-sm p-6 flex items-center gap-4
                  hover:shadow-md transition-shadow group">
            <div class="w-12 h-12 rounded-2xl bg-dark/5 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-dark/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="font-alumni text-h6 text-dark group-hover:text-primary transition-colors">{{ __('app.invoices') }}</p>
                <p class="font-outfit text-xs text-gray-400">{{ __('app.invoices_hint') }}</p>
            </div>
        </a>
        <a href="{{ route('print.index') }}"
           class="bg-white rounded-3xl shadow-sm p-6 flex items-center gap-4
                  hover:shadow-md transition-shadow group border-2 border-primary/10">
            <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center shrink-0 text-2xl">
                🖨️
            </div>
            <div>
                <p class="font-alumni text-h6 text-dark group-hover:text-primary transition-colors">Impressió a mida</p>
                <p class="font-outfit text-xs text-gray-400">Configura i encarrega treballs professionals</p>
            </div>
        </a>
        <a href="{{ route('tickets.index') }}"
           class="bg-white rounded-3xl shadow-sm p-6 flex items-center gap-4
                  hover:shadow-md transition-shadow group">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center shrink-0 text-2xl">
                🎫
            </div>
            <div>
                <p class="font-alumni text-h6 text-dark group-hover:text-primary transition-colors">Suport</p>
                <p class="font-outfit text-xs text-gray-400">Dubtes o incidències amb les teves comandes</p>
            </div>
        </a>
    </div>
</div>
@endsection
