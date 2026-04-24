@extends('layouts.admin')
@section('title', 'Importació massiva de productes')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.products.index') }}"
           class="font-outfit text-sm text-gray-500 hover:text-primary transition-colors">
            ← Productes
        </a>
        <h1 class="font-alumni text-h1 text-dark">Importació massiva (CSV)</h1>
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

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-600 font-outfit text-sm px-4 py-3 rounded-2xl">
            {{ session('error') }}
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

    @if(session('csv_errors'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-600 font-outfit text-sm px-4 py-3 rounded-2xl">
            <p class="font-semibold mb-2">Files amb errors:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach(session('csv_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <p class="font-outfit text-sm text-gray-500 mb-6">
        Vols pujar només imatges (sense CSV)?
        <a href="{{ route('admin.products.bulk-images') }}" class="text-primary hover:underline">
            Anar a importació massiva d'imatges →
        </a>
    </p>

    {{-- Sample CSV download --}}
    <div class="mb-6 bg-light border border-gray-200 rounded-2xl p-6">
        <div class="flex items-start gap-4">
            <div class="bg-primary/10 text-primary rounded-xl p-3 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h2 class="font-alumni text-h4 text-dark mb-1">Necessites un model?</h2>
                <p class="font-outfit text-sm text-gray-500 mb-3">
                    Descarrega un CSV d'exemple amb totes les columnes i dues files de mostra.
                </p>
                <a href="{{ route('admin.products.bulk-upload.sample') }}"
                   class="inline-flex items-center gap-2 bg-dark text-white font-alumni text-sm-header
                          px-5 py-2 rounded-xl hover:brightness-110 active:scale-95 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/>
                    </svg>
                    Descarrega CSV d'exemple
                </a>
            </div>
        </div>
    </div>

    {{-- Upload form --}}
    <form method="POST" action="{{ route('admin.products.bulk-upload.store') }}"
          enctype="multipart/form-data"
          class="bg-white border border-gray-200 rounded-2xl p-6 space-y-5">
        @csrf

        <div>
            <label class="block font-outfit text-sm font-medium text-dark mb-2">
                Fitxer CSV
            </label>
            <input type="file" name="csv_file" accept=".csv,text/csv" required
                   class="block w-full text-sm font-outfit text-gray-600
                          file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0
                          file:bg-primary file:text-white file:font-alumni file:text-sm-header
                          file:cursor-pointer hover:file:brightness-110">
            <p class="font-outfit text-xs text-gray-400 mt-2">
                Mida màxima: 5 MB. Productes existents (mateix SKU) s'actualitzaran.
            </p>
        </div>

        <div>
            <label class="block font-outfit text-sm font-medium text-dark mb-2">
                Imatges (opcional)
            </label>
            <input type="file" name="images[]" multiple accept="image/*" webkitdirectory
                   class="block w-full text-sm font-outfit text-gray-600
                          file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0
                          file:bg-dark file:text-white file:font-alumni file:text-sm-header
                          file:cursor-pointer hover:file:brightness-110">
            <p class="font-outfit text-xs text-gray-400 mt-2">
                Selecciona una carpeta sencera amb les imatges. Les associarem als productes
                pel nom del fitxer:
                <code class="bg-light px-1.5 py-0.5 rounded">SKU.jpg</code> → imatge principal,
                <code class="bg-light px-1.5 py-0.5 rounded">SKU-1.jpg</code>,
                <code class="bg-light px-1.5 py-0.5 rounded">SKU-2.jpg</code> → galeria.
                Mida màxima per imatge: 8 MB.
            </p>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-primary text-white font-alumni text-sm-header px-8 py-3
                           rounded-2xl hover:brightness-110 active:scale-95 transition-all">
                Importar productes
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
            <p class="font-semibold mb-2">Imatges sense coincidència:</p>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach(session('unmatched_images') as $name)
                    <li>{{ $name }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Column reference --}}
    <div class="mt-8 bg-white border border-gray-200 rounded-2xl p-6">
        <h2 class="font-alumni text-h4 text-dark mb-3">Columnes del CSV</h2>
        <p class="font-outfit text-sm text-gray-500 mb-4">
            La primera fila ha de ser la capçalera amb els noms exactes de les columnes.
            Les marcades amb <span class="text-red-500">*</span> són obligatòries.
        </p>
        <div class="overflow-x-auto">
            <table class="w-full font-outfit text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-500 text-left">
                        <th class="py-2 pr-4">Columna</th>
                        <th class="py-2 pr-4">Descripció</th>
                    </tr>
                </thead>
                <tbody class="text-dark">
                    @php
                        $required = ['sku', 'category_slug', 'brand', 'name_ca', 'name_es', 'price', 'vat_rate', 'stock', 'min_order_quantity', 'unit'];
                        $descriptions = [
                            'sku'                  => 'Identificador únic del producte (s\'utilitza per detectar duplicats)',
                            'category_slug'        => 'Slug de la categoria existent (ex: paper, tinta)',
                            'brand'                => 'Marca del producte',
                            'name_ca'              => 'Nom en català',
                            'name_es'              => 'Nom en castellà',
                            'name_en'              => 'Nom en anglès (opcional, per defecte = castellà)',
                            'short_description_ca' => 'Descripció curta en català',
                            'short_description_es' => 'Descripció curta en castellà',
                            'short_description_en' => 'Descripció curta en anglès',
                            'description_ca'       => 'Descripció completa en català',
                            'description_es'       => 'Descripció completa en castellà',
                            'description_en'       => 'Descripció completa en anglès',
                            'price'                => 'Preu sense IVA (decimal, ex: 12.99)',
                            'vat_rate'             => 'Tipus d\'IVA (decimal, ex: 21.00)',
                            'stock'                => 'Quantitat disponible (enter)',
                            'min_order_quantity'   => 'Quantitat mínima per comanda (enter, mínim 1)',
                            'unit'                 => 'Unitat de venda (ex: unit, caixa, palet)',
                            'is_active'            => '1/0 — producte actiu (per defecte: 1)',
                            'is_featured'          => '1/0 — producte destacat (per defecte: 0)',
                            'low_stock_threshold'  => 'Llindar d\'estoc baix (enter, opcional)',
                        ];
                    @endphp
                    @foreach($columns as $col)
                        <tr class="border-b border-gray-100">
                            <td class="py-2 pr-4 font-mono text-xs">
                                {{ $col }}
                                @if(in_array($col, $required)) <span class="text-red-500">*</span> @endif
                            </td>
                            <td class="py-2 pr-4 text-gray-600">{{ $descriptions[$col] ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
