@extends('layouts.admin')
@section('title', 'Comanda ' . $order->order_number)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.orders.index') }}"
           class="font-outfit text-body-sm text-gray-500 hover:text-primary transition-colors">
            ← Comandes
        </a>
        <h1 class="font-alumni text-h1 text-dark">{{ $order->order_number }}</h1>
        @php
            $statusColors = [
                'pending'    => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                'confirmed'  => 'bg-blue-50 text-blue-700 border-blue-200',
                'processing' => 'bg-purple-50 text-purple-700 border-purple-200',
                'shipped'    => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                'delivered'  => 'bg-green-50 text-green-700 border-green-200',
                'cancelled'  => 'bg-red-50 text-red-600 border-red-200',
            ];
        @endphp
        <span class="font-alumni text-sm-header border px-3 py-1.5 rounded-xl
                     {{ $statusColors[$order->status] ?? 'bg-gray-50 text-gray-500 border-gray-200' }}">
            {{ ucfirst($order->status) }}
        </span>
        @if($order->payment_status === 'paid')
            <span class="font-alumni text-sm-header border border-green-200 bg-green-50 text-green-700 px-3 py-1.5 rounded-xl">
                Pagat ✓
            </span>
        @else
            <span class="font-alumni text-sm-header border border-amber-200 bg-amber-50 text-amber-700 px-3 py-1.5 rounded-xl">
                Pendent de pagament
            </span>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-6 px-5 py-4 bg-green-50 border border-green-200 rounded-2xl font-outfit text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Order Info --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">
            <h2 class="font-alumni text-h4 text-dark mb-4">Detalls del client</h2>
            <dl class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="font-outfit text-body-sm text-gray-400">Client</dt>
                    <dd class="font-outfit text-body-sm text-dark">{{ $order->user?->name }}</dd>
                </div>
                <div>
                    <dt class="font-outfit text-body-sm text-gray-400">Empresa</dt>
                    <dd class="font-outfit text-body-sm text-dark">{{ $order->user?->company_name }}</dd>
                </div>
                <div>
                    <dt class="font-outfit text-body-sm text-gray-400">Email</dt>
                    <dd class="font-outfit text-body-sm text-dark">{{ $order->user?->email }}</dd>
                </div>
                <div>
                    <dt class="font-outfit text-body-sm text-gray-400">Data</dt>
                    <dd class="font-outfit text-body-sm text-dark">{{ $order->created_at->format('d/m/Y H:i') }}</dd>
                </div>
                <div class="col-span-2">
                    <dt class="font-outfit text-body-sm text-gray-400">Adreça d'enviament</dt>
                    <dd class="font-outfit text-body-sm text-dark">
                        @if(!empty($order->shipping_address['contact_name']))
                            {{ $order->shipping_address['contact_name'] }} —
                        @endif
                        {{ $order->shipping_address['address'] ?? '—' }},
                        {{ $order->shipping_address['city'] ?? '' }}
                        {{ $order->shipping_address['postal_code'] ?? '' }},
                        {{ $order->shipping_address['country'] ?? '' }}
                        @if(!empty($order->shipping_address['phone']))
                            · {{ $order->shipping_address['phone'] }}
                        @endif
                    </dd>
                </div>
                @if($order->notes)
                    <div class="col-span-2">
                        <dt class="font-outfit text-body-sm text-gray-400">Notes del client</dt>
                        <dd class="font-outfit text-body-sm text-dark italic">{{ $order->notes }}</dd>
                    </div>
                @endif
                @if($order->payment_reference)
                    <div class="col-span-2">
                        <dt class="font-outfit text-body-sm text-gray-400">Referència de pagament</dt>
                        <dd class="font-outfit text-body-sm text-dark font-medium">{{ $order->payment_reference }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Update Status --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl shadow-sm p-5">
                <h2 class="font-alumni text-h5 text-dark mb-3">Actualitzar Estat</h2>
                <form method="POST" action="{{ route('admin.orders.status', $order->order_number) }}">
                    @csrf @method('PATCH')
                    <select name="status"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2
                                   font-outfit text-body-sm mb-3 focus:outline-none focus:ring-2 focus:ring-primary">
                        @foreach(['pending','confirmed','processing','shipped','delivered','cancelled'] as $s)
                            <option value="{{ $s }}" @selected($order->status === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    <button class="w-full bg-primary text-white font-outfit text-body-sm
                                   py-2 rounded-xl hover:bg-primary/90 transition-colors">
                        Actualitzar estat
                    </button>
                </form>
            </div>

            {{-- Mark as paid --}}
            @if($order->payment_status !== 'paid')
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
                    <h2 class="font-alumni text-h5 text-amber-800 mb-3">Pagament</h2>
                    @if($order->payment_reference)
                        <p class="font-outfit text-body-sm text-amber-700 mb-3">
                            Ref. client: <strong>{{ $order->payment_reference }}</strong>
                        </p>
                    @else
                        <p class="font-outfit text-body-sm text-amber-700 mb-3">
                            Cap referència de pagament rebuda.
                        </p>
                    @endif
                    <form method="POST" action="{{ route('admin.orders.paid', $order->order_number) }}">
                        @csrf @method('PATCH')
                        <button class="w-full bg-amber-600 text-white font-outfit text-body-sm
                                       py-2 rounded-xl hover:brightness-110 transition-colors">
                            Confirmar pagament ✓
                        </button>
                    </form>
                </div>
            @else
                <div class="bg-green-50 border border-green-200 rounded-2xl p-5 text-center">
                    <p class="font-alumni text-h5 text-green-700">Pagament confirmat ✓</p>
                    @if($order->payment_confirmed_at)
                        <p class="font-outfit text-xs text-green-600 mt-1">
                            {{ $order->payment_confirmed_at->format('d/m/Y H:i') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Tracking & Internal Notes --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
        <h2 class="font-alumni text-h4 text-dark mb-4">Enviament i notes internes</h2>
        <form method="POST" action="{{ route('admin.orders.tracking', $order->order_number) }}" class="space-y-4">
            @csrf @method('PATCH')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-outfit text-body-sm text-gray-500 mb-1">Número de seguiment</label>
                    <input type="text" name="tracking_number"
                           value="{{ old('tracking_number', $order->tracking_number) }}"
                           placeholder="Ex: ES123456789ES"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5
                                  font-outfit text-body-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block font-outfit text-body-sm text-gray-500 mb-1">URL de seguiment</label>
                    <input type="url" name="tracking_url"
                           value="{{ old('tracking_url', $order->tracking_url) }}"
                           placeholder="https://..."
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5
                                  font-outfit text-body-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>
            <div>
                <label class="block font-outfit text-body-sm text-gray-500 mb-1">Notes internes (no visibles al client)</label>
                <textarea name="admin_notes" rows="3"
                          placeholder="Anotacions internes, incidències, comunicació interna..."
                          class="w-full border border-gray-200 rounded-xl px-3 py-2.5
                                 font-outfit text-body-sm focus:outline-none focus:ring-2 focus:ring-primary
                                 resize-none">{{ old('admin_notes', $order->admin_notes) }}</textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit"
                        class="bg-primary text-white font-outfit text-body-sm
                               px-6 py-2.5 rounded-xl hover:bg-primary/90 transition-colors">
                    Guardar enviament
                </button>
            </div>
        </form>
    </div>

    {{-- Items --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-alumni text-h4 text-dark">Articles</h2>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left font-outfit text-body-sm text-gray-500 px-6 py-3">Producte</th>
                    <th class="text-right font-outfit text-body-sm text-gray-500 px-6 py-3">Qty</th>
                    <th class="text-right font-outfit text-body-sm text-gray-500 px-6 py-3">Preu unit.</th>
                    <th class="text-right font-outfit text-body-sm text-gray-500 px-6 py-3">IVA</th>
                    <th class="text-right font-outfit text-body-sm text-gray-500 px-6 py-3">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($order->items as $item)
                @php
                    $isPrintJob = ($item->product_snapshot['type'] ?? null) === 'print_job';
                    $printJobId = $item->product_snapshot['print_job_id'] ?? null;
                    $printJob   = $printJobId ? \App\Models\PrintJob::find($printJobId) : null;
                @endphp
                    <tr class="{{ $isPrintJob ? 'bg-primary/5' : '' }}">
                        <td class="px-6 py-3">
                            <div class="flex items-start gap-2">
                                @if($isPrintJob)<span class="text-primary mt-0.5">🖨️</span>@endif
                                <div>
                                    <p class="font-outfit text-body-sm text-dark">
                                        {{ $item->product_snapshot['name'] ?? $item->product?->name ?? 'Producte eliminat' }}
                                    </p>
                                    @if(!empty($item->product_snapshot['sku']))
                                        <span class="font-outfit text-gray-400 text-xs">{{ $item->product_snapshot['sku'] }}</span>
                                    @endif
                                    @if($isPrintJob && !empty($item->product_snapshot['configuration_labels']))
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($item->product_snapshot['configuration_labels'] as $lbl => $val)
                                                <span class="bg-white border border-gray-200 font-outfit text-xs text-gray-500 px-2 py-0.5 rounded">
                                                    {{ $lbl }}: <strong>{{ $val }}</strong>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if($printJob)
                                        <a href="{{ route('admin.print.jobs.show', $printJob) }}"
                                           class="font-outfit text-xs text-primary hover:underline mt-0.5 inline-block">
                                            Ver treball #{{ $printJob->id }} →
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3 font-outfit text-body-sm text-right">{{ $item->quantity }}</td>
                        <td class="px-6 py-3 font-outfit text-body-sm text-right">
                            {{ number_format($item->unit_price, $isPrintJob ? 4 : 2, ',', '.') }} €
                        </td>
                        <td class="px-6 py-3 font-outfit text-body-sm text-right text-gray-500">
                            {{ $item->vat_rate }}%
                        </td>
                        <td class="px-6 py-3 font-outfit text-body-sm text-right">
                            {{ number_format($item->total, 2, ',', '.') }} €
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="border-t border-gray-200 bg-gray-50">
                <tr>
                    <td colspan="4" class="px-6 py-3 font-outfit text-body-sm text-right text-gray-500">Subtotal</td>
                    <td class="px-6 py-3 font-outfit text-body-sm text-right">{{ number_format($order->subtotal, 2, ',', '.') }} €</td>
                </tr>
                <tr>
                    <td colspan="4" class="px-6 py-3 font-outfit text-body-sm text-right text-gray-500">IVA</td>
                    <td class="px-6 py-3 font-outfit text-body-sm text-right">{{ number_format($order->vat_amount, 2, ',', '.') }} €</td>
                </tr>
                <tr>
                    <td colspan="4" class="px-6 py-3 font-alumni text-h6 text-right text-dark">Total</td>
                    <td class="px-6 py-3 font-alumni text-h5 text-right text-dark">
                        {{ number_format($order->total, 2, ',', '.') }} €
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>
@endsection
