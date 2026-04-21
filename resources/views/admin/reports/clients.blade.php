@extends('layouts.admin')
@section('title', 'Clients Report')

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="font-alumni text-h2 text-dark">👥 Clients Report</h1>
            <p class="font-outfit text-body-lg text-gray-500 mt-1">
                Client performance and acquisition overview
            </p>
        </div>
        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('admin.reports.clients') }}"
                  class="flex items-center gap-2">
                <label class="font-outfit text-body-sm text-gray-500">Sort by:</label>
                <select name="sort"
                        onchange="this.form.submit()"
                        class="border border-gray-200 rounded-xl px-4 py-2
                               font-outfit text-body-lg focus:outline-none
                               focus:ring-2 focus:ring-primary">
                    <option value="spend"  {{ $sortBy === 'spend'  ? 'selected' : '' }}>Total Spend</option>
                    <option value="orders" {{ $sortBy === 'orders' ? 'selected' : '' }}>Order Count</option>
                </select>
            </form>
            <a href="{{ route('admin.reports.export.clients') }}"
               class="btn-primary flex items-center gap-2">
                ⬇️ Export CSV
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="font-outfit text-body-sm text-gray-400 mb-1">Approved clients</p>
            <p class="font-alumni text-h3 text-dark">{{ $clients->total() }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="font-outfit text-body-sm text-gray-400 mb-1">Pending Approval</p>
            <p class="font-alumni text-h3 text-yellow-600">{{ $pendingCount }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="font-outfit text-body-sm text-gray-400 mb-1">Rejected</p>
            <p class="font-alumni text-h3 text-red-500">{{ $rejectedCount }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="font-outfit text-body-sm text-gray-400 mb-1">Avg Spend / Client</p>
            <p class="font-alumni text-h3 text-dark">
                @php
                    $avgSpend = $clients->total() > 0
                        ? $clients->sum('total_spend') / $clients->total()
                        : 0;
                @endphp
                €{{ number_format($avgSpend, 2) }}
            </p>
        </div>
    </div>

    {{-- New Clients Chart --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-alumni text-h4 text-dark mb-5">📅 New Clients (Last 12 Months)</h2>
        <canvas id="clientsChart" height="80"></canvas>
    </div>

    {{-- Clients Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-alumni text-h4 text-dark">Client Performance</h2>
            <span class="font-outfit text-body-sm text-gray-400">
                {{ $clients->total() }} clients
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-alumni text-sm-header text-dark">#</th>
                        <th class="px-6 py-3 text-left font-alumni text-sm-header text-dark">Company</th>
                        <th class="px-6 py-3 text-left font-alumni text-sm-header text-dark">Contact</th>
                        <th class="px-6 py-3 text-left font-alumni text-sm-header text-dark">CIF</th>
                        <th class="px-6 py-3 text-left font-alumni text-sm-header text-dark">City</th>
                        <th class="px-6 py-3 text-right font-alumni text-sm-header text-dark">Orders</th>
                        <th class="px-6 py-3 text-right font-alumni text-sm-header text-dark">Total Spend</th>
                        <th class="px-6 py-3 text-left font-alumni text-sm-header text-dark">Last Order</th>
                        <th class="px-6 py-3 text-center font-alumni text-sm-header text-dark">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($clients as $index => $client)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-outfit text-body-sm text-gray-400">
                            {{ ($clients->currentPage() - 1) * $clients->perPage() + $index + 1 }}
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-outfit text-body-lg text-dark font-medium">
                                {{ $client->company_name ?? '—' }}
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-outfit text-body-lg text-dark">{{ $client->name }}</p>
                            <p class="font-outfit text-body-sm text-gray-400">{{ $client->email }}</p>
                        </td>
                        <td class="px-6 py-4 font-outfit text-body-sm text-gray-500 font-mono">
                            {{ $client->cif ?? '—' }}
                        </td>
                        <td class="px-6 py-4 font-outfit text-body-lg text-gray-500">
                            {{ $client->city ?? '—' }}
                        </td>
                        <td class="px-6 py-4 font-outfit text-body-lg text-dark text-right">
                            {{ $client->orders_count }}
                        </td>
                        <td class="px-6 py-4 font-alumni text-body-lg text-dark text-right font-semibold">
                            €{{ number_format($client->total_spend ?? 0, 2) }}
                        </td>
                        <td class="px-6 py-4 font-outfit text-body-sm text-gray-400">
                            {{ $client->last_order_at
                                ? \Carbon\Carbon::parse($client->last_order_at)->format('d/m/Y')
                                : '—' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.users.show', $client->id) }}"
                               class="font-outfit text-body-sm text-secondary hover:text-primary
                                      transition-colors">
                                View →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center font-outfit
                                               text-body-lg text-gray-400">
                            No clients found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($clients->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $clients->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels = @json(collect($newClientsMonthly)->pluck('label'));
const counts = @json(collect($newClientsMonthly)->pluck('count'));

new Chart(document.getElementById('clientsChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'New Clients',
            data: counts,
            backgroundColor: 'rgba(16, 185, 129, 0.2)',
            borderColor: 'rgba(16, 185, 129, 1)',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 },
                grid: { color: 'rgba(0,0,0,0.04)' }
            }
        }
    }
});
</script>
@endpush
