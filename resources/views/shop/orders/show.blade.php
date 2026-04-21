@extends('layouts.app')
@section('title', __('app.order_number') . ' #' . $order->order_number)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-20">

    {{-- Back + Header --}}
    <div class="mb-10">
        <a href="{{ route('orders.index') }}"
           class="font-outfit text-xs text-gray-400 hover:text-primary transition-colors mb-4 inline-flex items-center gap-1">
            ← {{ __('app.orders') }}
        </a>
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="font-alumni text-h1 text-dark leading-tight">
                    {{ __('app.order_number') }}
                    <span class="text-secondary">#{{ $order->order_number }}</span>
                </h1>
                <p class="font-alumni text-h5 text-primary mt-1">
                    {{ $order->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
            @php
                $statusColors = [
                    'pending'    => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                    'confirmed'  => 'bg-blue-50 text-blue-700 border-blue-200',
                    'processing' => 'bg-purple-50 text-purple-700 border-purple-200',
                    'shipped'    => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                    'delivered'  => 'bg-green-50 text-green-700 border-green-200',
                    'cancelled'  => 'bg-red-50 text-red-600 border-red-200',
                ];
                $color = $statusColors[$order->status] ?? 'bg-gray-50 text-gray-600 border-gray-200';
            @endphp
            <span class="inline-block font-alumni text-sm-header border px-4 py-2 rounded-2xl {{ $color }}">
                {{ __('app.status_' . $order->status) }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 px-5 py-4 bg-green-50 border border-green-200 rounded-2xl font-outfit text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 px-5 py-4 bg-red-50 border border-red-200 rounded-2xl font-outfit text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Bank Transfer Instructions (shown when unpaid + not cancelled) --}}
    @if($order->payment_status === 'unpaid' && $order->status !== 'cancelled')
        <div class="mb-6 bg-amber-50 border border-amber-200 rounded-3xl p-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 bg-amber-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-alumni text-h5 text-amber-800 mb-1">{{ __('app.bank_transfer_instructions') }}</p>
                    <p class="font-outfit text-sm text-amber-700 mb-4">{{ __('app.bank_transfer_hint') }}</p>
                    <div class="bg-white rounded-2xl p-4 space-y-2 mb-4">
                        <div class="flex justify-between gap-2">
                            <span class="font-outfit text-xs text-gray-400">{{ __('app.bank_beneficiary') }}</span>
                            <span class="font-outfit text-sm text-dark font-medium">{{ config('bank.beneficiary') }}</span>
                        </div>
                        <div class="flex justify-between gap-2">
                            <span class="font-outfit text-xs text-gray-400">IBAN</span>
                            <span class="font-outfit text-sm text-dark font-mono font-medium">{{ config('bank.iban') }}</span>
                        </div>
                        <div class="flex justify-between gap-2">
                            <span class="font-outfit text-xs text-gray-400">BIC / SWIFT</span>
                            <span class="font-outfit text-sm text-dark font-mono font-medium">{{ config('bank.bic') }}</span>
                        </div>
                        <div class="flex justify-between gap-2">
                            <span class="font-outfit text-xs text-gray-400">{{ __('app.bank_name') }}</span>
                            <span class="font-outfit text-sm text-dark font-medium">{{ config('bank.bank_name') }}</span>
                        </div>
                        <div class="pt-2 border-t border-gray-100 flex justify-between gap-2">
                            <span class="font-outfit text-xs text-gray-400">{{ __('app.payment_reference_label') }}</span>
                            <span class="font-alumni text-sm-header text-primary">{{ $order->order_number }}</span>
                        </div>
                        <div class="flex justify-between gap-2">
                            <span class="font-outfit text-xs text-gray-400">{{ __('app.amount_to_transfer') }}</span>
                            <span class="font-alumni text-h5 text-primary">{{ number_format($order->total, 2, ',', '.') }} €</span>
                        </div>
                    </div>

                    {{-- Payment reference submission --}}
                    @if($order->payment_reference)
                        <div class="bg-green-50 border border-green-200 rounded-2xl px-4 py-3 flex items-center gap-3">
                            <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <p class="font-outfit text-sm text-green-700">
                                {{ __('app.payment_reference_received') }}: <strong>{{ $order->payment_reference }}</strong>
                            </p>
                        </div>
                    @else
                        <form method="POST" action="{{ route('orders.payment-reference', $order->order_number) }}"
                              class="flex gap-3">
                            @csrf
                            <input type="text" name="payment_reference"
                                   placeholder="{{ __('app.payment_reference_placeholder') }}"
                                   class="flex-1 bg-white border border-amber-200 rounded-2xl px-4 py-2.5
                                          font-outfit text-sm focus:outline-none focus:border-amber-400 transition-colors" />
                            <button type="submit"
                                    class="font-alumni text-sm-header bg-amber-600 text-white px-5 py-2.5 rounded-2xl
                                           hover:brightness-110 active:scale-95 transition-all whitespace-nowrap">
                                {{ __('app.confirm_payment') }}
                            </button>
                        </form>
                        @error('payment_reference')
                            <p class="font-outfit text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Paid confirmation --}}
    @if($order->payment_status === 'paid')
        <div class="mb-6 px-5 py-4 bg-green-50 border border-green-200 rounded-2xl flex items-center gap-3">
            <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="font-outfit text-sm text-green-700 font-medium">{{ __('app.payment_confirmed') }}</p>
            @if($order->payment_confirmed_at)
                <span class="font-outfit text-xs text-green-600 ml-auto">{{ $order->payment_confirmed_at->format('d/m/Y') }}</span>
            @endif
        </div>
    @endif

    {{-- Tracking info (if available) --}}
    @if($order->tracking_number)
        <div class="mb-6 bg-indigo-50 border border-indigo-200 rounded-3xl p-5">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-outfit text-xs text-indigo-600 font-semibold uppercase tracking-widest mb-0.5">
                        {{ __('app.tracking_number') }}
                    </p>
                    <p class="font-alumni text-h6 text-indigo-800">{{ $order->tracking_number }}</p>
                </div>
                @if($order->tracking_url)
                    <a href="{{ $order->tracking_url }}" target="_blank"
                       class="font-alumni text-sm-header text-indigo-600 border border-indigo-300
                              px-4 py-2 rounded-xl hover:bg-indigo-100 transition-colors">
                        {{ __('app.track_shipment') }} →
                    </a>
                @endif
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Items --}}
        <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase">{{ __('app.order_details') }}</p>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($order->items as $item)
                @php
                    $isPrintJob = ($item->product_snapshot['type'] ?? null) === 'print_job';
                    $printJobId = $item->product_snapshot['print_job_id'] ?? null;
                    $printJob   = $printJobId ? \App\Models\PrintJob::find($printJobId) : null;
                @endphp
                @if($isPrintJob)
                    {{-- Print job item --}}
                    <div class="px-6 py-5">
                        <div class="flex items-start gap-4 mb-3">
                            <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center flex-shrink-0">
                                <span class="text-2xl">🖨️</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="font-alumni text-h6 text-dark">
                                        {{ $item->product_snapshot['name'] ?? 'Treball d\'impressió' }}
                                    </p>
                                    <span class="bg-primary/10 text-primary font-outfit text-xs px-2 py-0.5 rounded-full">Impressió</span>
                                    @if($printJob)
                                        @php
                                            $jobBadge = match($printJob->status) {
                                                'ordered'       => 'bg-yellow-50 text-yellow-700',
                                                'in_production' => 'bg-orange-50 text-orange-700',
                                                'completed'     => 'bg-green-50 text-green-700',
                                                'cancelled'     => 'bg-red-50 text-red-600',
                                                default         => 'bg-gray-100 text-gray-500',
                                            };
                                            $jobLabel = match($printJob->status) {
                                                'ordered'       => 'Pendent de producció',
                                                'in_production' => 'En producció',
                                                'completed'     => 'Completat',
                                                'cancelled'     => 'Cancel·lat',
                                                default         => $printJob->status,
                                            };
                                        @endphp
                                        <span class="font-outfit text-xs px-2 py-0.5 rounded-full {{ $jobBadge }}">
                                            {{ $jobLabel }}
                                        </span>
                                    @endif
                                </div>
                                <p class="font-outfit text-xs text-gray-400 mt-1">
                                    × {{ $item->quantity }} ut.
                                    @if($printJob?->production_days)
                                        · {{ $printJob->production_days }} dies producció
                                    @endif
                                    @if($printJob?->expected_delivery_at)
                                        · Lliurament estimat: <strong>{{ $printJob->expected_delivery_at->format('d/m/Y') }}</strong>
                                    @endif
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="font-alumni text-h6 text-dark">{{ number_format($item->total, 2, ',', '.') }} €</p>
                                <p class="font-outfit text-xs text-gray-400">{{ number_format($item->unit_price, 4, ',', '.') }} € / ut.</p>
                            </div>
                        </div>

                        {{-- Config summary --}}
                        @if(!empty($item->product_snapshot['configuration_labels']))
                            <div class="ml-18 flex flex-wrap gap-2 mb-3 pl-[4.5rem]">
                                @foreach($item->product_snapshot['configuration_labels'] as $label => $value)
                                    <span class="bg-light font-outfit text-xs text-gray-600 px-3 py-1 rounded-lg">
                                        <span class="text-gray-400">{{ $label }}:</span> {{ $value }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Artwork + notes + cancel (while ordered/in_production) --}}
                        @if($printJob && in_array($printJob->status, ['ordered', 'in_production']) && $printJob->user_id === Auth::id())
                            <div class="pl-[4.5rem] space-y-3">

                                {{-- Artwork confirmation + delete --}}
                                @if($printJob->artwork_path)
                                    <div class="flex items-center gap-3 bg-green-50 border border-green-100 rounded-xl px-4 py-2.5">
                                        <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <p class="font-outfit text-xs text-green-700 flex-1">
                                            Arxiu de disseny carregat: <span class="font-semibold">{{ basename($printJob->artwork_path) }}</span>
                                        </p>
                                        <form method="POST"
                                              action="{{ route('print.jobs.artwork.delete', $printJob) }}"
                                              onsubmit="return confirm('Eliminar l\'arxiu de disseny?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="font-outfit text-xs text-red-400 hover:text-red-600 transition-colors">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                @endif

                                {{-- Upload / replace artwork --}}
                                <form method="POST"
                                      action="{{ route('print.jobs.artwork', $printJob) }}"
                                      enctype="multipart/form-data"
                                      class="flex items-end gap-3">
                                    @csrf
                                    <div class="flex-1">
                                        <label class="block font-outfit text-xs text-gray-400 mb-1">
                                            {{ $printJob->artwork_path ? 'Substituir arxiu de disseny' : 'Carregar arxiu de disseny' }}
                                            <span class="text-gray-300">(PDF, AI, EPS, SVG, PNG — màx. 50MB)</span>
                                        </label>
                                        <input type="file" name="artwork"
                                               accept=".pdf,.ai,.eps,.svg,.png,.jpg,.jpeg,.tiff,.psd"
                                               class="w-full text-xs font-outfit text-gray-600
                                                      file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                                      file:text-xs file:font-semibold file:bg-primary/10 file:text-primary
                                                      hover:file:bg-primary/20 transition">
                                    </div>
                                    <button type="submit"
                                            class="bg-primary text-white font-outfit text-xs px-4 py-2 rounded-xl
                                                   hover:brightness-110 transition-all whitespace-nowrap">
                                        Enviar
                                    </button>
                                </form>

                                {{-- Artwork notes --}}
                                <form method="POST" action="{{ route('print.jobs.notes', $printJob) }}">
                                    @csrf @method('PATCH')
                                    <label class="block font-outfit text-xs text-gray-400 mb-1">
                                        Notes de producció
                                        <span class="text-gray-300">(instruccions especials, acabats, etc.)</span>
                                    </label>
                                    <div class="flex gap-3">
                                        <textarea name="artwork_notes" rows="2"
                                                  placeholder="Afegeix qualsevol indicació per al taller…"
                                                  class="flex-1 border border-gray-200 rounded-xl px-3 py-2
                                                         font-outfit text-xs focus:outline-none focus:ring-2 focus:ring-primary resize-none">{{ old('artwork_notes', $printJob->artwork_notes) }}</textarea>
                                        <button type="submit"
                                                class="self-end bg-light border border-gray-200 text-dark font-outfit text-xs
                                                       px-4 py-2 rounded-xl hover:bg-primary/10 transition-all whitespace-nowrap">
                                            Guardar
                                        </button>
                                    </div>
                                </form>

                                {{-- Cancel — only while ordered (not yet in production) --}}
                                @if($printJob->status === 'ordered')
                                    <div x-data="{ openCancel: false }">
                                        <button @click="openCancel = true"
                                                class="font-outfit text-xs text-red-400 hover:text-red-600 transition-colors">
                                            Cancel·lar aquest treball d'impressió
                                        </button>
                                        <div x-show="openCancel" x-cloak
                                             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
                                             @keydown.escape.window="openCancel = false">
                                            <div class="bg-white rounded-3xl shadow-xl max-w-sm w-full mx-4 p-8" @click.stop>
                                                <h3 class="font-alumni text-h5 text-dark mb-2">Cancel·lar treball d'impressió?</h3>
                                                <p class="font-outfit text-sm text-gray-500 mb-6">
                                                    Aquesta acció no es pot desfer. El treball quedarà cancel·lat.
                                                </p>
                                                <div class="flex gap-3 justify-end">
                                                    <button type="button" @click="openCancel = false"
                                                            class="font-alumni text-sm-header border border-gray-200 text-gray-600
                                                                   px-5 py-2.5 rounded-2xl hover:bg-gray-50 transition-colors">
                                                        Tornar
                                                    </button>
                                                    <form method="POST" action="{{ route('print.jobs.cancel', $printJob) }}">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                                class="font-alumni text-sm-header bg-red-500 text-white
                                                                       px-5 py-2.5 rounded-2xl hover:brightness-110 transition-all">
                                                            Sí, cancel·lar
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        @endif

                        {{-- Received confirmation (completed, owned by user) --}}
                        @if($printJob && $printJob->status === 'completed' && $printJob->user_id === Auth::id())
                            <div class="pl-[4.5rem]">
                                @if($printJob->received_at)
                                    <div class="flex items-center gap-2 font-outfit text-xs text-green-600">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Recepció confirmada el {{ $printJob->received_at->format('d/m/Y') }}
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('print.jobs.received', $printJob) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="flex items-center gap-2 font-outfit text-xs bg-green-50 border border-green-200
                                                       text-green-700 px-4 py-2 rounded-xl hover:bg-green-100 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Confirmar recepció del treball
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                @else
                    {{-- Regular product item --}}
                    <div class="px-6 py-4 flex items-center gap-4">
                        <div class="w-14 h-14 bg-light rounded-2xl flex items-center justify-center flex-shrink-0">
                            <span class="text-2xl">📦</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-alumni text-h6 text-dark">
                                {{ $item->product_snapshot['name'] ?? $item->product?->getTranslation('name', app()->getLocale()) }}
                            </p>
                            <p class="font-outfit text-xs text-gray-400">
                                {{ $item->product_snapshot['sku'] ?? '' }} · × {{ $item->quantity }} {{ $item->product_snapshot['unit'] ?? '' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="font-alumni text-h6 text-dark">{{ number_format($item->total, 2, ',', '.') }} €</p>
                            <p class="font-outfit text-xs text-gray-400">{{ number_format($item->unit_price, 2, ',', '.') }} € / unit</p>
                        </div>
                    </div>
                @endif
                @endforeach
            </div>
            <div class="px-6 py-5 bg-light space-y-2">
                <div class="flex justify-between">
                    <span class="font-outfit text-xs text-gray-500">{{ __('app.subtotal') }}</span>
                    <span class="font-outfit text-sm text-dark">{{ number_format($order->subtotal, 2, ',', '.') }} €</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-outfit text-xs text-gray-500">{{ __('app.vat') }}</span>
                    <span class="font-outfit text-sm text-dark">{{ number_format($order->vat_amount, 2, ',', '.') }} €</span>
                </div>
                <div class="flex justify-between pt-3 border-t border-gray-200">
                    <span class="font-alumni text-h6 text-dark">{{ __('app.total') }}</span>
                    <span class="font-alumni text-h4 text-primary">{{ number_format($order->total, 2, ',', '.') }} €</span>
                </div>
            </div>
        </div>

        {{-- Right --}}
        <div class="space-y-5">

            <div class="bg-white rounded-3xl shadow-sm p-6">
                <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-4">
                    {{ __('app.shipping_address') }}
                </p>
                <div class="space-y-1">
                    @if(!empty($order->shipping_address['contact_name']))
                        <p class="font-alumni text-sm-header text-dark">{{ $order->shipping_address['contact_name'] }}</p>
                    @endif
                    <p class="font-outfit text-sm text-gray-600">{{ $order->shipping_address['address'] ?? '—' }}</p>
                    <p class="font-outfit text-sm text-gray-600">{{ $order->shipping_address['city'] ?? '' }} {{ $order->shipping_address['postal_code'] ?? '' }}</p>
                    <p class="font-outfit text-sm text-gray-600">{{ $order->shipping_address['country'] ?? '' }}</p>
                    @if(!empty($order->shipping_address['phone']))
                        <p class="font-outfit text-xs text-gray-400">{{ $order->shipping_address['phone'] }}</p>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm p-6">
                <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-4">
                    {{ __('app.billing_address') }}
                </p>
                <div class="space-y-1">
                    <p class="font-alumni text-sm-header text-dark">{{ $order->billing_address['company_name'] ?? '—' }}</p>
                    <p class="font-outfit text-sm text-gray-500">{{ __('app.cif') }}: {{ $order->billing_address['cif'] ?? '—' }}</p>
                    <p class="font-outfit text-sm text-gray-500">{{ $order->billing_address['address'] ?? '' }}, {{ $order->billing_address['city'] ?? '' }}</p>
                    <p class="font-outfit text-sm text-gray-500">{{ $order->billing_address['postal_code'] ?? '' }} · {{ $order->billing_address['country'] ?? '' }}</p>
                </div>
            </div>

            @if($order->invoice)
                <div class="bg-white rounded-3xl shadow-sm p-6">
                    <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-4">
                        {{ __('app.invoices') }}
                    </p>
                    <p class="font-outfit text-sm text-gray-500 mb-4">#{{ $order->invoice->invoice_number }}</p>
                    <a href="{{ route('invoices.download', $order->invoice->id) }}"
                       class="flex items-center justify-center gap-2 w-full bg-secondary text-white
                              font-alumni text-sm-header py-3 rounded-2xl hover:brightness-110
                              active:scale-95 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        {{ __('app.download_invoice') }}
                    </a>
                </div>
            @endif

            {{-- Re-order --}}
            <div class="bg-white rounded-3xl shadow-sm p-6">
                <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-4">
                    {{ __('app.reorder') }}
                </p>
                <form method="POST" action="{{ route('orders.reorder', $order->order_number) }}">
                    @csrf
                    <button type="submit"
                            class="flex items-center justify-center gap-2 w-full border border-primary text-primary
                                   font-alumni text-sm-header py-3 rounded-2xl hover:bg-primary hover:text-white
                                   active:scale-95 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        {{ __('app.reorder') }}
                    </button>
                </form>
            </div>

            {{-- Cancel Order --}}
            @if($order->is_cancellable)
                <div class="bg-white rounded-3xl shadow-sm p-6 border border-red-100" x-data="{ open: false }">
                    <p class="font-outfit text-xs font-semibold tracking-widest text-red-500 uppercase mb-4">
                        {{ __('app.cancel_order') }}
                    </p>
                    <button @click="open = true"
                            class="flex items-center justify-center gap-2 w-full border border-red-300 text-red-600
                                   font-alumni text-sm-header py-3 rounded-2xl hover:bg-red-50
                                   active:scale-95 transition-all">
                        {{ __('app.cancel_order') }}
                    </button>

                    <div x-show="open" x-cloak
                         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
                         @keydown.escape.window="open = false">
                        <div class="bg-white rounded-3xl shadow-xl max-w-sm w-full mx-4 p-8" @click.stop>
                            <h3 class="font-alumni text-h4 text-dark mb-3">{{ __('app.cancel_order') }}</h3>
                            <p class="font-outfit text-sm text-gray-500 mb-6">{{ __('app.cancel_order_confirm') }}</p>
                            <div class="flex gap-3 justify-end">
                                <button type="button" @click="open = false"
                                        class="font-alumni text-sm-header border border-gray-200 text-gray-600
                                               px-6 py-3 rounded-2xl hover:bg-gray-50 transition-colors">
                                    {{ __('app.back') }}
                                </button>
                                <form method="POST" action="{{ route('orders.cancel', $order->order_number) }}">
                                    @csrf
                                    <button type="submit"
                                            class="font-alumni text-sm-header bg-red-500 text-white
                                                   px-6 py-3 rounded-2xl hover:brightness-110 active:scale-95 transition-all">
                                        {{ __('app.confirm_cancel') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($order->notes)
                <div class="bg-white rounded-3xl shadow-sm p-6">
                    <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-3">
                        {{ __('app.notes') }}
                    </p>
                    <p class="font-outfit text-sm text-gray-600">{{ $order->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
