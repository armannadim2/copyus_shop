@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8">
        <h1 class="font-alumni text-h1 text-dark">{{ __('app.admin_panel') }}</h1>
        <p class="font-outfit text-sm text-gray-400 mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    {{-- Action banners --}}
    @if($stats['pending_users'] > 0)
        <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-3xl px-6 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="text-yellow-500 text-xl">👥</span>
                <p class="font-outfit text-sm text-yellow-800">
                    <strong>{{ $stats['pending_users'] }}</strong> {{ $stats['pending_users'] === 1 ? 'usuari pendent' : 'usuaris pendents' }} d\'aprovació
                </p>
            </div>
            <a href="{{ route('admin.users.pending') }}"
               class="shrink-0 font-alumni text-sm-header bg-yellow-500 text-white px-5 py-2 rounded-2xl hover:brightness-110 transition-all">
                Revisar →
            </a>
        </div>
    @endif

    @if($stats['quotations_quoted'] > 0)
        <div class="mb-6 bg-purple-50 border border-purple-200 rounded-3xl px-6 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="text-purple-500 text-xl">📋</span>
                <p class="font-outfit text-sm text-purple-800">
                    <strong>{{ $stats['quotations_quoted'] }}</strong> {{ $stats['quotations_quoted'] === 1 ? 'pressupost enviat' : 'pressupostos enviats' }} esperant resposta del client
                </p>
            </div>
            <a href="{{ route('admin.quotations.index') }}?status=quoted"
               class="shrink-0 font-alumni text-sm-header bg-purple-600 text-white px-5 py-2 rounded-2xl hover:brightness-110 transition-all">
                Veure →
            </a>
        </div>
    @endif

    @if($stats['print_jobs_ordered'] > 0)
        <div class="mb-6 bg-orange-50 border border-orange-200 rounded-3xl px-6 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="text-orange-500 text-xl">🖨️</span>
                <p class="font-outfit text-sm text-orange-800">
                    <strong>{{ $stats['print_jobs_ordered'] }}</strong> {{ $stats['print_jobs_ordered'] === 1 ? 'treball d\'impressió' : 'treballs d\'impressió' }} pendent{{ $stats['print_jobs_ordered'] === 1 ? '' : 's' }} d\'iniciar producció
                </p>
            </div>
            <a href="{{ route('admin.print.jobs.index') }}?status=ordered"
               class="shrink-0 font-alumni text-sm-header bg-orange-500 text-white px-5 py-2 rounded-2xl hover:brightness-110 transition-all">
                Gestionar →
            </a>
        </div>
    @endif

    @if($stats['print_jobs_no_artwork'] > 0)
        <div class="mb-6 bg-red-50 border border-red-200 rounded-3xl px-6 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="text-red-500 text-xl">⚠️</span>
                <p class="font-outfit text-sm text-red-800">
                    <strong>{{ $stats['print_jobs_no_artwork'] }}</strong> {{ $stats['print_jobs_no_artwork'] === 1 ? 'treball' : 'treballs' }} en curs sense arxiu de disseny
                </p>
            </div>
            <a href="{{ route('admin.print.jobs.index') }}"
               class="shrink-0 font-alumni text-sm-header bg-red-500 text-white px-5 py-2 rounded-2xl hover:brightness-110 transition-all">
                Revisar →
            </a>
        </div>
    @endif

    {{-- Revenue Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <div class="lg:col-span-1 bg-primary/5 border-2 border-primary/20 rounded-3xl p-6">
            <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-2">Avui</p>
            <p class="font-alumni text-h3 text-primary">{{ number_format($stats['revenue_today'], 2, ',', '.') }} €</p>
        </div>

        <div class="lg:col-span-1 bg-secondary/5 border-2 border-secondary/20 rounded-3xl p-6">
            <p class="font-outfit text-xs font-semibold tracking-widest text-secondary uppercase mb-2">Aquest mes</p>
            <p class="font-alumni text-h3 text-secondary">{{ number_format($stats['revenue_month'], 2, ',', '.') }} €</p>
        </div>

        <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm p-6">
            <p class="font-outfit text-xs font-semibold tracking-widest text-gray-400 uppercase mb-2">Facturació total</p>
            <p class="font-alumni text-h2 text-dark">{{ number_format($stats['revenue_total'], 2, ',', '.') }} €</p>
        </div>
    </div>

    {{-- Operational Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-8">

        <div class="bg-white rounded-3xl shadow-sm p-5">
            <p class="font-outfit text-xs text-gray-400 mb-1">Usuaris</p>
            <p class="font-alumni text-h3 text-dark">{{ $stats['total_users'] }}</p>
            <div class="flex gap-2 mt-2 flex-wrap">
                @if($stats['pending_users'] > 0)
                    <span class="inline-block bg-yellow-50 text-yellow-700 font-outfit text-xs px-2 py-0.5 rounded-full">
                        {{ $stats['pending_users'] }} pendents
                    </span>
                @endif
                <span class="inline-block bg-green-50 text-green-700 font-outfit text-xs px-2 py-0.5 rounded-full">
                    {{ $stats['approved_users'] }} aprovats
                </span>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-5">
            <p class="font-outfit text-xs text-gray-400 mb-1">Productes</p>
            <p class="font-alumni text-h3 text-dark">{{ $stats['total_products'] }}</p>
            <div class="flex gap-2 mt-2 flex-wrap">
                <span class="inline-block bg-green-50 text-green-700 font-outfit text-xs px-2 py-0.5 rounded-full">
                    {{ $stats['active_products'] }} actius
                </span>
                @if($stats['low_stock_products'] > 0)
                    <span class="inline-block bg-orange-50 text-orange-700 font-outfit text-xs px-2 py-0.5 rounded-full">
                        {{ $stats['low_stock_products'] }} stock baix
                    </span>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-5">
            <p class="font-outfit text-xs text-gray-400 mb-1">Comandes</p>
            <p class="font-alumni text-h3 text-dark">{{ $stats['total_orders'] }}</p>
            <div class="flex gap-2 mt-2 flex-wrap">
                @if($stats['orders_pending'] > 0)
                    <span class="inline-block bg-yellow-50 text-yellow-700 font-outfit text-xs px-2 py-0.5 rounded-full">
                        {{ $stats['orders_pending'] }} pendents
                    </span>
                @endif
                @if($stats['orders_shipped'] > 0)
                    <span class="inline-block bg-indigo-50 text-indigo-700 font-outfit text-xs px-2 py-0.5 rounded-full">
                        {{ $stats['orders_shipped'] }} enviades
                    </span>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-5">
            <p class="font-outfit text-xs text-gray-400 mb-1">Pressupostos</p>
            <p class="font-alumni text-h3 text-dark">{{ $stats['total_quotations'] }}</p>
            <div class="flex gap-2 mt-2 flex-wrap">
                @if($stats['quotations_pending'] > 0)
                    <span class="inline-block bg-yellow-50 text-yellow-700 font-outfit text-xs px-2 py-0.5 rounded-full">
                        {{ $stats['quotations_pending'] }} pendents
                    </span>
                @endif
                @if($stats['quotations_reviewing'] > 0)
                    <span class="inline-block bg-blue-50 text-blue-700 font-outfit text-xs px-2 py-0.5 rounded-full">
                        {{ $stats['quotations_reviewing'] }} en revisió
                    </span>
                @endif
            </div>
        </div>

        <div class="{{ ($stats['print_jobs_ordered'] + $stats['print_jobs_in_production']) > 0 ? 'bg-orange-50 border border-orange-200' : 'bg-white' }} rounded-3xl shadow-sm p-5">
            <p class="font-outfit text-xs {{ ($stats['print_jobs_ordered'] + $stats['print_jobs_in_production']) > 0 ? 'text-orange-500' : 'text-gray-400' }} mb-1">Impressió</p>
            <p class="font-alumni text-h3 {{ ($stats['print_jobs_ordered'] + $stats['print_jobs_in_production']) > 0 ? 'text-orange-700' : 'text-dark' }}">
                {{ $stats['print_jobs_ordered'] + $stats['print_jobs_in_production'] }}
            </p>
            <div class="flex gap-2 mt-2 flex-wrap">
                @if($stats['print_jobs_ordered'] > 0)
                    <span class="inline-block bg-orange-100 text-orange-700 font-outfit text-xs px-2 py-0.5 rounded-full">
                        {{ $stats['print_jobs_ordered'] }} nous
                    </span>
                @endif
                @if($stats['print_jobs_in_production'] > 0)
                    <span class="inline-block bg-blue-50 text-blue-700 font-outfit text-xs px-2 py-0.5 rounded-full">
                        {{ $stats['print_jobs_in_production'] }} en prod.
                    </span>
                @endif
                @if($stats['print_jobs_ordered'] + $stats['print_jobs_in_production'] === 0)
                    <span class="inline-block bg-green-50 text-green-700 font-outfit text-xs px-2 py-0.5 rounded-full">
                        Al dia
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Revenue Chart + Top Products --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Revenue Bar Chart (last 6 months) --}}
        <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm p-6">
            <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-6">
                Facturació — últims 6 mesos
            </p>
            @php
                $maxRevenue = $monthlyRevenue->max('revenue') ?: 1;
            @endphp
            <div class="flex items-end gap-3 h-40">
                @foreach($monthlyRevenue as $m)
                    @php
                        $pct = $maxRevenue > 0 ? round(($m['revenue'] / $maxRevenue) * 100) : 0;
                        $isCurrentMonth = ($m['year'] == now()->year && $m['month'] == now()->translatedFormat('M'));
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <p class="font-outfit text-xs text-gray-500">
                            {{ $m['revenue'] > 0 ? number_format($m['revenue'], 0, ',', '.') : '—' }}
                        </p>
                        <div class="w-full rounded-t-lg transition-all duration-500
                                    {{ $isCurrentMonth ? 'bg-secondary' : 'bg-primary/30' }}"
                             style="height: {{ max(4, $pct) }}%; min-height: 4px; max-height: 100%">
                        </div>
                        <p class="font-outfit text-xs {{ $isCurrentMonth ? 'text-secondary font-semibold' : 'text-gray-400' }}">
                            {{ $m['month'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Top Products --}}
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase">Top 5 productes</p>
            </div>
            @if($topProducts->isEmpty())
                <div class="px-6 py-10 text-center">
                    <p class="font-outfit text-sm text-gray-400">Sense dades</p>
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($topProducts as $i => $item)
                        <div class="px-6 py-3 flex items-center gap-3">
                            <span class="font-alumni text-h5 {{ $i === 0 ? 'text-secondary' : 'text-gray-300' }} w-5 shrink-0">
                                {{ $i + 1 }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="font-outfit text-xs font-medium text-dark truncate">
                                    {{ $item->product?->getTranslation('name', 'ca') ?? '—' }}
                                </p>
                                <p class="font-outfit text-xs text-gray-400">
                                    {{ $item->units_sold }} u. · {{ number_format($item->revenue, 2, ',', '.') }} €
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-8">
        <a href="{{ route('admin.users.pending') }}"
           class="flex items-center gap-3 bg-yellow-50 border border-yellow-200 rounded-2xl px-4 py-3 hover:shadow-sm transition-shadow">
            <span class="text-xl">👥</span>
            <span class="font-alumni text-sm-header text-yellow-800">Aprovació usuaris</span>
        </a>
        <a href="{{ route('admin.products.create') }}"
           class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-2xl px-4 py-3 hover:shadow-sm transition-shadow">
            <span class="text-xl">➕</span>
            <span class="font-alumni text-sm-header text-green-800">Nou producte</span>
        </a>
        <a href="{{ route('admin.orders.index') }}"
           class="flex items-center gap-3 bg-blue-50 border border-blue-200 rounded-2xl px-4 py-3 hover:shadow-sm transition-shadow">
            <span class="text-xl">📦</span>
            <span class="font-alumni text-sm-header text-blue-800">Comandes</span>
        </a>
        <a href="{{ route('admin.quotations.index') }}"
           class="flex items-center gap-3 bg-purple-50 border border-purple-200 rounded-2xl px-4 py-3 hover:shadow-sm transition-shadow">
            <span class="text-xl">📋</span>
            <span class="font-alumni text-sm-header text-purple-800">Pressupostos</span>
        </a>
        <a href="{{ route('admin.print.jobs.index') }}"
           class="flex items-center gap-3 {{ $stats['print_jobs_ordered'] > 0 ? 'bg-orange-50 border border-orange-200' : 'bg-gray-50 border border-gray-200' }} rounded-2xl px-4 py-3 hover:shadow-sm transition-shadow">
            <span class="text-xl">🖨️</span>
            <span class="font-alumni text-sm-header {{ $stats['print_jobs_ordered'] > 0 ? 'text-orange-800' : 'text-gray-700' }}">Treballs impressió</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Recent Orders --}}
        <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase">Comandes recents</p>
                <a href="{{ route('admin.orders.index') }}"
                   class="font-outfit text-xs text-secondary hover:text-primary transition-colors">Veure totes →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-light border-b border-gray-100">
                        <tr>
                            <th class="font-outfit text-xs text-gray-400 text-left px-6 py-3">Nº</th>
                            <th class="font-outfit text-xs text-gray-400 text-left px-6 py-3">Client</th>
                            <th class="font-outfit text-xs text-gray-400 text-left px-6 py-3">Estat</th>
                            <th class="font-outfit text-xs text-gray-400 text-right px-6 py-3">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($recentOrders as $order)
                            <tr class="hover:bg-light transition-colors">
                                <td class="px-6 py-3">
                                    <a href="{{ route('admin.orders.show', $order->order_number) }}"
                                       class="font-alumni text-sm-header text-secondary hover:text-primary transition-colors">
                                        #{{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-3">
                                    <p class="font-outfit text-xs text-dark">{{ $order->user->name }}</p>
                                    <p class="font-outfit text-xs text-gray-400">{{ $order->user->company_name }}</p>
                                </td>
                                <td class="px-6 py-3">
                                    <span class="inline-block font-outfit text-xs px-2 py-0.5 rounded-full {{ $order->status_color }}">
                                        {{ __('app.status_' . $order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <span class="font-alumni text-sm-header text-dark">
                                        {{ number_format($order->total, 2, ',', '.') }} €
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pending Users --}}
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase">Usuaris pendents</p>
                <a href="{{ route('admin.users.pending') }}"
                   class="font-outfit text-xs text-secondary hover:text-primary transition-colors">Veure tots →</a>
            </div>
            @if($pendingUsers->isEmpty())
                <div class="px-6 py-10 text-center">
                    <p class="text-3xl mb-2">✅</p>
                    <p class="font-outfit text-sm text-gray-400">Cap usuari pendent</p>
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($pendingUsers as $u)
                        <div class="px-6 py-4">
                            <p class="font-alumni text-sm-header text-dark">{{ $u->name }}</p>
                            <p class="font-outfit text-xs text-gray-400 mb-3">{{ $u->company_name }} · {{ $u->email }}</p>
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('admin.users.approve', $u->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="font-outfit text-xs bg-green-50 text-green-700 px-3 py-1.5 rounded-xl hover:bg-green-100 transition-colors">
                                        ✅ Aprovar
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.reject', $u->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="font-outfit text-xs bg-red-50 text-red-600 px-3 py-1.5 rounded-xl hover:bg-red-100 transition-colors">
                                        ❌ Rebutjar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- Recent Quotations --}}
    <div class="mt-8 bg-white rounded-3xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase">Pressupostos recents</p>
            <a href="{{ route('admin.quotations.index') }}"
               class="font-outfit text-xs text-secondary hover:text-primary transition-colors">Veure tots →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-light border-b border-gray-100">
                    <tr>
                        <th class="font-outfit text-xs text-gray-400 text-left px-6 py-3">Nº</th>
                        <th class="font-outfit text-xs text-gray-400 text-left px-6 py-3">Client</th>
                        <th class="font-outfit text-xs text-gray-400 text-left px-6 py-3">Data</th>
                        <th class="font-outfit text-xs text-gray-400 text-left px-6 py-3">Estat</th>
                        <th class="font-outfit text-xs text-gray-400 text-right px-6 py-3">Total</th>
                        <th class="font-outfit text-xs text-gray-400 text-center px-6 py-3">Acció</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($recentQuotations as $quote)
                        <tr class="hover:bg-light transition-colors {{ $quote->status === 'pending' ? 'bg-yellow-50/30' : '' }}">
                            <td class="px-6 py-3">
                                <span class="font-alumni text-sm-header text-dark">#{{ $quote->quote_number }}</span>
                            </td>
                            <td class="px-6 py-3">
                                <p class="font-outfit text-xs text-dark">{{ $quote->user->name }}</p>
                                <p class="font-outfit text-xs text-gray-400">{{ $quote->user->company_name }}</p>
                            </td>
                            <td class="px-6 py-3">
                                <span class="font-outfit text-xs text-gray-500">{{ $quote->created_at->format('d/m/Y') }}</span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="inline-block font-outfit text-xs px-2 py-0.5 rounded-full {{ $quote->status_color }}">
                                    {{ __('app.quote_status_' . $quote->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <span class="font-alumni text-sm-header text-dark">
                                    {{ $quote->total_quoted ? number_format($quote->total_quoted, 2, ',', '.') . ' €' : '—' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <a href="{{ route('admin.quotations.show', $quote->quote_number) }}"
                                   class="font-outfit text-xs text-secondary hover:text-primary transition-colors underline">
                                    Gestionar
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
