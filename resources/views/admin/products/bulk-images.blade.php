@extends('layouts.admin')
@section('title', 'Importació massiva d\'imatges')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.products.index') }}"
           class="font-outfit text-sm text-gray-500 hover:text-primary transition-colors">
            ← Productes
        </a>
        <h1 class="font-alumni text-h1 text-dark">Importació massiva d'imatges</h1>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 font-outfit text-sm px-4 py-3 rounded-2xl">
            {{ session('success') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="mb-6 bg-amber-50 border border-amber-200 text-amber-700 font-outfit text-sm px-4 py-3 rounded-2xl">
            {{ session('warning') }}
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

    {{-- Naming convention --}}
    <div class="mb-6 bg-light border border-gray-200 rounded-2xl p-6">
        <h2 class="font-alumni text-h4 text-dark mb-2">Com funciona?</h2>
        <p class="font-outfit text-sm text-gray-600 mb-3">
            Selecciona una carpeta amb les imatges. Cada fitxer s'associarà al producte
            corresponent segons el nom:
        </p>
        <ul class="font-outfit text-sm text-gray-600 space-y-1.5 list-disc list-inside">
            <li>
                <code class="bg-white px-1.5 py-0.5 rounded border border-gray-200">SKU-001.jpg</code>
                → imatge principal del producte amb SKU
                <code class="bg-white px-1.5 py-0.5 rounded border border-gray-200">SKU-001</code>
            </li>
            <li>
                <code class="bg-white px-1.5 py-0.5 rounded border border-gray-200">SKU-001-1.jpg</code>,
                <code class="bg-white px-1.5 py-0.5 rounded border border-gray-200">SKU-001-2.jpg</code>
                → afegides a la galeria (en aquest ordre)
            </li>
            <li>El SKU no distingeix majúscules/minúscules.</li>
            <li>Formats acceptats: JPG, PNG, WEBP, GIF. Mida màxima: 8 MB cadascuna.</li>
        </ul>
    </div>

    {{-- Upload form --}}
    <form method="POST" action="{{ route('admin.products.bulk-images.store') }}"
          enctype="multipart/form-data"
          class="bg-white border border-gray-200 rounded-2xl p-6 space-y-5">
        @csrf

        <div>
            <label class="block font-outfit text-sm font-medium text-dark mb-2">
                Carpeta o fitxers d'imatges
            </label>
            <input type="file" name="images[]" multiple accept="image/*" webkitdirectory required
                   class="block w-full text-sm font-outfit text-gray-600
                          file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0
                          file:bg-primary file:text-white file:font-alumni file:text-sm-header
                          file:cursor-pointer hover:file:brightness-110">
            <p class="font-outfit text-xs text-gray-400 mt-2">
                A Chrome, Edge i Safari pots seleccionar una carpeta sencera. A Firefox,
                pots seleccionar múltiples fitxers amb Ctrl/Cmd + click.
            </p>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-primary text-white font-alumni text-sm-header px-8 py-3
                           rounded-2xl hover:brightness-110 active:scale-95 transition-all">
                Pujar imatges
            </button>
            <a href="{{ route('admin.products.index') }}"
               class="font-outfit text-sm text-gray-500 px-6 py-3 rounded-2xl
                      hover:text-dark transition-colors">
                Cancel·lar
            </a>
        </div>
    </form>

    @if(session('unmatched_images'))
        <div class="mt-6 bg-amber-50 border border-amber-200 text-amber-700 font-outfit text-sm px-4 py-3 rounded-2xl">
            <p class="font-semibold mb-2">Imatges sense coincidència ({{ count(session('unmatched_images')) }}):</p>
            <ul class="list-disc list-inside space-y-0.5 text-xs max-h-48 overflow-y-auto">
                @foreach(session('unmatched_images') as $name)
                    <li>{{ $name }}</li>
                @endforeach
            </ul>
            <p class="text-xs mt-3 text-amber-600">
                Comprova que el nom del fitxer coincideix amb un SKU existent.
            </p>
        </div>
    @endif

</div>
@endsection
