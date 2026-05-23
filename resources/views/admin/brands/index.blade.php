@extends('layouts.admin')
@section('title', 'Gestió de Marques')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center justify-between mb-8">
        <h1 class="font-alumni text-h4 text-dark">Marques</h1>
        <a href="{{ route('admin.brands.create') }}"
           class="inline-flex items-center gap-2 bg-primary text-white font-alumni text-sm-header
                  px-5 py-2.5 rounded-2xl hover:brightness-110 active:scale-95 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nova marca
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 font-outfit text-sm px-4 py-3 rounded-2xl">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 font-outfit text-sm px-4 py-3 rounded-2xl">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.brands.index') }}"
          class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cerca nom..."
               class="border border-gray-200 rounded-xl px-4 py-2 font-outfit text-sm
                      focus:outline-none focus:ring-2 focus:ring-primary w-56">
        <select name="status"
                class="border border-gray-200 rounded-xl px-4 py-2 font-outfit text-sm
                       focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">Tots els estats</option>
            <option value="active"   @selected(request('status') === 'active')>Actives</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Inactives</option>
        </select>
        <button type="submit"
                class="bg-primary text-white font-outfit text-sm px-5 py-2
                       rounded-xl hover:bg-primary/90 transition-colors">
            Filtrar
        </button>
        @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.brands.index') }}"
               class="font-outfit text-sm text-gray-500 px-4 py-2 rounded-xl
                      border border-gray-200 hover:bg-gray-50 transition-colors">
                Netejar
            </a>
        @endif
    </form>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    @include('admin.partials._sort_th', ['thCol' => 'name_ca',       'thLabel' => 'Marca'])
                    <th class="text-left font-outfit text-xs font-semibold tracking-widest text-gray-400 uppercase px-6 py-3">Slug</th>
                    @include('admin.partials._sort_th', ['thCol' => 'sort_order',     'thLabel' => 'Ordre',     'thAlign' => 'center'])
                    @include('admin.partials._sort_th', ['thCol' => 'products_count', 'thLabel' => 'Productes', 'thAlign' => 'center'])
                    @include('admin.partials._sort_th', ['thCol' => 'is_active',      'thLabel' => 'Activa',    'thAlign' => 'center'])
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($brands as $brand)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($brand->image)
                                    <img src="{{ asset('storage/'.$brand->image) }}"
                                         class="w-10 h-10 object-cover rounded-xl flex-shrink-0">
                                @else
                                    <div class="w-10 h-10 bg-light rounded-xl flex items-center justify-center text-lg flex-shrink-0">🏷️</div>
                                @endif
                                <span class="font-outfit text-sm text-dark">{{ $brand->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-outfit text-sm text-gray-500">{{ $brand->slug }}</td>
                        <td class="px-6 py-4 text-center font-outfit text-sm text-gray-500">
                            {{ $brand->sort_order }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-outfit text-sm {{ $brand->products_count > 0 ? 'text-dark font-semibold' : 'text-gray-300' }}">
                                {{ $brand->products_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form method="POST" action="{{ route('admin.brands.toggle', $brand->id) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-lg" title="{{ $brand->is_active ? 'Desactivar' : 'Activar' }}">
                                    {{ $brand->is_active ? '✅' : '❌' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('admin.brands.edit', $brand->id) }}"
                                   class="font-outfit text-sm text-secondary hover:text-primary transition-colors">
                                    Editar →
                                </a>
                                <form method="POST" action="{{ route('admin.brands.destroy', $brand->id) }}"
                                      onsubmit="return confirm('N\'estàs segur de voler eliminar aquesta marca?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="font-outfit text-sm text-red-500 hover:text-red-700 transition-colors">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center font-outfit text-sm text-gray-400">
                            No s'han trobat marques.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $brands->links() }}</div>

</div>
@endsection
