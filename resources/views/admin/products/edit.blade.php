@extends('layouts.admin')
@section('title', 'Editar: ' . $product->getTranslation('name', 'ca'))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.products.index') }}"
           class="font-outfit text-sm text-gray-500 hover:text-primary transition-colors">
            ← Productes
        </a>
        <h1 class="font-alumni text-h1 text-dark">Editar Producte</h1>
        @if($product->is_low_stock)
            <span class="inline-flex items-center gap-1 bg-orange-50 border border-orange-200
                         text-orange-700 font-outfit text-xs px-3 py-1 rounded-full">
                ⚠️ Stock baix
            </span>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 font-outfit text-sm px-4 py-3 rounded-2xl">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-600 font-outfit text-sm px-4 py-3 rounded-2xl">
            <ul class="space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.products.update', $product->id) }}"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf @method('PUT')

        @include('admin.products._form')

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-primary text-white font-alumni text-sm-header px-8 py-3
                           rounded-2xl hover:brightness-110 active:scale-95 transition-all">
                Guardar canvis
            </button>
            <a href="{{ route('admin.products.index') }}"
               class="font-outfit text-sm text-gray-500 px-6 py-3 rounded-2xl
                      border border-gray-200 hover:bg-gray-50 transition-colors">
                Cancel·lar
            </a>
            <button type="submit" form="delete-product-form"
                    class="ml-auto font-outfit text-sm text-red-500 hover:text-red-700
                           transition-colors px-4 py-3">
                Eliminar producte
            </button>
        </div>
    </form>

    {{-- Delete form must live OUTSIDE the update form to avoid nested-form conflict --}}
    <form id="delete-product-form"
          method="POST" action="{{ route('admin.products.destroy', $product->id) }}"
          onsubmit="return confirm('Eliminar aquest producte? Aquesta acció no es pot desfer.')">
        @csrf @method('DELETE')
    </form>

</div>
@endsection
