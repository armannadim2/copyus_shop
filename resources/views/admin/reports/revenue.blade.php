@extends('layouts.admin')
@section('title', 'Revenue Report')

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-alumni text-h2 text-dark">📈 Revenue Report</h1>
            <p class="font-outfit text-body-lg text-gray-500 mt-1">
                Detailed revenue breakdown by month and year
            </p>
        </div>
        {{-- Year Filter --}}
        <form method="GET" action="{{ route('admin.reports.revenue') }}"
              class="flex items-center gap-3">
            <select name="year"
                    onchange="this.form.submit()"
                    class="border border-gray-200 rounded-xl px-4 py-2
                           font-outfit text-body-lg focus:outline-none
                           focus:ring-2 focus:ring-primary">
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Year Totals --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($yearlyTotals as $yt)
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100
                    {{ $yt->year == $year ? 'ring-2 ring-primary' : '' }}">
            <p class="font-outfit text-body-sm text-gray-400 mb-1">{{ $yt->year }}</p>
            <p class="font-alumni text-h3 text-dark">€{{ number_format($yt->revenue, 2) }}</p>
            <p class="font-outfit text-body-sm text-gray-400 mt-1">{{ $yt->orders }} orders</p>
        </div>
        @endforeach
    </div>

    {{-- Monthly Chart --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-alumni text-h4 text-dark mb-5">
            Monthly Revenue — {{ $year }}
        </h2>
        <canvas id="monthlyChart" height="80"></canvas>
    </div>

    {{-- Monthly Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-alumni text-h4 text-dark">Monthly Breakdown — {{ $year }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-alumni text-sm-header text-dark">Month</th>
                        <th class="px-6 py-3 text-right font-alumni text-sm-header text-dark">Orders</th>
                        <th class="px-6 py-3 text-right font-alumni text-sm-header text-dark">Subtotal</th>
                        <th class="px-6 py-3 text-right font-alumni text-sm-header text-dark">VAT</th>
                        <th class="px-6 py-3 text-right font-alumni text-sm-header text-dark">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @php $grandTotal = 0; $grandOrders = 0; @endphp
                    @foreach($months as $m)
                    @php
                        $grandTotal  += $m['revenue'];
                        $grandOrders += $m['orders'];
                    @endphp
                    <tr class="{{ $m['orders'] === 0 ? 'opacity-40' : 'hover:bg-gray-50' }}
                               transition-colors">
                        <td class="px-6 py-4 font-outfit text-body-lg text-dark">{{ $m['label'] }}</td>
                        <td class="px-6 py-4 font-outfit text-body-lg text-dark text-right">
                            {{ $m['orders'] }}
                        </td>
                        <td class="px-6 py-4 font-outfit text-body-lg text-dark text-right">
                            €{{ number_format($m['subtotal'], 2) }}
                        </td>
                        <td class="px-6 py-4 font-outfit text-body-lg text-gray-500 text-right">
                                                        €{{ number_format($m['vat'], 2) }}
                        </td>
                        <td class="px-6 py-4 font-alumni text-body-lg text-dark text-right font-semibold">
                            €{{ number_format($m['revenue'], 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                    <tr>
                        <td class="px-6 py-4 font-alumni text-sm-header text-dark">TOTAL {{ $year }}</td>
                        <td class="px-6 py-4 font-alumni text-sm-header text-dark text-right">
                            {{ $grandOrders }}
                        </td>
                        <td class="px-6 py-4 font-alumni text-sm-header text-dark text-right">—</td>
                        <td class="px-6 py-4 font-alumni text-sm-header text-dark text-right">—</td>
                        <td class="px-6 py-4 font-alumni text-sm-header text-dark text-right">
                            €{{ number_format($grandTotal, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Export Button --}}
    <div class="flex justify-end">
        <a href="{{ route('admin.reports.export.orders', ['from' => $year.'-01-01', 'to' => $year.'-12-31']) }}"
           class="btn-primary flex items-center gap-2">
            ⬇️ Export {{ $year }} Orders CSV
        </a>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels   = @json(collect($months)->pluck('label'));
const revenue  = @json(collect($months)->pluck('revenue'));
const subtotal = @json(collect($months)->pluck('subtotal'));
const vat      = @json(collect($months)->pluck('vat'));

const ctx = document.getElementById('monthlyChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Subtotal (€)',
                data: subtotal,
                backgroundColor: 'rgba(99, 102, 241, 0.6)',
                borderRadius: 6,
                stack: 'revenue',
            },
            {
                label: 'VAT (€)',
                data: vat,
                backgroundColor: 'rgba(167, 139, 250, 0.5)',
                borderRadius: 6,
                stack: 'revenue',
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: (ctx) =>
                        ` ${ctx.dataset.label}: €${ctx.parsed.y.toLocaleString('ca-ES', { minimumFractionDigits: 2 })}`
                }
            }
        },
        scales: {
            x: { stacked: true },
            y: {
                stacked: true,
                ticks: {
                    callback: val => '€' + val.toLocaleString('ca-ES')
                },
                grid: { color: 'rgba(0,0,0,0.04)' }
            }
        }
    }
});
</script>
@endpush

