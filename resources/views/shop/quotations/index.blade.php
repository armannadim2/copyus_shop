@extends('layouts.app')
@section('title', __('app.quotation'))

@section('content')

{{-- Hero --}}
<div class="text-center pt-16 pb-12 px-4">
    <h1 class="font-alumni text-h1 text-dark leading-tight mb-4">
        {{ __('app.quotes_title_1') }}
        <span class="text-secondary">{{ __('app.quotes_title_2') }}</span>
    </h1>
    <p class="font-alumni text-h5 text-primary max-w-xl mx-auto">
        {{ __('app.quotes_subtitle') }}
    </p>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 pb-20">
    @if($quotations->isEmpty())
        <div class="bg-white rounded-3xl shadow-sm p-16 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h2 class="font-alumni text-h4 text-dark mb-3">{{ __('app.quote_basket_empty') }}</h2>
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
                            <th class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase text-left px-6 py-4">{{ __('app.quote_number') }}</th>
                            <th class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase text-left px-6 py-4">{{ __('app.date') }}</th>
                            <th class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase text-left px-6 py-4">{{ __('app.status') }}</th>
                            <th class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase text-right px-6 py-4">{{ __('app.total') }}</th>
                            <th class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase text-left px-6 py-4">{{ __('app.valid_until') }}</th>
                            <th class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase text-center px-6 py-4">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($quotations as $quote)
                            @php
                                $statusColors = [
                                    'pending'   => 'bg-yellow-50 text-yellow-700',
                                    'reviewing' => 'bg-blue-50 text-blue-700',
                                    'quoted'    => 'bg-purple-50 text-purple-700',
                                    'accepted'  => 'bg-green-50 text-green-700',
                                    'rejected'  => 'bg-red-50 text-red-600',
                                    'expired'   => 'bg-gray-100 text-gray-500',
                                    'converted' => 'bg-indigo-50 text-indigo-700',
                                ];
                                $color = $statusColors[$quote->status] ?? 'bg-gray-50 text-gray-600';
                            @endphp
                            <tr class="hover:bg-light transition-colors">
                                <td class="px-6 py-4"><span class="font-alumni text-sm-header text-dark">#{{ $quote->quote_number }}</span></td>
                                <td class="px-6 py-4"><span class="font-outfit text-sm text-gray-500">{{ $quote->created_at->format('d/m/Y') }}</span></td>
                                <td class="px-6 py-4">
                                    <span class="inline-block font-outfit text-xs px-3 py-1 rounded-full {{ $color }}">
                                        {{ __('app.quote_status_' . $quote->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-alumni text-h6 text-dark">
                                        {{ $quote->total_quoted ? number_format($quote->total_quoted, 2, ',', '.') . ' €' : '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-outfit text-sm text-gray-500">
                                        {{ $quote->valid_until ? \Carbon\Carbon::parse($quote->valid_until)->format('d/m/Y') : '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('quotations.show', $quote->quote_number) }}"
                                       class="font-outfit text-xs font-semibold text-secondary hover:text-primary transition-colors">
                                        {{ __('app.view') }} →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($quotations->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">{{ $quotations->links() }}</div>
            @endif
        </div>
    @endif
</div>
@endsection
