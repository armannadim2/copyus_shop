@extends('layouts.admin')
@section('title', 'Gestió de Productes')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center justify-between mb-8">
        <h1 class="font-alumni text-h1 text-dark">Productes</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.products.bulk-images') }}"
               class="inline-flex items-center gap-2 bg-white text-dark border border-gray-200 font-alumni text-sm-header
                      px-5 py-2.5 rounded-2xl hover:bg-light active:scale-95 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Imatges per SKU
            </a>
            <a href="{{ route('admin.products.bulk-upload') }}"
               class="inline-flex items-center gap-2 bg-white text-dark border border-gray-200 font-alumni text-sm-header
                      px-5 py-2.5 rounded-2xl hover:bg-light active:scale-95 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5-5m0 0l5 5m-5-5v12"/>
                </svg>
                Importació CSV
            </a>
            <a href="{{ route('admin.products.create') }}"
               class="inline-flex items-center gap-2 bg-primary text-white font-alumni text-sm-header
                      px-5 py-2.5 rounded-2xl hover:brightness-110 active:scale-95 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nou producte
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 font-outfit text-sm px-4 py-3 rounded-2xl">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.products.index') }}"
          class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cerca SKU, marca, nom..."
               class="border border-gray-200 rounded-xl px-4 py-2 font-outfit text-sm
                      focus:outline-none focus:ring-2 focus:ring-primary w-56">
        <select name="category_id"
                class="border border-gray-200 rounded-xl px-4 py-2 font-outfit text-sm
                       focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">Totes les categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                    {{ $category->getTranslation('name', 'ca') }}
                </option>
            @endforeach
        </select>
        <select name="status"
                class="border border-gray-200 rounded-xl px-4 py-2 font-outfit text-sm
                       focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">Tots els estats</option>
            <option value="active"   @selected(request('status') === 'active')>Actius</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Inactius</option>
        </select>
        <select name="stock"
                class="border border-gray-200 rounded-xl px-4 py-2 font-outfit text-sm
                       focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">Tot l'estoc</option>
            <option value="low" @selected(request('stock') === 'low')>Stock baix</option>
            <option value="out" @selected(request('stock') === 'out')>Sense stock</option>
        </select>
        @if($allTags->isNotEmpty())
            <select name="tag"
                    class="border border-gray-200 rounded-xl px-4 py-2 font-outfit text-sm
                           focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">Totes les etiquetes</option>
                @foreach($allTags as $tag)
                    <option value="{{ $tag->slug }}" @selected(request('tag') === $tag->slug)>
                        {{ $tag->name }}
                    </option>
                @endforeach
            </select>
        @endif
        <button type="submit"
                class="bg-primary text-white font-outfit text-sm px-5 py-2
                       rounded-xl hover:bg-primary/90 transition-colors">
            Filtrar
        </button>
        @if(request()->hasAny(['search','category_id','status','stock','tag']))
            <a href="{{ route('admin.products.index') }}"
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
                    <th class="text-left font-outfit text-xs font-semibold tracking-widest text-gray-400 uppercase px-6 py-3">Producte</th>
                    <th class="text-left font-outfit text-xs font-semibold tracking-widest text-gray-400 uppercase px-6 py-3">SKU</th>
                    <th class="text-left font-outfit text-xs font-semibold tracking-widest text-gray-400 uppercase px-6 py-3">Categoria</th>
                    <th class="text-right font-outfit text-xs font-semibold tracking-widest text-gray-400 uppercase px-6 py-3">Preu</th>
                    <th class="text-right font-outfit text-xs font-semibold tracking-widest text-gray-400 uppercase px-6 py-3">Stock</th>
                    <th class="text-left font-outfit text-xs font-semibold tracking-widest text-gray-400 uppercase px-6 py-3">Etiquetes</th>
                    <th class="text-center font-outfit text-xs font-semibold tracking-widest text-gray-400 uppercase px-6 py-3">Actiu</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($product->image)
                                    <img src="{{ asset('storage/'.$product->image) }}"
                                         class="w-10 h-10 object-cover rounded-xl flex-shrink-0">
                                @else
                                    <div class="w-10 h-10 bg-light rounded-xl flex items-center justify-center text-lg flex-shrink-0">📦</div>
                                @endif
                                <span class="font-outfit text-sm text-dark">{{ $product->getTranslation('name', 'ca') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-outfit text-sm text-gray-500">{{ $product->sku }}</td>
                        <td class="px-6 py-4 font-outfit text-sm text-gray-500">
                            {{ $product->category?->getTranslation('name', 'ca') }}
                        </td>
                        <td class="px-6 py-4 font-outfit text-sm text-right text-dark">
                            {{ number_format($product->price, 2, ',', '.') }} €
                            @if($product->priceTiers->isNotEmpty())
                                <span class="block font-outfit text-xs text-secondary">{{ $product->priceTiers->count() }} tarif.</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($product->stock === 0)
                                <span class="inline-block font-outfit text-xs px-2 py-0.5 bg-red-50 text-red-600 rounded-full">
                                    Sense stock
                                </span>
                            @elseif($product->is_low_stock)
                                <span class="inline-block font-outfit text-xs px-2 py-0.5 bg-orange-50 text-orange-600 rounded-full">
                                    ⚠️ {{ $product->stock }}
                                </span>
                            @else
                                <span class="font-outfit text-sm text-dark">{{ $product->stock }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($product->tags as $tag)
                                    <span class="font-outfit text-xs px-2 py-0.5 bg-light text-gray-600 rounded-full">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form method="POST" action="{{ route('admin.products.toggle', $product->id) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-lg" title="{{ $product->is_active ? 'Desactivar' : 'Activar' }}">
                                    {{ $product->is_active ? '✅' : '❌' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.products.edit', $product->id) }}"
                               class="font-outfit text-sm text-secondary hover:text-primary transition-colors">
                                Editar →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center font-outfit text-sm text-gray-400">
                            No s'han trobat productes.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>

</div>
@endsection
