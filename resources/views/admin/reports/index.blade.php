@extends('layouts.admin')
@section('title', 'Reports Dashboard')

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-alumni text-h2 text-dark">📊 Reports & Analytics</h1>
            <p class="font-outfit text-body-lg text-gray-500 mt-1">
                Overview of sales, clients and performance
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.reports.revenue') }}"
               class="btn-outline">📈 Revenue</a>
            <a href="{{ route('admin.reports.products') }}"
               class="btn-outline">📦 Products</a>
            <a href="{{ route('admin.reports.clients') }}"
               class="btn-outline">👥 Clients</a>
            <a href="{{ route('admin.reports.print-jobs') }}"
               class="btn-outline">🖨️ Impressió</a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 col-span-2 md:col-span-1">
            <p class="font-outfit text-body-sm text-gray-400 mb-1">Total Revenue</p>
            <p class="font-alumni text-h3 text-dark">
                €{{ number_format($kpis['total_revenue'], 2) }}
            </p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="font-outfit text-body-sm text-gray-400 mb-1">Total Orders</p>
            <p class="font-alumni text-h3 text-dark">{{ number_format($kpis['total_orders']) }}</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="font-outfit text-body-sm text-gray-400 mb-1">Avg Order Value</p>
            <p class="font-alumni text-h3 text-dark">€{{ number_format($kpis['avg_order_value'], 2) }}</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="font-outfit text-body-sm text-gray-400 mb-1">Clients</p>
            <p class="font-alumni text-h3 text-dark">{{ $kpis['total_clients'] }}</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="font-outfit text-body-sm text-gray-400 mb-1">Pending Approvals</p>
            <p class="font-alumni text-h3 text-yellow-600">{{ $kpis['pending_approvals'] }}</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="font-outfit text-body-sm text-gray-400 mb-1">Open Quotations</p>
            <p class="font-alumni text-h3 text-purple-600">{{ $kpis['open_quotations'] }}</p>
        </div>

    </div>

    {{-- Revenue Chart --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-alumni text-h4 text-dark mb-5">📈 Monthly Revenue (Last 12 Months)</h2>
        <canvas id="revenueChart" height="90"></canvas>
    </div>

    {{-- Two Column --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Top Products --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-alumni text-h4 text-dark">📦 Top Products by Revenue</h2>
                <a href="{{ route('admin.reports.products') }}"
                   class="font-outfit text-body-sm text-secondary hover:text-primary">
                    View all →
                </a>
            </div>
            <div class="space-y-3">
                @forelse($topProducts as $item)
                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                    <div>
                        <p class="font-outfit text-body-lg text-dark font-medium">
                            {{ $item->product?->brand ?? '—' }}
                            <span class="text-gray-400 text-sm">({{ $item->product?->sku ?? '—' }})</span>
                        </p>
                        <p class="font-outfit text-body-sm text-gray-400">
                            {{ number_format($item->units_sold) }} units sold
                        </p>
                    </div>
                    <p class="font-alumni text-h5 text-dark">
                        €{{ number_format($item->revenue, 2) }}
                    </p>
                </div>
                @empty
                <p class="font-outfit text-body-lg text-gray-400 text-center py-4">
                    No sales data yet.
                </p>
                @endforelse
            </div>
        </div>

        {{-- Top Clients --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-alumni text-h4 text-dark">👥 Top Clients by Spend</h2>
                <a href="{{ route('admin.reports.clients') }}"
                   class="font-outfit text-body-sm text-secondary hover:text-primary">
                    View all →
                </a>
            </div>
            <div class="space-y-3">
                @forelse($topClients as $client)
                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                    <div>
                        <p class="font-outfit text-body-lg text-dark font-medium">
                            {{ $client->user?->company_name ?? $client->user?->name ?? '—' }}
                        </p>
                        <p class="font-outfit text-body-sm text-gray-400">
                            {{ $client->order_count }} orders
                        </p>
                    </div>
                    <p class="font-alumni text-h5 text-dark">
                        €{{ number_format($client->total_spend, 2) }}
                    </p>
                </div>
                @empty
                <p class="font-outfit text-body-lg text-gray-400 text-center py-4">
                    No client data yet.
                </p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Order Status Breakdown --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-alumni text-h4 text-dark mb-5">🔢 Orders by Status</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach(['pending','confirmed','processing','shipped','delivered','cancelled'] as $status)
            <div class="text-center p-4 rounded-xl
                {{ match($status) {
                    'pending'    => 'bg-yellow-50',
                    'confirmed'  => 'bg-blue-50',
                    'processing' => 'bg-purple-50',
                    'shipped'    => 'bg-indigo-50',
                    'delivered'  => 'bg-green-50',
                    'cancelled'  => 'bg-red-50',
                } }}">
                <p class="font-alumni text-h3 text-dark">
                    {{ $ordersByStatus[$status] ?? 0 }}
                </p>
                <p class="font-outfit text-body-sm text-gray-500 capitalize mt-1">{{ $status }}</p>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels  = @json($monthlyRevenue->pluck('label'));
const revenue = @json($monthlyRevenue->pluck('revenue'));
const orders  = @json($monthlyRevenue->pluck('order_count'));

const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Revenue (€)',
                data: revenue,
                backgroundColor: 'rgba(99, 102, 241, 0.15)',
                borderColor: 'rgba(99, 102, 241, 1)',
                borderWidth: 2,
                borderRadius: 6,
                yAxisID: 'y',
            },
            {
                label: 'Orders',
                data: orders,
                type: 'line',
                borderColor: 'rgba(234, 88, 12, 0.8)',
                backgroundColor: 'rgba(234, 88, 12, 0.1)',
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
                        : ` ${ctx.parsed.y} orders`
                }
            }
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                ticks: {
                    callback: val => '€' + val.toLocaleString('ca-ES')
                },
                grid: { color: 'rgba(0,0,0,0.04)' }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                grid: { drawOnChartArea: false },
                ticks: { stepSize: 1 }
            }
        }
    }
});
</script>
@endpush
