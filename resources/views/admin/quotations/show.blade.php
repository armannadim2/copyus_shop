@extends('layouts.admin')
@section('title', 'Pressupost ' . $quotation->quote_number)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.quotations.index') }}"
           class="font-outfit text-body-sm text-gray-500 hover:text-primary transition-colors">
            ← Pressupostos
        </a>
        <h1 class="font-alumni text-h1 text-dark">{{ $quotation->quote_number }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Info --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">
            <h2 class="font-alumni text-h4 text-dark mb-4">Detalls</h2>
            <dl class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="font-outfit text-body-sm text-gray-400">Client</dt>
                    <dd class="font-outfit text-body-sm text-dark">{{ $quotation->user?->name }}</dd>
                </div>
                <div>
                    <dt class="font-outfit text-body-sm text-gray-400">Empresa</dt>
                    <dd class="font-outfit text-body-sm text-dark">{{ $quotation->user?->company_name }}</dd>
                </div>
                <div>
                    <dt class="font-outfit text-body-sm text-gray-400">Data</dt>
                    <dd class="font-outfit text-body-sm text-dark">{{ $quotation->created_at->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="font-outfit text-body-sm text-gray-400">Estat</dt>
                    <dd class="font-outfit text-body-sm text-dark">{{ $quotation->status }}</dd>
                </div>
                @if($quotation->valid_until)
                    <div>
                        <dt class="font-outfit text-body-sm text-gray-400">Vàlid fins</dt>
                        <dd class="font-outfit text-body-sm text-dark">
                            {{ \Carbon\Carbon::parse($quotation->valid_until)->format('d/m/Y') }}
                        </dd>
                    </div>
                @endif
                @if($quotation->customer_notes)
                    <div class="col-span-2">
                        <dt class="font-outfit text-body-sm text-gray-400">Notes del client</dt>
                        <dd class="font-outfit text-body-sm text-dark">{{ $quotation->customer_notes }}</dd>
                    </div>
                @endif
                @if($quotation->admin_notes)
                    <div class="col-span-2">
                        <dt class="font-outfit text-body-sm text-gray-400">Notes admin</dt>
                        <dd class="font-outfit text-body-sm text-dark">{{ $quotation->admin_notes }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Actions --}}
        <div class="space-y-4">
            {{-- Update Status --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="font-alumni text-h5 text-dark mb-3">Canviar Estat</h2>
                <form method="POST" action="{{ route('admin.quotations.status', $quotation->quote_number) }}">
                    @csrf @method('PATCH')
                    <select name="status"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2
                                   font-outfit text-body-sm mb-3 focus:outline-none focus:ring-2 focus:ring-primary">
                        @foreach(['pending','reviewing','quoted','accepted','rejected','expired','converted'] as $s)
                            <option value="{{ $s }}" @selected($quotation->status === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    <button class="w-full bg-primary text-white font-outfit text-body-sm
                                   py-2 rounded-xl hover:bg-primary/90 transition-colors">
                        Actualitzar
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Set Prices Form --}}
    @if(in_array($quotation->status, ['pending', 'reviewing']))
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-alumni text-h4 text-dark">Establir Preus</h2>
            </div>
            <form method="POST" action="{{ route('admin.quotations.price', $quotation->quote_number) }}"
                  class="p-6">
                @csrf @method('PATCH')
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="font-outfit text-body-sm text-gray-500 block mb-1">Notes admin</label>
                        <textarea name="admin_notes" rows="2"
                                  class="w-full border border-gray-200 rounded-xl px-3 py-2
                                         font-outfit text-body-sm focus:outline-none focus:ring-2 focus:ring-primary"
                        >{{ old('admin_notes') }}</textarea>
                    </div>
                    <div>
                        <label class="font-outfit text-body-sm text-gray-500 block mb-1">Vàlid fins *</label>
                        <input type="date" name="valid_until" value="{{ old('valid_until') }}"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2
                                      font-outfit text-body-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        @error('valid_until')
                            <p class="text-red-500 font-outfit text-body-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <table class="w-full mb-6">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left font-outfit text-body-sm text-gray-500 px-4 py-2">Producte</th>
                            <th class="text-right font-outfit text-body-sm text-gray-500 px-4 py-2">Qty</th>
                            <th class="text-right font-outfit text-body-sm text-gray-500 px-4 py-2">Preu citat €</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($quotation->items as $i => $item)
                            <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                            <tr>
                                <td class="px-4 py-3 font-outfit text-body-sm">
                                    {{ $item->product?->name ?? 'Producte eliminat' }}
                                </td>
                                <td class="px-4 py-3 font-outfit text-body-sm text-right">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-right">
                                    <input type="number" name="items[{{ $i }}][price]"
                                           value="{{ old("items.{$i}.price", $item->quoted_price) }}"
                                           step="0.01" min="0"
                                           class="w-28 border border-gray-200 rounded-lg px-2 py-1
                                                  font-outfit text-body-sm text-right
                                                  focus:outline-none focus:ring-2 focus:ring-primary">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <button class="bg-primary text-white font-outfit text-body-sm px-6 py-2
                               rounded-xl hover:bg-primary/90 transition-colors">
                    Enviar pressupost al client
                </button>
            </form>
        </div>
    @else
        {{-- Read-only items --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-alumni text-h4 text-dark">Articles</h2>
            </div>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left font-outfit text-body-sm text-gray-500 px-6 py-3">Producte</th>
                        <th class="text-right font-outfit text-body-sm text-gray-500 px-6 py-3">Qty</th>
                        <th class="text-right font-outfit text-body-sm text-gray-500 px-6 py-3">Preu citat</th>
                        <th class="text-right font-outfit text-body-sm text-gray-500 px-6 py-3">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($quotation->items as $item)
                        <tr>
                            <td class="px-6 py-3 font-outfit text-body-sm">
                                {{ $item->product?->name ?? 'Producte eliminat' }}
                            </td>
                            <td class="px-6 py-3 font-outfit text-body-sm text-right">{{ $item->quantity }}</td>
                            <td class="px-6 py-3 font-outfit text-body-sm text-right">
                                {{ $item->quoted_price ? number_format($item->quoted_price, 2, ',', '.') . ' €' : '—' }}
                            </td>
                            <td class="px-6 py-3 font-outfit text-body-sm text-right">
                                @if($item->quoted_price)
                                    {{ number_format($item->quoted_price * $item->quantity, 2, ',', '.') }} €
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                @if($quotation->total_quoted)
                    <tfoot class="border-t border-gray-200 bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-3 font-alumni text-h6 text-right text-dark">Total</td>
                            <td class="px-6 py-3 font-alumni text-h5 text-right text-dark">
                                {{ number_format($quotation->total_quoted, 2, ',', '.') }} €
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    @endif

</div>
@endsection
