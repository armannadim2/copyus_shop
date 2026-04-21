@extends('layouts.admin')
@section('title', 'Plantilles d\'impressió')

@section('content')
<div class="p-8 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-alumni text-h4 text-dark">Plantilles d'impressió</h1>
            <p class="font-outfit text-xs text-gray-400 mt-0.5">
                Gestiona els tipus de treballs d'impressió disponibles
            </p>
        </div>
        <a href="{{ route('admin.print.templates.create') }}"
           class="flex items-center gap-2 bg-primary text-white font-outfit text-sm
                  font-semibold px-4 py-2.5 rounded-xl hover:brightness-110 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nova plantilla
        </a>
    </div>

    {{-- Search --}}
    <form method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cercar per nom…"
               class="border border-gray-200 rounded-xl px-4 py-2 font-outfit text-sm
                      focus:outline-none focus:ring-2 focus:ring-primary w-72">
        <button type="submit"
                class="bg-dark text-white font-outfit text-sm px-4 py-2 rounded-xl
                       hover:bg-primary transition-colors">
            Cercar
        </button>
        @if(request('search'))
            <a href="{{ route('admin.print.templates.index') }}"
               class="font-outfit text-sm text-gray-400 hover:text-primary
                      px-4 py-2 rounded-xl border border-gray-200 transition-colors">
                Netejar
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        @if($templates->isEmpty())
            <div class="py-16 text-center">
                <p class="text-4xl mb-3">🖨️</p>
                <p class="font-outfit text-sm text-gray-400">
                    Encara no hi ha plantilles. Crea'n una per començar.
                </p>
            </div>
        @else
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="font-outfit text-xs font-semibold tracking-widest text-gray-400
                                   uppercase text-left px-6 py-3">Plantilla</th>
                        <th class="font-outfit text-xs font-semibold tracking-widest text-gray-400
                                   uppercase text-left px-4 py-3">Preu base</th>
                        <th class="font-outfit text-xs font-semibold tracking-widest text-gray-400
                                   uppercase text-center px-4 py-3">Opcions</th>
                        <th class="font-outfit text-xs font-semibold tracking-widest text-gray-400
                                   uppercase text-center px-4 py-3">Treballs</th>
                        <th class="font-outfit text-xs font-semibold tracking-widest text-gray-400
                                   uppercase text-center px-4 py-3">Dies prod.</th>
                        <th class="font-outfit text-xs font-semibold tracking-widest text-gray-400
                                   uppercase text-center px-4 py-3">Estat</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($templates as $template)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($template->icon)
                                        <span class="text-2xl">{{ $template->icon }}</span>
                                    @endif
                                    <div>
                                        <p class="font-outfit text-sm font-semibold text-dark">
                                            {{ $template->getTranslation('name', 'ca') }}
                                        </p>
                                        <p class="font-outfit text-xs text-gray-400">
                                            {{ $template->slug }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="font-alumni text-sm-header text-primary">
                                    {{ number_format($template->base_price, 4, ',', '.') }} €
                                </span>
                                <span class="font-outfit text-xs text-gray-400 block">
                                    + IVA {{ $template->vat_rate }}%
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="font-outfit text-sm text-dark">
                                    {{ $template->options_count }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="font-outfit text-sm text-dark">
                                    {{ $template->jobs_count }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="font-outfit text-sm text-dark">
                                    {{ $template->base_production_days }}d
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <form method="POST"
                                      action="{{ route('admin.print.templates.toggle', $template) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="font-outfit text-xs px-2.5 py-1 rounded-full
                                                   border transition-colors
                                                   {{ $template->is_active
                                                       ? 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100'
                                                       : 'bg-gray-100 text-gray-500 border-gray-200 hover:bg-gray-200' }}">
                                        {{ $template->is_active ? 'Actiu' : 'Inactiu' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2 justify-end">
                                    <a href="{{ route('admin.print.templates.edit', $template) }}"
                                       class="font-outfit text-xs text-primary hover:underline">
                                        Editar
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.print.templates.destroy', $template) }}"
                                          onsubmit="return confirm('Eliminar aquesta plantilla i totes les seves opcions?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="font-outfit text-xs text-red-400 hover:text-red-600 transition-colors">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($templates->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $templates->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
