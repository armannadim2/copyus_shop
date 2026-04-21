@extends('layouts.app')
@section('title', __('app.invoices'))

@section('content')

{{-- Hero --}}
<div class="text-center pt-16 pb-12 px-4">
    <h1 class="font-alumni text-h1 text-dark leading-tight mb-4">
        {{ __('app.invoices_title_1') }}
        <span class="text-secondary">{{ __('app.invoices_title_2') }}</span>
    </h1>
    <p class="font-alumni text-h5 text-primary max-w-xl mx-auto">
        {{ __('app.invoices_subtitle') }}
    </p>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 pb-20">
    @if($invoices->isEmpty())
        <div class="bg-white rounded-3xl shadow-sm p-16 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <h2 class="font-alumni text-h4 text-dark mb-3">{{ __('app.no_invoices') }}</h2>
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
                            <th class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase text-left px-6 py-4">{{ __('app.invoice_number') }}</th>
                            <th class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase text-left px-6 py-4">{{ __('app.order_number') }}</th>
                            <th class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase text-left px-6 py-4">{{ __('app.invoice_date') }}</th>
                            <th class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase text-left px-6 py-4">{{ __('app.status') }}</th>
                            <th class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase text-right px-6 py-4">{{ __('app.total') }}</th>
                            <th class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase text-center px-6 py-4">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($invoices as $invoice)
                            @php
                                $statusColors = [
                                    'issued'    => 'bg-blue-50 text-blue-700',
                                    'paid'      => 'bg-green-50 text-green-700',
                                    'overdue'   => 'bg-red-50 text-red-600',
                                    'cancelled' => 'bg-gray-100 text-gray-500',
                                ];
                                $color = $statusColors[$invoice->status] ?? 'bg-gray-50 text-gray-600';
                            @endphp
                            <tr class="hover:bg-light transition-colors">
                                <td class="px-6 py-4"><span class="font-alumni text-sm-header text-dark">#{{ $invoice->invoice_number }}</span></td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('orders.show', $invoice->order->order_number) }}"
                                       class="font-outfit text-sm text-secondary hover:text-primary transition-colors">
                                        #{{ $invoice->order->order_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4"><span class="font-outfit text-sm text-gray-500">{{ \Carbon\Carbon::parse($invoice->issued_at)->format('d/m/Y') }}</span></td>
                                <td class="px-6 py-4">
                                    <span class="inline-block font-outfit text-xs px-3 py-1 rounded-full {{ $color }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right"><span class="font-alumni text-h6 text-dark">{{ number_format($invoice->total, 2, ',', '.') }} €</span></td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('invoices.download', $invoice->id) }}"
                                       class="inline-flex items-center gap-1.5 font-outfit text-xs font-semibold
                                              text-secondary hover:text-primary transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        {{ __('app.download_invoice') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($invoices->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">{{ $invoices->links() }}</div>
            @endif
        </div>
    @endif
</div>
@endsection
