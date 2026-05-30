@extends('layouts.admin')
@section('title', 'Nou slide portada')

@section('content')
<div class="p-8 max-w-2xl">

    <div class="mb-6">
        <a href="{{ route('admin.hero-slides.index') }}"
           class="font-outfit text-sm text-gray-400 hover:text-dark transition-colors inline-flex items-center gap-1">
            ← Tornar als slides
        </a>
        <h1 class="font-alumni text-h5 text-dark mt-2">Nou slide portada</h1>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-8">
        <form method="POST" action="{{ route('admin.hero-slides.store') }}"
              enctype="multipart/form-data"
              x-data="{ preview: null }">
            @csrf

            {{-- Image upload --}}
            <div class="mb-6">
                <label class="block font-outfit text-sm font-medium text-dark mb-2">
                    Imatge <span class="text-red-500">*</span>
                </label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center
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
                    <input type="file" name="image" accept="image/*" required
                           x-ref="imageInput"
                           @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                           class="hidden">

                    <template x-if="preview">
                        <div>
                            <img :src="preview" class="mx-auto max-h-52 rounded-xl object-cover mb-3 shadow-sm">
                            <p class="font-outfit text-xs text-gray-400">Fes clic per canviar la imatge</p>
                        </div>
                    </template>
                    <template x-if="!preview">
                        <div class="py-4">
                            <p class="text-3xl mb-3">🖼️</p>
                            <p class="font-outfit text-sm text-gray-500">
                                Arrossega una imatge o fes clic per seleccionar
                            </p>
                            <p class="font-outfit text-xs text-gray-400 mt-1">JPG, PNG, WebP · màx. 4 MB</p>
                        </div>
                    </template>
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
                    S'mostra a sobre del títol, en majúscules. Ex: «Novetat», «Oferta especial».
                </p>
                <input type="text" name="eyebrow" id="eyebrow" value="{{ old('eyebrow') }}"
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
                <p class="font-outfit text-xs text-gray-400 mb-2">
                    Apareix en gran sobre la imatge. Ex: «Targetes de visita des de 9,90 €».
                </p>
                <input type="text" name="title" id="title" value="{{ old('title') }}"
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
                       value="{{ old('sort_order', 0) }}" min="0"
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
                           {{ old('is_active', '1') ? 'checked' : '' }}
                           class="w-4 h-4 accent-primary rounded">
                    <span class="font-outfit text-sm text-dark">Actiu (visible a la portada)</span>
                </label>
            </div>

            <div class="flex gap-3 pt-2 border-t border-gray-100">
                <button type="submit"
                        class="bg-primary text-white font-outfit text-sm font-medium
                               px-6 py-3 rounded-xl hover:brightness-110 active:scale-95 transition-all">
                    Desar slide
                </button>
                <a href="{{ route('admin.hero-slides.index') }}"
                   class="bg-gray-100 text-gray-600 font-outfit text-sm px-6 py-3 rounded-xl
                          hover:bg-gray-200 transition-all">
                    Cancel·lar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
