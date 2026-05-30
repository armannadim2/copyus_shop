@extends('layouts.admin')
@section('title', 'Editar slide portada')

@section('content')
<div class="p-8 max-w-2xl">

    <div class="mb-6">
        <a href="{{ route('admin.hero-slides.index') }}"
           class="font-outfit text-sm text-gray-400 hover:text-dark transition-colors inline-flex items-center gap-1">
            ← Tornar als slides
        </a>
        <h1 class="font-alumni text-h5 text-dark mt-2">Editar slide</h1>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-8">
        <form method="POST" action="{{ route('admin.hero-slides.update', $heroSlide) }}"
              enctype="multipart/form-data"
              x-data="{ preview: null }">
            @csrf @method('PUT')

            {{-- Current image + upload --}}
            <div class="mb-6">
                <label class="block font-outfit text-sm font-medium text-dark mb-1.5">
                    Imatge
                </label>
                <p class="font-outfit text-xs text-gray-400 mb-3">
                    Deixa-ho en blanc per conservar la imatge actual. Selecciona una nova per substituir-la.
                </p>

                {{-- Current image preview --}}
                <div class="mb-4 rounded-xl overflow-hidden w-48 h-28 bg-gray-100 shadow-sm">
                    <img src="{{ $heroSlide->imageUrl() }}" alt="Imatge actual"
                         class="w-full h-full object-cover"
                         x-show="!preview">
                    <img :src="preview" alt="Nova imatge"
                         class="w-full h-full object-cover"
                         x-show="preview" style="display:none">
                </div>
                <p class="font-outfit text-xs text-gray-400 mb-3" x-show="!preview">
                    Imatge actual
                </p>
                <p class="font-outfit text-xs text-primary mb-3" x-show="preview" style="display:none">
                    ✓ Nova imatge seleccionada
                </p>

                <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center
                            hover:border-primary/40 transition-colors cursor-pointer"
                     @click="$refs.imageInput.click()"
                     @dragover.prevent
                     @drop.prevent="
                        const f = $event.dataTransfer.files[0];
                        if (f && f.type.startsWith('image/')) {
                            const dt = new DataTransfer();
                            dt.items.add(f);
                            $refs.imageInput.files = dt.files;
                            preview = URL.createObjectURL(f);
                        }
                     ">
                    <input type="file" name="image" accept="image/*"
                           x-ref="imageInput"
                           @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                           class="hidden">
                    <p class="font-outfit text-sm text-gray-400">
                        Fes clic o arrossega per canviar la imatge
                    </p>
                    <p class="font-outfit text-xs text-gray-300 mt-1">JPG, PNG, WebP · màx. 4 MB</p>
                </div>
                @error('image')
                    <p class="mt-1.5 font-outfit text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Eyebrow --}}
            <div class="mb-5">
                <label for="eyebrow" class="block font-outfit text-sm font-medium text-dark mb-1.5">
                    Text petit (eyebrow)
                </label>
                <p class="font-outfit text-xs text-gray-400 mb-2">
                    S'mostra a sobre del títol, en majúscules.
                </p>
                <input type="text" name="eyebrow" id="eyebrow"
                       value="{{ old('eyebrow', $heroSlide->eyebrow) }}"
                       maxlength="100" placeholder="Novetat"
                       class="w-full font-outfit text-sm border border-gray-200 rounded-xl px-4 py-3
                              focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all
                              placeholder:text-gray-300">
                @error('eyebrow')
                    <p class="mt-1.5 font-outfit text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Title --}}
            <div class="mb-5">
                <label for="title" class="block font-outfit text-sm font-medium text-dark mb-1.5">
                    Títol principal
                </label>
                <input type="text" name="title" id="title"
                       value="{{ old('title', $heroSlide->title) }}"
                       maxlength="200" placeholder="El teu títol aquí"
                       class="w-full font-outfit text-sm border border-gray-200 rounded-xl px-4 py-3
                              focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all
                              placeholder:text-gray-300">
                @error('title')
                    <p class="mt-1.5 font-outfit text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Sort order --}}
            <div class="mb-5">
                <label for="sort_order" class="block font-outfit text-sm font-medium text-dark mb-1.5">
                    Ordre <span class="font-normal text-gray-400">(número menor = primer)</span>
                </label>
                <input type="number" name="sort_order" id="sort_order"
                       value="{{ old('sort_order', $heroSlide->sort_order) }}" min="0"
                       class="w-28 font-outfit text-sm border border-gray-200 rounded-xl px-4 py-3
                              focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                @error('sort_order')
                    <p class="mt-1.5 font-outfit text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Active --}}
            <div class="mb-8">
                <label class="inline-flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', $heroSlide->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 accent-primary rounded">
                    <span class="font-outfit text-sm text-dark">Actiu (visible a la portada)</span>
                </label>
            </div>

            <div class="flex gap-3 pt-2 border-t border-gray-100">
                <button type="submit"
                        class="bg-primary text-white font-outfit text-sm font-medium
                               px-6 py-3 rounded-xl hover:brightness-110 active:scale-95 transition-all">
                    Desar canvis
                </button>
                <a href="{{ route('admin.hero-slides.index') }}"
                   class="bg-gray-100 text-gray-600 font-outfit text-sm px-6 py-3 rounded-xl
                          hover:bg-gray-200 transition-all">
                    Cancel·lar
                </a>
            </div>
        </form>
    </div>

    {{-- Danger zone --}}
    <div class="mt-6 bg-white rounded-2xl border border-red-100 p-6">
        <h3 class="font-outfit text-sm font-semibold text-red-600 mb-3">Zona de perill</h3>
        <form method="POST" action="{{ route('admin.hero-slides.destroy', $heroSlide) }}"
              onsubmit="return confirm('Segur que vols eliminar aquest slide? L\'acció no es pot desfer.')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="bg-red-50 text-red-600 border border-red-200 font-outfit text-sm px-5 py-2.5
                           rounded-xl hover:bg-red-100 transition-colors">
                Eliminar slide
            </button>
        </form>
    </div>

</div>
@endsection
