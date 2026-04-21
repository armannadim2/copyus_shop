@extends('layouts.admin')
@section('title', 'Usuari: ' . $user->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.users.index') }}"
           class="font-outfit text-body-sm text-gray-500 hover:text-primary transition-colors">
            ← Usuaris
        </a>
        <h1 class="font-alumni text-h1 text-dark">{{ $user->name }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- User Info --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">
            <h2 class="font-alumni text-h4 text-dark mb-4">Informació</h2>
            <dl class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="font-outfit text-body-sm text-gray-400">Empresa</dt>
                    <dd class="font-outfit text-body-sm text-dark">{{ $user->company_name }}</dd>
                </div>
                <div>
                    <dt class="font-outfit text-body-sm text-gray-400">CIF</dt>
                    <dd class="font-outfit text-body-sm text-dark">{{ $user->cif }}</dd>
                </div>
                <div>
                    <dt class="font-outfit text-body-sm text-gray-400">Email</dt>
                    <dd class="font-outfit text-body-sm text-dark">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt class="font-outfit text-body-sm text-gray-400">Telèfon</dt>
                    <dd class="font-outfit text-body-sm text-dark">{{ $user->phone ?? '—' }}</dd>
                </div>
                <div class="col-span-2">
                    <dt class="font-outfit text-body-sm text-gray-400">Adreça</dt>
                    <dd class="font-outfit text-body-sm text-dark">
                        {{ $user->address }}, {{ $user->city }} {{ $user->postal_code }}, {{ $user->country }}
                    </dd>
                </div>
                <div>
                    <dt class="font-outfit text-body-sm text-gray-400">Factura empresarial</dt>
                    <dd class="font-outfit text-body-sm text-dark">
                        @if($user->requires_invoice)
                            <span class="inline-flex items-center gap-1 text-green-700">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Sí
                            </span>
                        @else
                            <span class="text-gray-400">No</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="font-outfit text-body-sm text-gray-400">Registrat</dt>
                    <dd class="font-outfit text-body-sm text-dark">{{ $user->created_at->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="font-outfit text-body-sm text-gray-400">Aprovació</dt>
                    <dd class="font-outfit text-body-sm text-dark">
                        {{ $user->approved_at ? $user->approved_at->format('d/m/Y') : '—' }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Actions --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="font-alumni text-h4 text-dark mb-4">Accions</h2>
            @if($user->role === 'pending')
                <form method="POST" action="{{ route('admin.users.approve', $user->id) }}" class="mb-3">
                    @csrf @method('PATCH')
                    <button class="w-full bg-green-600 text-white font-outfit text-body-sm
                                   py-2 rounded-xl hover:bg-green-700 transition-colors">
                        ✅ Aprovar usuari
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.users.reject', $user->id) }}">
                    @csrf @method('PATCH')
                    <button class="w-full bg-red-600 text-white font-outfit text-body-sm
                                   py-2 rounded-xl hover:bg-red-700 transition-colors">
                        ✗ Rebutjar usuari
                    </button>
                </form>
            @elseif($user->role === 'rejected')
                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                      onsubmit="return confirm('Eliminar usuari definitivamente?')">
                    @csrf @method('DELETE')
                    <button class="w-full bg-gray-700 text-white font-outfit text-body-sm
                                   py-2 rounded-xl hover:bg-gray-800 transition-colors">
                        Eliminar usuari
                    </button>
                </form>
            @else
                <p class="font-outfit text-body-sm text-gray-400">Usuari aprovat. Sense accions disponibles.</p>
            @endif
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-alumni text-h4 text-dark">Comandes recents</h2>
        </div>
        @if($user->orders->isEmpty())
            <p class="px-6 py-8 font-outfit text-body-sm text-gray-400">Sense comandes.</p>
        @else
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left font-outfit text-body-sm text-gray-500 px-6 py-3">Número</th>
                        <th class="text-left font-outfit text-body-sm text-gray-500 px-6 py-3">Data</th>
                        <th class="text-left font-outfit text-body-sm text-gray-500 px-6 py-3">Estat</th>
                        <th class="text-right font-outfit text-body-sm text-gray-500 px-6 py-3">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($user->orders->take(5) as $order)
                        <tr>
                            <td class="px-6 py-3 font-outfit text-body-sm">
                                <a href="{{ route('admin.orders.show', $order->order_number) }}"
                                   class="text-secondary hover:text-primary">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="px-6 py-3 font-outfit text-body-sm text-gray-500">
                                {{ $order->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-3 font-outfit text-body-sm">{{ $order->status }}</td>
                            <td class="px-6 py-3 font-outfit text-body-sm text-right">
                                {{ number_format($order->total, 2, ',', '.') }} €
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>
@endsection
