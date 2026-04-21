@extends('layouts.app')
@section('title', __('app.orders'))

@section('content')

{{-- Hero --}}
<div class="text-center pt-16 pb-12 px-4">
    <h1 class="font-alumni text-h1 text-dark leading-tight mb-4">
        {{ __('app.orders_title_1') }}
        <span class="text-secondary">{{ __('app.orders_title_2') }}</span>
    </h1>
    <p class="font-alumni text-h5 text-primary max-w-xl mx-auto">
        {{ __('app.orders_subtitle') }}
    </p>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 pb-20">
    @if($orders->isEmpty())
        <div class="bg-white rounded-3xl shadow-sm p-16 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <h2 class="font-alumni text-h4 text-dark mb-3">{{ __('app.no_orders') }}</h2>
            <a href="{{ route('products.index') }}"
               class="inline-flex items-center gap-2 bg-primary text-white font-alumni text-sm-header
                      px-8 py-3 rounded-2xl hover:brightness-110 active:scale-95 transition-all">
                {{ __('app.browse_products') }}
            </a>
        </div>
    @else
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase text-left px-6 py-4">
                                {{ __('app.order_number') }}
                            </th>
                            <th class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase text-left px-6 py-4">
                                {{ __('app.date') }}
                            </th>
                            <th class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase text-left px-6 py-4">
                                {{ __('app.status') }}
                            </th>
                            <th class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase text-right px-6 py-4">
                                {{ __('app.total') }}
                            </th>
                            <th class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase text-center px-6 py-4">
                                {{ __('app.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($orders as $order)
                            @php
                                $statusColors = [
                                    'pending'    => 'bg-yellow-50 text-yellow-700',
                                    'confirmed'  => 'bg-blue-50 text-blue-700',
                                    'processing' => 'bg-purple-50 text-purple-700',
                                    'shipped'    => 'bg-indigo-50 text-indigo-700',
                                    'delivered'  => 'bg-green-50 text-green-700',
                                    'cancelled'  => 'bg-red-50 text-red-600',
                                ];
                                $color = $statusColors[$order->status] ?? 'bg-gray-50 text-gray-600';
                            @endphp
                            <tr class="hover:bg-light transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-alumni text-sm-header text-dark">#{{ $order->order_number }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-outfit text-sm text-gray-500">{{ $order->created_at->format('d/m/Y') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-block font-outfit text-xs px-3 py-1 rounded-full {{ $color }}">
                                        {{ __('app.status_' . $order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-alumni text-h6 text-dark">{{ number_format($order->total, 2, ',', '.') }} €</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <a href="{{ route('orders.show', $order->order_number) }}"
                                           class="font-outfit text-xs font-semibold text-secondary hover:text-primary transition-colors">
                                            {{ __('app.view') }} →
                                        </a>
                                        <form method="POST" action="{{ route('orders.reorder', $order->order_number) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="font-outfit text-xs font-semibold text-gray-400 hover:text-primary transition-colors"
                                                    title="{{ __('app.reorder') }}">
                                                ↺
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($orders->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">{{ $orders->links() }}</div>
            @endif
        </div>
    @endif
</div>
@endsection
