@extends('layouts.admin')
@section('title', 'Subscriptors Newsletter')

@section('content')
<div class="p-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="font-alumni text-h5 text-dark">Subscriptors Newsletter</h1>
            <p class="font-outfit text-body-sm text-gray-500 mt-1">
                Gestiona les adreces subscrites al butlletí de novetats.
            </p>
        </div>
        <a href="{{ route('admin.newsletter.export') }}"
           class="inline-flex items-center gap-2 bg-dark text-white font-outfit text-sm
                  px-5 py-2.5 rounded-xl hover:brightness-125 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Exportar CSV
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 px-6 py-5">
            <p class="font-outfit text-xs text-gray-400 uppercase tracking-widest mb-1">Actius</p>
            <p class="font-alumni text-h3 text-dark">{{ number_format($stats['total_active']) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 px-6 py-5">
            <p class="font-outfit text-xs text-gray-400 uppercase tracking-widest mb-1">Total</p>
            <p class="font-alumni text-h3 text-dark">{{ number_format($stats['total_all']) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 px-6 py-5">
            <p class="font-outfit text-xs text-gray-400 uppercase tracking-widest mb-1">Aquest mes</p>
            <p class="font-alumni text-h3 text-secondary">+{{ number_format($stats['this_month']) }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.newsletter.index') }}"
          class="flex gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cercar per correu..."
               class="border border-gray-200 rounded-xl px-4 py-2 font-outfit text-sm
                      focus:outline-none focus:ring-2 focus:ring-primary flex-1 max-w-xs">
        <select name="status"
                class="border border-gray-200 rounded-xl px-4 py-2 font-outfit text-sm
                       focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">Tots</option>
            <option value="active"   @selected(request('status') === 'active')>Actius</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Inactius</option>
        </select>
        <button type="submit"
                class="bg-primary text-white font-outfit text-sm px-5 py-2
                       rounded-xl hover:bg-primary/90 transition-colors">
            Filtrar
        </button>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.newsletter.index') }}"
               class="font-outfit text-sm text-gray-500 px-4 py-2 rounded-xl
                      border border-gray-200 hover:bg-gray-50 transition-colors">
                Netejar
            </a>
        @endif
    </form>

    {{-- Table --}}
    @if($subscriptions->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
            <p class="font-outfit text-4xl mb-4">📧</p>
            <p class="font-alumni text-h6 text-dark mb-2">Cap subscriptor</p>
            <p class="font-outfit text-sm text-gray-400">
                @if(request()->hasAny(['search', 'status']))
                    No s'han trobat subscriptors amb els filtres actuals.
                @else
                    Encara no hi ha cap subscripció al butlletí.
                @endif
            </p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-100 bg-light/50">
                        <th class="font-outfit text-xs font-semibold text-gray-400 uppercase tracking-wider px-6 py-4">
                            Correu electrònic
                        </th>
                        <th class="font-outfit text-xs font-semibold text-gray-400 uppercase tracking-wider px-6 py-4">
                            Data subscripció
                        </th>
                        <th class="font-outfit text-xs font-semibold text-gray-400 uppercase tracking-wider px-6 py-4">
                            Adreça IP
                        </th>
                        <th class="font-outfit text-xs font-semibold text-gray-400 uppercase tracking-wider px-6 py-4 w-28">
                            Estat
                        </th>
                        <th class="px-6 py-4 w-20"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($subscriptions as $sub)
                    <tr class="hover:bg-light/40 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-outfit text-sm text-dark">{{ $sub->email }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-outfit text-sm text-gray-600">
                                {{ $sub->created_at->format('d/m/Y') }}
                            </p>
                            <p class="font-outfit text-xs text-gray-400">
                                {{ $sub->created_at->format('H:i') }}
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-outfit text-sm text-gray-500">
                                {{ $sub->ip_address ?? '—' }}
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            @if($sub->is_active)
                                <span class="inline-flex items-center gap-1.5 font-outfit text-xs font-semibold
                                             px-2.5 py-1 bg-green-50 text-green-700 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Actiu
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 font-outfit text-xs font-semibold
                                             px-2.5 py-1 bg-gray-100 text-gray-500 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                    Inactiu
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <form method="POST"
                                  action="{{ route('admin.newsletter.destroy', $sub) }}"
                                  onsubmit="return confirm('Eliminar aquesta subscripció?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="font-outfit text-xs text-red-400 hover:text-red-600
                                               transition-colors px-2 py-1 rounded-lg hover:bg-red-50">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($subscriptions->hasPages())
            <div class="mt-6">
                {{ $subscriptions->links() }}
            </div>
        @endif

        <p class="font-outfit text-xs text-gray-400 mt-3">
            {{ $subscriptions->total() }} subscriptor{{ $subscriptions->total() !== 1 ? 's' : '' }} en total
        </p>
    @endif

</div>
@endsection
