@extends('layouts.admin')
@section('title', 'Gestió de Comandes')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <h1 class="font-alumni text-h1 text-dark mb-8">Comandes</h1>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.orders.index') }}"
          class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cerca número, client..."
               class="border border-gray-200 rounded-xl px-4 py-2 font-outfit text-body-sm
                      focus:outline-none focus:ring-2 focus:ring-primary w-72">
        <select name="status"
                class="border border-gray-200 rounded-xl px-4 py-2 font-outfit text-body-sm
                       focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">Tots els estats</option>
            @foreach(['pending','confirmed','processing','shipped','delivered','cancelled'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button type="submit"
                class="bg-primary text-white font-outfit text-body-sm px-5 py-2
                       rounded-xl hover:bg-primary/90 transition-colors">
            Filtrar
        </button>
        @if(request('search') || request('status'))
            <a href="{{ route('admin.orders.index') }}"
               class="font-outfit text-body-sm text-gray-500 px-4 py-2 rounded-xl
                      border border-gray-200 hover:bg-gray-50 transition-colors">
                Netejar
            </a>
        @endif
    </form>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left font-outfit text-body-sm text-gray-500 px-6 py-3">Número</th>
                    <th class="text-left font-outfit text-body-sm text-gray-500 px-6 py-3">Client</th>
                    <th class="text-left font-outfit text-body-sm text-gray-500 px-6 py-3">Data</th>
                    <th class="text-left font-outfit text-body-sm text-gray-500 px-6 py-3">Estat</th>
                    <th class="text-right font-outfit text-body-sm text-gray-500 px-6 py-3">Total</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @php
                    $statusColors = [
                        'pending'    => 'bg-yellow-50 text-yellow-700',
                        'confirmed'  => 'bg-blue-50 text-blue-700',
                        'processing' => 'bg-purple-50 text-purple-700',
                        'shipped'    => 'bg-indigo-50 text-indigo-700',
                        'delivered'  => 'bg-green-50 text-green-700',
                        'cancelled'  => 'bg-red-50 text-red-600',
                    ];
                @endphp
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-alumni text-sm-header text-dark">
                            {{ $order->order_number }}
                        </td>
                        <td class="px-6 py-4 font-outfit text-body-sm text-gray-600">
                            {{ $order->user?->name }}<br>
                            <span class="text-gray-400 text-xs">{{ $order->user?->company_name }}</span>
                        </td>
                        <td class="px-6 py-4 font-outfit text-body-sm text-gray-500">
                            {{ $order->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-block font-outfit text-body-sm px-2 py-0.5 rounded-full
                                         {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-500' }}">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-alumni text-sm-header text-right">
                            {{ number_format($order->total, 2, ',', '.') }} €
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.orders.show', $order->order_number) }}"
                               class="font-outfit text-body-sm text-secondary hover:text-primary transition-colors">
                                Veure →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center font-outfit text-body-lg text-gray-400">
                            No s'han trobat comandes.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>

</div>
@endsection
