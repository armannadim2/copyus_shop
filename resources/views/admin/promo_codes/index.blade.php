@extends('layouts.admin')
@section('title', 'Codis de descompte')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center justify-between mb-8">
        <h1 class="font-alumni text-h1 text-dark">Codis de descompte</h1>
        <a href="{{ route('admin.promo-codes.create') }}"
           class="bg-primary text-white font-alumni text-sm-header px-6 py-2.5
                  rounded-2xl hover:brightness-110 transition-all">
            + Nou codi
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 font-outfit text-xs font-semibold tracking-widest text-primary uppercase">Codi</th>
                    <th class="text-left px-5 py-3 font-outfit text-xs font-semibold tracking-widest text-primary uppercase">Descompte</th>
                    <th class="text-left px-5 py-3 font-outfit text-xs font-semibold tracking-widest text-primary uppercase">Usos</th>
                    <th class="text-left px-5 py-3 font-outfit text-xs font-semibold tracking-widest text-primary uppercase">Validesa</th>
                    <th class="text-left px-5 py-3 font-outfit text-xs font-semibold tracking-widest text-primary uppercase">Estat</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($codes as $code)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3">
                            <span class="font-outfit font-semibold text-dark tracking-wide">{{ $code->code }}</span>
                            @if($code->description)
                                <p class="font-outfit text-xs text-gray-400">{{ $code->description }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3 font-outfit text-dark">
                            @if($code->type === 'percent')
                                {{ $code->value }}%
                            @else
                                {{ number_format($code->value, 2, ',', '.') }} €
                            @endif
                            @if($code->min_order_total)
                                <span class="text-xs text-gray-400">(mín. {{ $code->min_order_total }} €)</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 font-outfit text-dark">
                            {{ $code->used_count }}{{ $code->max_uses ? ' / ' . $code->max_uses : '' }}
                        </td>
                        <td class="px-5 py-3 font-outfit text-xs text-gray-500">
                            {{ $code->valid_from?->format('d/m/Y') ?? '—' }}
                            → {{ $code->valid_until?->format('d/m/Y') ?? '∞' }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-outfit font-semibold
                                {{ $code->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $code->is_active ? 'Actiu' : 'Inactiu' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.promo-codes.edit', $code->id) }}"
                               class="font-outfit text-xs text-secondary hover:text-primary transition-colors mr-3">
                                Editar
                            </a>
                            <form method="POST" action="{{ route('admin.promo-codes.destroy', $code->id) }}"
                                  class="inline" onsubmit="return confirm('Eliminar aquest codi?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="font-outfit text-xs text-red-400 hover:text-red-600 transition-colors">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center font-outfit text-sm text-gray-400">
                            No hi ha codis de descompte encara.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $codes->links() }}</div>
</div>
@endsection
