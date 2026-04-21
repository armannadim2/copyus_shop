@extends('layouts.admin')
@section('title', 'Products Report')

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="font-alumni text-h2 text-dark">📦 Products Report</h1>
            <p class="font-outfit text-body-lg text-gray-500 mt-1">
                Top products by revenue and units sold
            </p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Sort Filter --}}
            <form method="GET" action="{{ route('admin.reports.products') }}"
                  class="flex items-center gap-2">
                <label class="font-outfit text-body-sm text-gray-500">Sort by:</label>
                <select name="sort"
                        onchange="this.form.submit()"
                        class="border border-gray-200 rounded-xl px-4 py-2
                               font-outfit text-body-lg focus:outline-none
                               focus:ring-2 focus:ring-primary">
                    <option value="revenue" {{ $sortBy === 'revenue' ? 'selected' : '' }}>Revenue</option>
                    <option value="units"   {{ $sortBy === 'units'   ? 'selected' : '' }}>Units Sold</option>
                </select>
            </form>
        </div>
    </div>

    {{-- Category Revenue Doughnut --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:col-span-1">
            <h2 class="font-alumni text-h4 text-dark mb-4">Revenue by Category</h2>
            <canvas id="categoryChart"></canvas>
        </div>

        {{-- Top 5 Summary Cards --}}
        <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4 content-start">
            @foreach($topProducts->take(4) as $index => $item)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex gap-4 items-center">
                <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center
                            font-alumni text-h4 text-primary shrink-0">
                    {{ $index + 1 }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-outfit text-body-lg text-dark font-medium truncate">
                        {{ $item->product?->brand ?? '—' }}
                    </p>
                    <p class="font-outfit text-body-sm text-gray-400">
                        SKU: {{ $item->product?->sku ?? '—' }}
                    </p>
                </div>
                <div class="text-right shrink-0">
                    <p class="font-alumni text-h5 text-dark">
                        €{{ number_format($item->revenue, 2) }}
                    </p>
                    <p class="font-outfit text-body-sm text-gray-400">
                        {{ number_format($item->units_sold) }} units
                    </p>
                </div>
            </div>
            @endforeach
        </div>

    </div>

    {{-- Full Products Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-alumni text-h4 text-dark">All Products Performance</h2>
            <span class="font-outfit text-body-sm text-gray-400">
                {{ $topProducts->total() }} products
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-alumni text-sm-header text-dark">#</th>
                        <th class="px-6 py-3 text-left font-alumni text-sm-header text-dark">SKU</th>
                        <th class="px-6 py-3 text-left font-alumni text-sm-header text-dark">Brand</th>
                        <th class="px-6 py-3 text-right font-alumni text-sm-header text-dark">Avg Price</th>
                        <th class="px-6 py-3 text-right font-alumni text-sm-header text-dark">Units Sold</th>
                        <th class="px-6 py-3 text-right font-alumni text-sm-header text-dark">Times Ordered</th>
                        <th class="px-6 py-3 text-right font-alumni text-sm-header text-dark">Revenue</th>
                        <th class="px-6 py-3 text-center font-alumni text-sm-header text-dark">Stock</th>
                        <th class="px-6 py-3 text-center font-alumni text-sm-header text-dark">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($topProducts as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-outfit text-body-sm text-gray-400">
                            {{ ($topProducts->currentPage() - 1) * $topProducts->perPage() + $index + 1 }}
                        </td>
                        <td class="px-6 py-4 font-outfit text-body-sm text-gray-500 font-mono">
                            {{ $item->product?->sku ?? '—' }}
                        </td>
                        <td class="px-6 py-4 font-outfit text-body-lg text-dark font-medium">
                            {{ $item->product?->brand ?? '—' }}
                        </td>
                        <td class="px-6 py-4 font-outfit text-body-lg text-dark text-right">
                            €{{ number_format($item->avg_price, 2) }}
                        </td>
                        <td class="px-6 py-4 font-outfit text-body-lg text-dark text-right">
                            {{ number_format($item->units_sold) }}
                        </td>
                        <td class="px-6 py-4 font-outfit text-body-lg text-dark text-right">
                            {{ number_format($item->times_ordered) }}
                        </td>
                        <td class="px-6 py-4 font-alumni text-body-lg text-dark text-right font-semibold">
                            €{{ number_format($item->revenue, 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-outfit text-body-sm px-2 py-1 rounded-lg
                                {{ ($item->product?->stock ?? 0) > 0
                                    ? 'bg-green-50 text-green-700'
                                    : 'bg-red-50 text-red-600' }}">
                                {{ number_format($item->product?->stock ?? 0) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-outfit text-body-sm px-2 py-1 rounded-lg
                                {{ $item->product?->is_active
                                    ? 'bg-green-50 text-green-700'
                                    : 'bg-gray-100 text-gray-400' }}">
                                {{ $item->product?->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center font-outfit
                                               text-body-lg text-gray-400">
                            No product sales data available yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($topProducts->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $topProducts->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const categoryLabels  = @json($categoryRevenue->pluck('category_name'));
const categoryRevenue = @json($categoryRevenue->pluck('revenue'));

const palette = [
    'rgba(99,102,241,0.8)',
    'rgba(167,139,250,0.8)',
    'rgba(234,88,12,0.8)',
    'rgba(16,185,129,0.8)',
    'rgba(245,158,11,0.8)',
    'rgba(59,130,246,0.8)',
];

new Chart(document.getElementById('categoryChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: categoryLabels,
        datasets: [{
            data: categoryRevenue,
            backgroundColor: palette,
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { padding: 16 } },
            tooltip: {
                callbacks: {
                    label: (ctx) =>
                        ` €${ctx.parsed.toLocaleString('ca-ES', { minimumFractionDigits: 2 })}`
                }
            }
        },
        cutout: '65%',
    }
});
</script>
@endpush
