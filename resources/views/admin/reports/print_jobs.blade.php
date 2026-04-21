@extends('layouts.admin')
@section('title', 'Report Treballs d\'Impressió')

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-alumni text-h2 text-dark">🖨️ Treballs d'Impressió</h1>
            <p class="font-outfit text-body-lg text-gray-500 mt-1">
                Volum de producció, facturació i rendiment per plantilla
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.reports.index') }}" class="btn-outline text-sm">← Reports</a>
            <a href="{{ route('admin.reports.export.print-jobs') }}"
               class="inline-flex items-center gap-2 font-alumni text-sm-header bg-primary text-white
                      px-5 py-2.5 rounded-2xl hover:brightness-110 transition-all">
                ⬇ Exportar CSV
            </a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-8 gap-4">

        <div class="xl:col-span-2 bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="font-outfit text-body-sm text-gray-400 mb-1">Total treballs</p>
            <p class="font-alumni text-h3 text-dark">{{ number_format($kpis['total_jobs']) }}</p>
        </div>

        <div class="xl:col-span-2 bg-primary/5 rounded-2xl p-5 shadow-sm border border-primary/20">
            <p class="font-outfit text-body-sm text-primary mb-1">Facturació total</p>
            <p class="font-alumni text-h3 text-primary">{{ number_format($kpis['total_revenue'], 2, ',', '.') }} €</p>
        </div>

        <div class="bg-yellow-50 rounded-2xl p-5 shadow-sm border border-yellow-100">
            <p class="font-outfit text-body-sm text-yellow-600 mb-1">Rebuts</p>
            <p class="font-alumni text-h3 text-yellow-700">{{ $kpis['jobs_ordered'] }}</p>
        </div>

        <div class="bg-orange-50 rounded-2xl p-5 shadow-sm border border-orange-100">
            <p class="font-outfit text-body-sm text-orange-600 mb-1">En producció</p>
            <p class="font-alumni text-h3 text-orange-700">{{ $kpis['jobs_production'] }}</p>
        </div>

        <div class="bg-green-50 rounded-2xl p-5 shadow-sm border border-green-100">
            <p class="font-outfit text-body-sm text-green-600 mb-1">Completats</p>
            <p class="font-alumni text-h3 text-green-700">{{ $kpis['jobs_completed'] }}</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="font-outfit text-body-sm text-gray-400 mb-1">Quantitat mitjana</p>
            <p class="font-alumni text-h3 text-dark">{{ number_format($kpis['avg_quantity']) }} u.</p>
        </div>

    </div>

    {{-- Alerts row --}}
    @if($kpis['no_artwork'] > 0)
        <div class="bg-red-50 border border-red-200 rounded-2xl px-5 py-4 flex items-center gap-3">
            <span class="text-red-500 text-xl">⚠️</span>
            <p class="font-outfit text-sm text-red-800">
                <strong>{{ $kpis['no_artwork'] }}</strong> {{ $kpis['no_artwork'] === 1 ? 'treball' : 'treballs' }}
                en curs sense arxiu de disseny.
                <a href="{{ route('admin.print.jobs.index') }}" class="underline hover:text-red-900">Gestionar →</a>
            </p>
        </div>
    @endif

    {{-- Monthly Chart + Status breakdown --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Monthly volume chart --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-alumni text-h4 text-dark mb-5">Volum mensual — últims 6 mesos</h2>
            <canvas id="monthlyChart" height="100"></canvas>
        </div>

        {{-- Status breakdown --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-alumni text-h4 text-dark mb-5">Estat dels treballs</h2>
            <div class="space-y-3">
                @foreach([
                    ['key' => 'jobs_ordered',    'label' => 'Rebuts',       'class' => 'bg-yellow-400'],
                    ['key' => 'jobs_production', 'label' => 'En producció', 'class' => 'bg-orange-400'],
                    ['key' => 'jobs_completed',  'label' => 'Completats',   'class' => 'bg-green-400'],
                    ['key' => 'jobs_cancelled',  'label' => 'Cancel·lats',  'class' => 'bg-red-400'],
                ] as $row)
                    @php
                        $total = max(1, $kpis['total_jobs'] + $kpis['jobs_cancelled']);
                        $pct = $total > 0 ? round($kpis[$row['key']] / $total * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="font-outfit text-xs text-gray-600">{{ $row['label'] }}</span>
                            <span class="font-alumni text-sm-header text-dark">{{ $kpis[$row['key']] }}</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-2 {{ $row['class'] }} rounded-full transition-all duration-700"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach

                @if($avgProductionDays !== null)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="font-outfit text-xs text-gray-400 mb-1">Temps de producció mitjà</p>
                        <p class="font-alumni text-h4 text-dark">
                            {{ round($avgProductionDays, 1) }} dies
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Revenue by template --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-alumni text-h4 text-dark">Facturació per plantilla</h2>
            <a href="{{ route('admin.print.templates.index') }}"
               class="font-outfit text-body-sm text-secondary hover:text-primary transition-colors">
                Gestionar plantilles →
            </a>
        </div>

        @if($byTemplate->isEmpty())
            <div class="px-6 py-10 text-center">
                <p class="font-outfit text-sm text-gray-400">Sense dades de producció.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-light border-b border-gray-100">
                        <tr>
                            <th class="font-outfit text-xs text-gray-400 text-left px-6 py-3">Plantilla</th>
                            <th class="font-outfit text-xs text-gray-400 text-right px-6 py-3">Treballs</th>
                            <th class="font-outfit text-xs text-gray-400 text-right px-6 py-3">Unitats</th>
                            <th class="font-outfit text-xs text-gray-400 text-right px-6 py-3">Facturació</th>
                            <th class="font-outfit text-xs text-gray-400 text-right px-6 py-3">% del total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @php $totalRevenue = max(1, $byTemplate->sum('revenue')); @endphp
                        @foreach($byTemplate as $row)
                            @php $pct = round($row->revenue / $totalRevenue * 100, 1); @endphp
                            <tr class="hover:bg-light transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-outfit text-sm font-medium text-dark">
                                                {{ $row->template?->getTranslation('name', 'ca') ?? '—' }}
                                            </p>
                                            <p class="font-outfit text-xs text-gray-400">
                                                {{ $row->template?->slug ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-alumni text-sm-header text-dark">{{ $row->job_count }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-alumni text-sm-header text-dark">
                                        {{ number_format($row->total_units, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-alumni text-sm-header text-primary">
                                        {{ number_format($row->revenue, 2, ',', '.') }} €
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-1.5 bg-primary rounded-full" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <span class="font-outfit text-xs text-gray-500 w-10 text-right">{{ $pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light border-t-2 border-gray-200">
                        <tr>
                            <td class="px-6 py-3 font-outfit text-xs font-semibold text-gray-600">Total</td>
                            <td class="px-6 py-3 text-right font-alumni text-sm-header text-dark">
                                {{ $byTemplate->sum('job_count') }}
                            </td>
                            <td class="px-6 py-3 text-right font-alumni text-sm-header text-dark">
                                {{ number_format($byTemplate->sum('total_units'), 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3 text-right font-alumni text-sm-header text-primary">
                                {{ number_format($byTemplate->sum('revenue'), 2, ',', '.') }} €
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

    {{-- Top clients --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="font-alumni text-h4 text-dark">Top clients per volum d'impressió</h2>
        </div>
        @if($topClients->isEmpty())
            <div class="px-6 py-10 text-center">
                <p class="font-outfit text-sm text-gray-400">Sense dades.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-light border-b border-gray-100">
                        <tr>
                            <th class="font-outfit text-xs text-gray-400 text-left px-6 py-3">#</th>
                            <th class="font-outfit text-xs text-gray-400 text-left px-6 py-3">Client</th>
                            <th class="font-outfit text-xs text-gray-400 text-right px-6 py-3">Treballs</th>
                            <th class="font-outfit text-xs text-gray-400 text-right px-6 py-3">Unitats</th>
                            <th class="font-outfit text-xs text-gray-400 text-right px-6 py-3">Facturació</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($topClients as $i => $client)
                            <tr class="hover:bg-light transition-colors">
                                <td class="px-6 py-3">
                                    <span class="font-alumni text-h5 {{ $i === 0 ? 'text-secondary' : 'text-gray-300' }}">
                                        {{ $i + 1 }}
                                    </span>
                                </td>
                                <td class="px-6 py-3">
                                    <p class="font-outfit text-sm font-medium text-dark">
                                        {{ $client->user?->company_name ?: $client->user?->name ?? '—' }}
                                    </p>
                                    <p class="font-outfit text-xs text-gray-400">{{ $client->user?->name }}</p>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <span class="font-alumni text-sm-header text-dark">{{ $client->job_count }}</span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <span class="font-alumni text-sm-header text-dark">
                                        {{ number_format($client->total_units, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <span class="font-alumni text-sm-header text-primary">
                                        {{ number_format($client->revenue, 2, ',', '.') }} €
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const months  = @json($monthlyData->pluck('month'));
const counts  = @json($monthlyData->pluck('count'));
const revenue = @json($monthlyData->pluck('revenue'));

const ctx = document.getElementById('monthlyChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: months,
        datasets: [
            {
                label: 'Facturació (€)',
                data: revenue,
                backgroundColor: 'rgba(234, 88, 12, 0.15)',
                borderColor: 'rgba(234, 88, 12, 1)',
                borderWidth: 2,
                borderRadius: 6,
                yAxisID: 'y',
            },
            {
                label: 'Treballs',
                data: counts,
                type: 'line',
                borderColor: 'rgba(99, 102, 241, 0.8)',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                borderWidth: 2,
                pointRadius: 4,
                fill: false,
                tension: 0.4,
                yAxisID: 'y1',
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: (ctx) => ctx.datasetIndex === 0
                        ? ` €${ctx.parsed.y.toLocaleString('ca-ES', {minimumFractionDigits: 2})}`
                        : ` ${ctx.parsed.y} treballs`
                }
            }
        },
        scales: {
            y: {
                type: 'linear',
                position: 'left',
                ticks: { callback: val => '€' + val.toLocaleString('ca-ES') },
                grid: { color: 'rgba(0,0,0,0.04)' }
            },
            y1: {
                type: 'linear',
                position: 'right',
                grid: { drawOnChartArea: false },
                ticks: { stepSize: 1 }
            }
        }
    }
});
</script>
@endpush
