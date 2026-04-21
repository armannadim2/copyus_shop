@extends('layouts.admin')
@section('title', $company->name)

@section('content')
<div class="p-8 max-w-5xl space-y-6">

    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('admin.companies.index') }}"
           class="font-outfit text-xs text-gray-400 hover:text-primary transition-colors">← Empreses</a>
        <span class="text-gray-300">/</span>
        <span class="font-outfit text-xs text-dark">{{ $company->name }}</span>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-2xl px-5 py-4 font-outfit text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="flex items-start justify-between flex-wrap gap-4">
        <div>
            <h1 class="font-alumni text-h3 text-dark">{{ $company->name }}</h1>
            <p class="font-outfit text-xs text-gray-400">{{ $company->cif_vat }} · {{ $company->email }}</p>
        </div>
        <div class="flex gap-2">
            <span class="font-outfit text-sm px-4 py-2 rounded-xl {{ $company->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                {{ $company->is_active ? 'Activa' : 'Inactiva' }}
            </span>
        </div>
    </div>

    <div class="grid lg:grid-cols-[1fr_320px] gap-6">

        {{-- Left --}}
        <div class="space-y-6">

            {{-- Members --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-alumni text-h5 text-dark">Membres ({{ $company->members->count() }})</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($company->members as $member)
                    <div class="px-6 py-3 flex items-center gap-4">
                        <div class="flex-1">
                            <p class="font-outfit text-sm text-dark">{{ $member->name }}</p>
                            <p class="font-outfit text-xs text-gray-400">{{ $member->email }}</p>
                        </div>
                        <span class="font-outfit text-xs text-gray-500">{{ ucfirst($member->company_role) }}</span>
                        @if($member->spending_limit)
                            <span class="font-outfit text-xs text-gray-400">{{ number_format($member->spending_limit, 2, ',', '.') }} €/mes</span>
                        @endif
                        <a href="{{ route('admin.users.show', $member) }}"
                           class="font-outfit text-xs text-primary hover:underline">Veure</a>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Recent orders --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-alumni text-h5 text-dark">Comandes recents</h2>
                    @if($outstandingTotal > 0)
                        <span class="font-outfit text-xs text-red-600 bg-red-50 px-3 py-1 rounded-full">
                            {{ number_format($outstandingTotal, 2, ',', '.') }} € pendents
                        </span>
                    @endif
                </div>
                @if($orders->isEmpty())
                    <p class="px-6 py-8 font-outfit text-sm text-gray-400 text-center">Cap comanda.</p>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($orders as $order)
                        <div class="px-6 py-3 flex items-center gap-4">
                            <div class="flex-1">
                                <p class="font-outfit text-sm font-semibold text-dark">#{{ $order->order_number }}</p>
                                <p class="font-outfit text-xs text-gray-400">{{ $order->user?->name }} · {{ $order->created_at->format('d/m/Y') }}</p>
                            </div>
                            <span class="font-outfit text-sm text-primary font-semibold">{{ number_format($order->total, 2, ',', '.') }} €</span>
                            <a href="{{ route('admin.orders.show', $order->order_number) }}"
                               class="font-outfit text-xs text-primary hover:underline">Veure</a>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Right — payment terms settings --}}
        <div class="space-y-5">
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="font-alumni text-h5 text-dark mb-4">Condicions comercials</h2>
                <form method="POST" action="{{ route('admin.companies.update', $company) }}" class="space-y-4">
                    @csrf @method('PATCH')
                    <div>
                        <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">Terminis de pagament</label>
                        <select name="payment_terms"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 font-outfit text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
                            @foreach(['immediate' => 'Pagament immediat', 'net_15' => 'Net 15', 'net_30' => 'Net 30', 'net_60' => 'Net 60', 'net_90' => 'Net 90'] as $val => $label)
                                <option value="{{ $val }}" @selected($company->payment_terms === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">Límit de crèdit (€)</label>
                        <input type="number" name="credit_limit" step="0.01" min="0"
                               value="{{ $company->credit_limit }}"
                               placeholder="0 = sense límit"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 font-outfit text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
                    </div>
                    <div>
                        <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">Llindar d'aprovació (€)</label>
                        <input type="number" name="approval_threshold" step="0.01" min="0"
                               value="{{ $company->approval_threshold }}"
                               placeholder="Buit = no requereix aprovació"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 font-outfit text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
                        <p class="font-outfit text-xs text-gray-400 mt-1">Comandes per sobre d'aquest import requeriran aprovació del gestor.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" @checked($company->is_active) class="rounded accent-primary">
                        <label class="font-outfit text-sm text-gray-600">Empresa activa</label>
                    </div>
                    <button type="submit"
                            class="w-full bg-primary text-white font-alumni text-sm-header py-3 rounded-2xl hover:brightness-110 transition-all">
                        Guardar condicions
                    </button>
                </form>
            </div>

            {{-- Credit summary --}}
            @if($company->credit_limit > 0)
            <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-3">
                <h2 class="font-alumni text-h5 text-dark">Crèdit</h2>
                <div class="flex justify-between">
                    <span class="font-outfit text-xs text-gray-400">Límit</span>
                    <span class="font-outfit text-sm text-dark">{{ number_format($company->credit_limit, 2, ',', '.') }} €</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-outfit text-xs text-gray-400">Utilitzat</span>
                    <span class="font-outfit text-sm text-dark">{{ number_format($company->credit_used, 2, ',', '.') }} €</span>
                </div>
                <div class="flex justify-between pt-2 border-t border-gray-100">
                    <span class="font-alumni text-sm-header text-dark">Disponible</span>
                    <span class="font-alumni text-h6 {{ $company->credit_available > 0 ? 'text-green-600' : 'text-red-500' }}">
                        {{ number_format($company->credit_available, 2, ',', '.') }} €
                    </span>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    @php $pct = $company->credit_limit > 0 ? min(100, ($company->credit_used / $company->credit_limit) * 100) : 0; @endphp
                    <div class="h-2 rounded-full {{ $pct >= 90 ? 'bg-red-400' : ($pct >= 70 ? 'bg-amber-400' : 'bg-green-400') }}"
                         style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
