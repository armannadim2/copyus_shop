@php $isEdit = isset($product) && $product !== null; @endphp

{{-- ── AI Content Generator ──────────────────────────────────────────────── --}}
<div class="bg-gradient-to-r from-violet-50 to-indigo-50 border border-violet-200 rounded-2xl p-5"
     x-data="aiGenerator()">

    <div class="flex items-start justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-violet-100 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <p class="font-outfit font-semibold text-sm text-dark">Genera contingut amb IA</p>
                <p class="font-outfit text-xs text-gray-500 mt-0.5">
                    Genera nom, descripció i camps SEO en català, castellà i anglès.
                    Omple alguns camps bàsics o afegeix una pista i la IA farà la resta.
                </p>
            </div>
        </div>
        <button type="button" @click="open = !open"
                class="shrink-0 font-outfit text-xs text-violet-600 hover:text-violet-800 transition-colors flex items-center gap-1">
            <span x-text="open ? 'Tancar' : 'Obrir'">Obrir</span>
            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
    </div>

    <div x-show="open" x-transition class="mt-4 space-y-4">

        {{-- Optional extra hint --}}
        <div>
            <label class="font-outfit text-xs font-semibold tracking-widest text-violet-700 uppercase mb-1 block">
                Pista addicional <span class="normal-case tracking-normal font-normal text-gray-400">(opcional)</span>
            </label>
            <input type="text" x-model="hint" maxlength="500"
                   placeholder="ex: paper reciclat 80g/m², format A4, caixa de 500 fulls…"
                   class="w-full border border-violet-200 rounded-xl px-3 py-2 font-outfit text-sm
                          focus:outline-none focus:ring-2 focus:ring-violet-400 bg-white">
        </div>

        {{-- Image preview hint --}}
        <p class="font-outfit text-xs text-gray-400 -mt-2">
            Si has seleccionat una imatge a la secció de imatges, la IA la farà servir automàticament.
        </p>

        {{-- What will be generated --}}
        <div class="flex flex-wrap gap-2">
            @foreach(['Nom (3 idiomes)','Descripció curta','Descripció completa','Meta títol','Meta descripció','Paraules clau'] as $label)
                <span class="inline-flex items-center gap-1 bg-violet-100 text-violet-700 font-outfit text-xs px-2.5 py-1 rounded-full">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    {{ $label }}
                </span>
            @endforeach
        </div>

        {{-- Error message --}}
        <div x-show="errorMsg" x-transition
             class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 font-outfit text-xs text-red-600"
             x-text="errorMsg"></div>

        {{-- Success message --}}
        <div x-show="successMsg" x-transition
             class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 font-outfit text-xs text-green-700"
             x-text="successMsg"></div>

        {{-- Generate button --}}
        <button type="button" @click="generate()"
                :disabled="loading"
                class="flex items-center gap-2 bg-violet-600 hover:bg-violet-700 disabled:opacity-60
                       disabled:cursor-not-allowed text-white font-outfit text-sm font-semibold
                       px-5 py-2.5 rounded-xl transition-all">
            <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <span x-text="loading ? 'Generant…' : 'Genera amb IA'">Genera amb IA</span>
        </button>
    </div>
</div>

{{-- ── Basic Info ──────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
    <h2 class="font-alumni text-h5 text-dark">Informació bàsica</h2>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">Categoria *</label>
            <select name="category_id" required
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                           focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">Seleccionar categoria</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        @selected(old('category_id', $product?->category_id) == $category->id)>
                        {{ $category->getTranslation('name', 'ca') }}
                    </option>
                @endforeach
            </select>
            @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">SKU *</label>
            <input type="text" name="sku" value="{{ old('sku', $product?->sku) }}" required
                   class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary">
            @error('sku') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">Marca</label>
            <input type="text" name="brand" value="{{ old('brand', $product?->brand) }}"
                   class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div>
            <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">Unitat *</label>
            <input type="text" name="unit" value="{{ old('unit', $product?->unit) }}" required
                   placeholder="unitat, kg, m2, pack..."
                   class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary">
            @error('unit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">Preu base (€) *</label>
            <input type="number" name="price" value="{{ old('price', $product?->price) }}"
                   step="0.0001" min="0" required
                   class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary">
            @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">IVA (%) *</label>
            <input type="number" name="vat_rate" value="{{ old('vat_rate', $product?->vat_rate ?? 21) }}"
                   step="0.01" min="0" max="100" required
                   class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary">
            @error('vat_rate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">Quantitat mínima *</label>
            <input type="number" name="min_order_quantity"
                   value="{{ old('min_order_quantity', $product?->min_order_quantity ?? 1) }}"
                   min="1" required
                   class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary">
            @error('min_order_quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex items-end gap-6 pb-1">
            <label class="flex items-center gap-2 font-outfit text-sm text-gray-600 cursor-pointer">
                <input type="checkbox" name="is_active" value="1"
                       @checked(old('is_active', $product?->is_active ?? true))
                       class="rounded accent-primary">
                Actiu
            </label>
            <label class="flex items-center gap-2 font-outfit text-sm text-gray-600 cursor-pointer">
                <input type="checkbox" name="is_featured" value="1"
                       @checked(old('is_featured', $product?->is_featured ?? false))
                       class="rounded accent-primary">
                Destacat
            </label>
        </div>
    </div>
</div>

{{-- ── Stock & Alerts ───────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
    <h2 class="font-alumni text-h5 text-dark">Estoc i alertes</h2>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">Estoc actual *</label>
            <input type="number" name="stock" value="{{ old('stock', $product?->stock ?? 0) }}"
                   min="0" required
                   class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary">
            @error('stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">Llindar d'estoc baix</label>
            <input type="number" name="low_stock_threshold"
                   value="{{ old('low_stock_threshold', $product?->low_stock_threshold) }}"
                   min="1" placeholder="ex: 10"
                   class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary">
            <p class="font-outfit text-xs text-gray-400 mt-1">Avisa quan l'estoc sigui ≤ aquest valor</p>
        </div>
        <div class="flex items-end pb-1">
            <label class="flex items-center gap-2 font-outfit text-sm text-gray-600 cursor-pointer">
                <input type="checkbox" name="notify_low_stock" value="1"
                       @checked(old('notify_low_stock', $product?->notify_low_stock ?? false))
                       class="rounded accent-primary">
                Notificar per correu
            </label>
        </div>
    </div>

    @if($isEdit && $product->is_low_stock)
        <div class="flex items-center gap-2 bg-orange-50 border border-orange-200 rounded-xl px-4 py-3">
            <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="font-outfit text-xs text-orange-700">
                <strong>Estoc baix!</strong> — {{ $product->stock }} unitats restants (llindar: {{ $product->low_stock_threshold }})
            </p>
        </div>
    @endif
</div>

{{-- ── Tags ─────────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
    <h2 class="font-alumni text-h5 text-dark">Etiquetes</h2>

    @php
        $currentTags = $isEdit
            ? $product->tags->pluck('name')->implode(', ')
            : old('tags', '');
    @endphp

    <div>
        <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">
            Etiquetes <span class="normal-case tracking-normal font-normal text-gray-400">(separades per coma)</span>
        </label>
        <input type="text" name="tags" value="{{ old('tags', $currentTags) }}"
               placeholder="impressió, paper, A4, eco..."
               class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                      focus:outline-none focus:ring-2 focus:ring-primary">
        @if($allTags->isNotEmpty())
            <div class="flex flex-wrap gap-2 mt-3">
                @foreach($allTags as $tag)
                    <button type="button"
                            onclick="toggleTag('{{ $tag->name }}')"
                            class="font-outfit text-xs px-3 py-1 rounded-full border border-gray-200
                                   hover:border-primary hover:text-primary transition-colors tag-pill
                                   {{ str_contains($currentTags, $tag->name) ? 'bg-primary text-white border-primary' : 'text-gray-500' }}">
                        {{ $tag->name }}
                    </button>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- ── Translations ─────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
    <h2 class="font-alumni text-h5 text-dark">Traduccions</h2>

    <div x-data="{ tab: 'ca' }" class="space-y-4">
        {{-- Tab selector --}}
        <div class="flex gap-1 bg-gray-100 rounded-xl p-1 w-fit">
            @foreach(['ca' => 'Català', 'es' => 'Castellà', 'en' => 'Anglès'] as $locale => $label)
                <button type="button"
                        @click="tab = '{{ $locale }}'"
                        :class="tab === '{{ $locale }}' ? 'bg-white shadow-sm text-dark' : 'text-gray-500 hover:text-dark'"
                        class="font-outfit text-xs px-4 py-1.5 rounded-lg transition-all">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Tab panels --}}
        @foreach(['ca' => 'Català', 'es' => 'Castellà', 'en' => 'Anglès'] as $locale => $label)
            <div x-show="tab === '{{ $locale }}'" class="space-y-3">
                <div>
                    <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">
                        Nom {{ $locale !== 'en' ? '*' : '' }}
                    </label>
                    <input type="text" name="name_{{ $locale }}"
                           value="{{ old('name_'.$locale, $product?->getTranslation('name', $locale, false)) }}"
                           {{ $locale !== 'en' ? 'required' : '' }}
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                                  focus:outline-none focus:ring-2 focus:ring-primary">
                    @error('name_'.$locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">Descripció curta</label>
                    <input type="text" name="short_description_{{ $locale }}"
                           value="{{ old('short_description_'.$locale, $product?->getTranslation('short_description', $locale, false)) }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                                  focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">Descripció completa</label>
                    <textarea name="description_{{ $locale }}" rows="4"
                              class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                                     focus:outline-none focus:ring-2 focus:ring-primary resize-none"
                    >{{ old('description_'.$locale, $product?->getTranslation('description', $locale, false)) }}</textarea>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- ── Image & Gallery ──────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
    <h2 class="font-alumni text-h5 text-dark">Imatge principal i galeria</h2>

    {{-- Thumbnail --}}
    <div>
        <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-2 block">Imatge principal</label>
        <div class="flex items-start gap-4">
            @if($isEdit && $product->image)
                <div class="relative group">
                    <img src="{{ asset('storage/'.$product->image) }}" alt="Imatge actual"
                         class="w-24 h-24 object-cover rounded-xl border border-gray-100" id="thumb-preview">
                    <label class="flex items-center gap-1 mt-1 cursor-pointer">
                        <input type="checkbox" name="remove_image" value="1" class="rounded accent-red-500 text-xs">
                        <span class="font-outfit text-xs text-red-500">Eliminar</span>
                    </label>
                </div>
            @else
                <div class="w-24 h-24 bg-light rounded-xl flex items-center justify-center text-3xl" id="thumb-preview-empty">
                    📦
                </div>
            @endif
            <div class="flex-1">
                <input type="file" name="image" accept="image/*"
                       onchange="previewThumb(this)"
                       class="font-outfit text-sm text-gray-600 block">
                <p class="font-outfit text-xs text-gray-400 mt-1">JPG, PNG, WebP — màx. 4 MB</p>
            </div>
        </div>
    </div>

    {{-- Gallery --}}
    <div>
        <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-2 block">
            Galeria d'imatges
            <span class="normal-case tracking-normal font-normal text-gray-400">(múltiples)</span>
        </label>

        {{-- Existing gallery --}}
        @if($isEdit && $product->images->isNotEmpty())
            <div class="grid grid-cols-4 sm:grid-cols-6 gap-3 mb-4" id="gallery-grid">
                @foreach($product->images as $img)
                    <div class="relative group" id="gallery-item-{{ $img->id }}">
                        <img src="{{ asset('storage/'.$img->path) }}"
                             class="w-full aspect-square object-cover rounded-xl border border-gray-100">
                        <button type="button"
                                onclick="removeGalleryImage({{ $img->id }}, this)"
                                class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full
                                       text-xs flex items-center justify-center opacity-0 group-hover:opacity-100
                                       transition-opacity">
                            ×
                        </button>
                        <input type="hidden" name="delete_images[]" value="{{ $img->id }}" disabled
                               id="del-{{ $img->id }}">
                    </div>
                @endforeach
            </div>
        @endif

        <input type="file" name="gallery[]" accept="image/*" multiple
               class="font-outfit text-sm text-gray-600 block"
               onchange="previewGallery(this)">
        <p class="font-outfit text-xs text-gray-400 mt-1">Selecciona múltiples imatges. Màx. 4 MB cadascuna.</p>

        {{-- Preview container for new uploads --}}
        <div class="grid grid-cols-4 sm:grid-cols-6 gap-3 mt-3" id="gallery-preview"></div>
    </div>
</div>

{{-- ── Variants ─────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4" x-data="variantManager()">
    <div class="flex items-center justify-between">
        <h2 class="font-alumni text-h5 text-dark">Variants del producte</h2>
        <button type="button" @click="add()"
                class="font-outfit text-xs text-secondary hover:text-primary transition-colors flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Afegir variant
        </button>
    </div>

    <p class="font-outfit text-xs text-gray-400">
        Defineix variacions del producte (mida, color, format…). Cada variant pot tenir el seu propi estoc i ajust de preu.
    </p>

    <div class="space-y-3">
        <template x-for="(v, i) in variants" :key="v._key">
            <div class="grid grid-cols-12 gap-2 items-start bg-gray-50 rounded-xl p-3">
                <input type="hidden" :name="`variants[${i}][id]`" :value="v.id">

                <div class="col-span-2">
                    <label class="font-outfit text-xs text-gray-400 mb-1 block">Tipus</label>
                    <select :name="`variants[${i}][type]`" x-model="v.type"
                            class="w-full border border-gray-200 rounded-lg px-2 py-1.5 font-outfit text-xs
                                   focus:outline-none focus:ring-1 focus:ring-primary">
                        <option value="">—</option>
                        <option value="mida">Mida</option>
                        <option value="color">Color</option>
                        <option value="format">Format</option>
                        <option value="gramatge">Gramatge</option>
                        <option value="acabat">Acabat</option>
                        <option value="altre">Altre</option>
                    </select>
                </div>
                <div class="col-span-3">
                    <label class="font-outfit text-xs text-gray-400 mb-1 block">Valor</label>
                    <input type="text" :name="`variants[${i}][value]`" x-model="v.value"
                           placeholder="A4, Vermell, PDF..."
                           class="w-full border border-gray-200 rounded-lg px-2 py-1.5 font-outfit text-xs
                                  focus:outline-none focus:ring-1 focus:ring-primary">
                </div>
                <div class="col-span-2">
                    <label class="font-outfit text-xs text-gray-400 mb-1 block">SKU variant</label>
                    <input type="text" :name="`variants[${i}][sku]`" x-model="v.sku"
                           class="w-full border border-gray-200 rounded-lg px-2 py-1.5 font-outfit text-xs
                                  focus:outline-none focus:ring-1 focus:ring-primary">
                </div>
                <div class="col-span-2">
                    <label class="font-outfit text-xs text-gray-400 mb-1 block">Ajust preu (€)</label>
                    <input type="number" :name="`variants[${i}][price_adjustment]`"
                           x-model="v.price_adjustment"
                           step="0.01" placeholder="0.00"
                           class="w-full border border-gray-200 rounded-lg px-2 py-1.5 font-outfit text-xs
                                  focus:outline-none focus:ring-1 focus:ring-primary">
                </div>
                <div class="col-span-1">
                    <label class="font-outfit text-xs text-gray-400 mb-1 block">Stock</label>
                    <input type="number" :name="`variants[${i}][stock]`" x-model="v.stock"
                           min="0"
                           class="w-full border border-gray-200 rounded-lg px-2 py-1.5 font-outfit text-xs
                                  focus:outline-none focus:ring-1 focus:ring-primary">
                </div>
                <div class="col-span-1 flex flex-col items-center gap-2 pt-5">
                    <label class="flex items-center gap-1 cursor-pointer">
                        <input type="checkbox" :name="`variants[${i}][is_active]`" :checked="v.is_active"
                               @change="v.is_active = $event.target.checked"
                               class="rounded accent-primary">
                        <span class="font-outfit text-xs text-gray-500">Actiu</span>
                    </label>
                    <button type="button" @click="remove(i)"
                            class="text-red-400 hover:text-red-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>

                {{-- Variant image (optional) --}}
                <div class="col-span-12 flex items-center gap-3 pt-2 mt-1 border-t border-gray-100">
                    <img x-show="v.image_url && !v.remove_image"
                         :src="v.image_url"
                         class="w-12 h-12 object-cover rounded-lg border border-gray-100 shrink-0">
                    <div class="flex-1">
                        <label class="font-outfit text-xs text-gray-400 mb-1 block">
                            Imatge variant <span class="text-gray-300 normal-case">(opcional)</span>
                        </label>
                        <input type="file" :name="`variants[${i}][image]`" accept="image/*"
                               class="font-outfit text-xs text-gray-500 block">
                    </div>
                    <label x-show="v.image_url" class="flex items-center gap-1 cursor-pointer shrink-0">
                        <input type="checkbox" :name="`variants[${i}][remove_image]`"
                               x-model="v.remove_image"
                               value="1"
                               class="rounded accent-red-500">
                        <span class="font-outfit text-xs text-red-400">Eliminar</span>
                    </label>
                </div>
            </div>
        </template>

        <div x-show="variants.length === 0"
             class="py-6 text-center font-outfit text-xs text-gray-400 border border-dashed border-gray-200 rounded-xl">
            Cap variant definida. Afegeix variants si el producte té mides, colors o formats.
        </div>
    </div>
</div>

{{-- ── Price Tiers ──────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4" x-data="tierManager()">
    <div class="flex items-center justify-between">
        <h2 class="font-alumni text-h5 text-dark">Tarifes per volum o client</h2>
        <button type="button" @click="add()"
                class="font-outfit text-xs text-secondary hover:text-primary transition-colors flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Afegir tarifa
        </button>
    </div>

    <p class="font-outfit text-xs text-gray-400">
        Defineix preus especials per quantitat mínima de compra o per un client concret. La tarifa més específica tindrà prioritat.
    </p>

    <div class="space-y-3">
        <template x-for="(t, i) in tiers" :key="t._key">
            <div class="grid grid-cols-12 gap-2 items-start bg-gray-50 rounded-xl p-3">
                <input type="hidden" :name="`tiers[${i}][id]`" :value="t.id">

                <div class="col-span-3">
                    <label class="font-outfit text-xs text-gray-400 mb-1 block">Client específic</label>
                    <select :name="`tiers[${i}][user_id]`" x-model="t.user_id"
                            class="w-full border border-gray-200 rounded-lg px-2 py-1.5 font-outfit text-xs
                                   focus:outline-none focus:ring-1 focus:ring-primary">
                        <option value="">Qualsevol client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->company_name ?? $client->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-1">
                    <label class="font-outfit text-xs text-gray-400 mb-1 block">Qty mín.</label>
                    <input type="number" :name="`tiers[${i}][min_quantity]`" x-model="t.min_quantity"
                           min="1" value="1"
                           class="w-full border border-gray-200 rounded-lg px-2 py-1.5 font-outfit text-xs
                                  focus:outline-none focus:ring-1 focus:ring-primary">
                </div>
                <div class="col-span-2">
                    <label class="font-outfit text-xs text-gray-400 mb-1 block">Preu (€) *</label>
                    <input type="number" :name="`tiers[${i}][price]`" x-model="t.price"
                           step="0.0001" min="0"
                           class="w-full border border-gray-200 rounded-lg px-2 py-1.5 font-outfit text-xs
                                  focus:outline-none focus:ring-1 focus:ring-primary">
                </div>
                <div class="col-span-2">
                    <label class="font-outfit text-xs text-gray-400 mb-1 block">Etiqueta</label>
                    <input type="text" :name="`tiers[${i}][label]`" x-model="t.label"
                           placeholder="Majoria, VIP..."
                           class="w-full border border-gray-200 rounded-lg px-2 py-1.5 font-outfit text-xs
                                  focus:outline-none focus:ring-1 focus:ring-primary">
                </div>
                <div class="col-span-2">
                    <label class="font-outfit text-xs text-gray-400 mb-1 block">Vàlid des de</label>
                    <input type="date" :name="`tiers[${i}][valid_from]`" x-model="t.valid_from"
                           class="w-full border border-gray-200 rounded-lg px-2 py-1.5 font-outfit text-xs
                                  focus:outline-none focus:ring-1 focus:ring-primary">
                </div>
                <div class="col-span-2">
                    <label class="font-outfit text-xs text-gray-400 mb-1 block">Fins a</label>
                    <input type="date" :name="`tiers[${i}][valid_until]`" x-model="t.valid_until"
                           class="w-full border border-gray-200 rounded-lg px-2 py-1.5 font-outfit text-xs
                                  focus:outline-none focus:ring-1 focus:ring-primary">
                </div>
                <div class="col-span-12 flex items-center justify-between pt-1">
                    <label class="flex items-center gap-1 cursor-pointer">
                        <input type="checkbox" :name="`tiers[${i}][is_active]`" :checked="t.is_active"
                               @change="t.is_active = $event.target.checked"
                               class="rounded accent-primary">
                        <span class="font-outfit text-xs text-gray-500">Tarifa activa</span>
                    </label>
                    <button type="button" @click="remove(i)"
                            class="font-outfit text-xs text-red-400 hover:text-red-600 transition-colors flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Eliminar
                    </button>
                </div>
            </div>
        </template>

        <div x-show="tiers.length === 0"
             class="py-6 text-center font-outfit text-xs text-gray-400 border border-dashed border-gray-200 rounded-xl">
            Cap tarifa especial. El preu base s'aplicarà a tots els clients.
        </div>
    </div>
</div>

{{-- ── SEO ───────────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
    <div class="flex items-center gap-2">
        <h2 class="font-alumni text-h5 text-dark">SEO</h2>
        <span class="font-outfit text-xs text-gray-400 normal-case tracking-normal font-normal">
            (opcional — si buit s'utilitza el nom i la descripció del producte)
        </span>
    </div>

    <div x-data="{ tab: 'ca' }" class="space-y-4">
        {{-- Tab selector --}}
        <div class="flex gap-1 bg-gray-100 rounded-xl p-1 w-fit">
            @foreach(['ca' => 'Català', 'es' => 'Castellà', 'en' => 'Anglès'] as $locale => $label)
                <button type="button"
                        @click="tab = '{{ $locale }}'"
                        :class="tab === '{{ $locale }}' ? 'bg-white shadow-sm text-dark' : 'text-gray-500 hover:text-dark'"
                        class="font-outfit text-xs px-4 py-1.5 rounded-lg transition-all">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        @foreach(['ca' => 'Català', 'es' => 'Castellà', 'en' => 'Anglès'] as $locale => $label)
            <div x-show="tab === '{{ $locale }}'" class="space-y-3">
                {{-- Meta title --}}
                <div>
                    <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">
                        Meta títol
                        <span class="normal-case tracking-normal font-normal text-gray-400">màx. 70 car.</span>
                    </label>
                    <input type="text" name="meta_title_{{ $locale }}"
                           value="{{ old('meta_title_'.$locale, $product?->getTranslation('meta_title', $locale, false)) }}"
                           maxlength="70"
                           placeholder="{{ $product?->getTranslation('name', $locale, false) }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                                  focus:outline-none focus:ring-2 focus:ring-primary">
                    @error('meta_title_'.$locale)
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Meta description --}}
                <div>
                    <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">
                        Meta descripció
                        <span class="normal-case tracking-normal font-normal text-gray-400">màx. 160 car.</span>
                    </label>
                    <textarea name="meta_description_{{ $locale }}" rows="2" maxlength="160"
                              placeholder="{{ $product?->getTranslation('short_description', $locale, false) }}"
                              class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                                     focus:outline-none focus:ring-2 focus:ring-primary resize-none"
                    >{{ old('meta_description_'.$locale, $product?->getTranslation('meta_description', $locale, false)) }}</textarea>
                    @error('meta_description_'.$locale)
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Meta keywords --}}
                <div>
                    <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">
                        Paraules clau
                        <span class="normal-case tracking-normal font-normal text-gray-400">(separades per coma)</span>
                    </label>
                    <input type="text" name="meta_keywords_{{ $locale }}"
                           value="{{ old('meta_keywords_'.$locale, $product?->getTranslation('meta_keywords', $locale, false)) }}"
                           maxlength="255"
                           placeholder="impressió, paper, A4..."
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                                  focus:outline-none focus:ring-2 focus:ring-primary">
                    @error('meta_keywords_'.$locale)
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- ── JS ────────────────────────────────────────────────────────────────── --}}
@push('scripts')
<script>
/* ── Tag pills ── */
function toggleTag(name) {
    const input = document.querySelector('input[name="tags"]');
    let tags = input.value.split(',').map(t => t.trim()).filter(Boolean);
    const idx = tags.indexOf(name);
    if (idx >= 0) {
        tags.splice(idx, 1);
    } else {
        tags.push(name);
    }
    input.value = tags.join(', ');
}

/* ── Thumbnail preview ── */
function previewThumb(input) {
    if (!input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const el = document.getElementById('thumb-preview') || document.getElementById('thumb-preview-empty');
        if (el.tagName === 'IMG') {
            el.src = e.target.result;
        } else {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'w-24 h-24 object-cover rounded-xl border border-gray-100';
            el.replaceWith(img);
        }
    };
    reader.readAsDataURL(input.files[0]);
}

/* ── Gallery preview ── */
function previewGallery(input) {
    const container = document.getElementById('gallery-preview');
    container.innerHTML = '';
    Array.from(input.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'relative aspect-square';
            div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded-xl border border-gray-100">`;
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

/* ── Gallery delete (existing images) ── */
function removeGalleryImage(id, btn) {
    document.getElementById(`del-${id}`).disabled = false;
    document.getElementById(`gallery-item-${id}`).style.opacity = '0.3';
    btn.disabled = true;
}

@php
    $variantsData = $isEdit ? $product->variants->map(fn($v) => [
        'id' => $v->id, '_key' => $v->id,
        'type' => $v->type, 'value' => $v->value, 'sku' => $v->sku ?? '',
        'price_adjustment' => $v->price_adjustment, 'stock' => $v->stock,
        'is_active' => $v->is_active, 'sort_order' => $v->sort_order,
        'image_url' => $v->image ? asset('storage/'.$v->image) : null,
        'remove_image' => false,
    ])->values() : collect([]);
    $tiersData = $isEdit ? $product->priceTiers->map(fn($t) => [
        'id' => $t->id, '_key' => $t->id,
        'user_id' => $t->user_id, 'min_quantity' => $t->min_quantity,
        'price' => $t->price, 'label' => $t->label ?? '',
        'valid_from' => $t->valid_from?->format('Y-m-d') ?? '',
        'valid_until' => $t->valid_until?->format('Y-m-d') ?? '',
        'is_active' => $t->is_active,
    ])->values() : collect([]);
@endphp
/* ── Variant manager ── */
function variantManager() {
    return {
        variants: @json($variantsData),
        _next: {{ $isEdit ? ($product->variants->count() + 1) : 1 }},
        add() {
            this.variants.push({ id: null, _key: 'new_' + this._next++,
                type: '', value: '', sku: '', price_adjustment: 0,
                stock: 0, is_active: true, sort_order: 0,
                image_url: null, remove_image: false });
        },
        remove(i) { this.variants.splice(i, 1); },
    };
}

/* ── Price tier manager ── */
function tierManager() {
    return {
        tiers: @json($tiersData),
        _next: {{ $isEdit ? ($product->priceTiers->count() + 1) : 1 }},
        add() {
            this.tiers.push({ id: null, _key: 'new_' + this._next++,
                user_id: '', min_quantity: 1, price: '', label: '',
                valid_from: '', valid_until: '', is_active: true });
        },
        remove(i) { this.tiers.splice(i, 1); },
    };
}

/* ── AI content generator ── */
function aiGenerator() {
    return {
        open: false,
        loading: false,
        hint: '',
        errorMsg: '',
        successMsg: '',

        async generate() {
            this.errorMsg   = '';
            this.successMsg = '';
            this.loading    = true;

            try {
                const fd = new FormData();
                fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                // Collect any name already typed (try all locales)
                const nameHint = ['ca','es','en']
                    .map(l => document.querySelector(`input[name="name_${l}"]`)?.value?.trim())
                    .filter(Boolean)[0] || '';
                if (nameHint) fd.append('hint', (this.hint ? this.hint + ' — ' : '') + nameHint);
                else if (this.hint) fd.append('hint', this.hint);

                // Brand
                const brand = document.querySelector('input[name="brand"]')?.value?.trim();
                if (brand) fd.append('brand', brand);

                // Category name from the selected <option> text
                const catSel = document.querySelector('select[name="category_id"]');
                if (catSel && catSel.selectedIndex > 0) {
                    fd.append('category', catSel.options[catSel.selectedIndex].text.trim());
                }

                // Product image if one has been chosen in the file input
                const imgFile = document.querySelector('input[name="image"]')?.files?.[0];
                if (imgFile) fd.append('image', imgFile);

                const res = await fetch('{{ route("admin.ai.product.generate") }}', {
                    method: 'POST',
                    body: fd,
                });

                const data = await res.json();

                if (!res.ok) {
                    this.errorMsg = data.error ?? 'Error desconegut. Torna-ho a intentar.';
                    return;
                }

                // ── Populate form fields ──────────────────────────────────
                const set = (selector, value) => {
                    const el = document.querySelector(selector);
                    if (el && value) el.value = value;
                };

                for (const locale of ['ca', 'es', 'en']) {
                    set(`input[name="name_${locale}"]`,                    data.name?.[locale]);
                    set(`input[name="short_description_${locale}"]`,       data.short_description?.[locale]);
                    set(`textarea[name="description_${locale}"]`,          data.description?.[locale]);
                    set(`input[name="meta_title_${locale}"]`,              data.meta_title?.[locale]);
                    set(`textarea[name="meta_description_${locale}"]`,     data.meta_description?.[locale]);
                    set(`input[name="meta_keywords_${locale}"]`,           data.meta_keywords?.[locale]);
                }

                this.successMsg = 'Contingut generat correctament. Revisa els camps i desa el producte.';
                this.open = true; // keep panel open so the success message is visible

            } catch (e) {
                this.errorMsg = 'Error de connexió. Comprova la teva connexió a internet.';
            } finally {
                this.loading = false;
            }
        },
    };
}
</script>
@endpush
