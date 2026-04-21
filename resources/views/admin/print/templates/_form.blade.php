@php $isEdit = isset($template) && $template !== null; @endphp

{{-- ── Basic Info ──────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
    <h2 class="font-alumni text-h5 text-dark">Informació bàsica</h2>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">
                Icona <span class="normal-case font-normal text-gray-400">(emoji opcional)</span>
            </label>
            <input type="text" name="icon" value="{{ old('icon', $template?->icon) }}"
                   placeholder="🖨️" maxlength="10"
                   class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div>
            <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">
                Ordre
            </label>
            <input type="number" name="sort_order"
                   value="{{ old('sort_order', $template?->sort_order ?? 0) }}" min="0"
                   class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div>
            <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">
                Preu base (€/unitat) *
            </label>
            <input type="number" name="base_price" step="0.0001" min="0"
                   value="{{ old('base_price', $template?->base_price) }}" required
                   class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div>
            <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">
                IVA (%) *
            </label>
            <input type="number" name="vat_rate" step="0.01" min="0" max="100"
                   value="{{ old('vat_rate', $template?->vat_rate ?? 21) }}" required
                   class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div>
            <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">
                Dies de producció base *
            </label>
            <input type="number" name="base_production_days" min="1" max="90"
                   value="{{ old('base_production_days', $template?->base_production_days ?? 3) }}" required
                   class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div class="flex items-end pb-1">
            <label class="flex items-center gap-2 font-outfit text-sm text-gray-600 cursor-pointer">
                <input type="checkbox" name="is_active" value="1"
                       @checked(old('is_active', $template?->is_active ?? true))
                       class="rounded accent-primary">
                Actiu
            </label>
        </div>
    </div>
</div>

{{-- ── Translations ─────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
    <h2 class="font-alumni text-h5 text-dark">Nom i descripció</h2>

    <div x-data="{ tab: 'ca' }" class="space-y-4">
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

        @foreach(['ca' => 'Català', 'es' => 'Castellà', 'en' => 'Anglès'] as $locale => $langLabel)
            <div x-show="tab === '{{ $locale }}'" class="space-y-3">
                <div>
                    <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">
                        Nom {{ $locale !== 'en' ? '*' : '' }}
                    </label>
                    <input type="text" name="name_{{ $locale }}"
                           value="{{ old('name_'.$locale, $template?->getTranslation('name', $locale, false)) }}"
                           {{ $locale !== 'en' ? 'required' : '' }}
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                                  focus:outline-none focus:ring-2 focus:ring-primary">
                    @error('name_'.$locale) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">
                        Descripció
                    </label>
                    <textarea name="description_{{ $locale }}" rows="3"
                              class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                                     focus:outline-none focus:ring-2 focus:ring-primary resize-none"
                    >{{ old('description_'.$locale, $template?->getTranslation('description', $locale, false)) }}</textarea>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- ── Options & Values ─────────────────────────────────────────────────── --}}
@php
    $optionsData = $isEdit ? $template->options->map(fn($o) => [
        'id' => $o->id, '_key' => $o->id,
        'key' => $o->key,
        'label_ca' => $o->getTranslation('label', 'ca', false) ?? '',
        'label_es' => $o->getTranslation('label', 'es', false) ?? '',
        'label_en' => $o->getTranslation('label', 'en', false) ?? '',
        'input_type' => $o->input_type,
        'is_required' => $o->is_required,
        'sort_order' => $o->sort_order,
        'values' => $o->values->map(fn($v) => [
            'id' => $v->id, '_key' => $v->id,
            'value_key' => $v->value_key,
            'label_ca' => $v->getTranslation('label', 'ca', false) ?? '',
            'label_es' => $v->getTranslation('label', 'es', false) ?? '',
            'label_en' => $v->getTranslation('label', 'en', false) ?? '',
            'price_modifier' => $v->price_modifier,
            'price_modifier_type' => $v->price_modifier_type,
            'production_days_modifier' => $v->production_days_modifier,
            'is_default' => $v->is_default,
            'is_active' => $v->is_active,
            'sort_order' => $v->sort_order,
        ])->values()->toArray(),
    ])->values() : collect([]);
@endphp

<div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4"
     x-data="optionManager()">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-alumni text-h5 text-dark">Opcions de configuració</h2>
            <p class="font-outfit text-xs text-gray-400 mt-0.5">
                Mida, paper, color, acabat… Cada opció pot tenir valors amb modificadors de preu.
            </p>
        </div>
        <button type="button" @click="addOption()"
                class="font-outfit text-xs text-secondary hover:text-primary transition-colors
                       flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Afegir opció
        </button>
    </div>

    <div class="space-y-4">
        <template x-for="(opt, oi) in options" :key="opt._key">
            <div class="border border-gray-200 rounded-xl overflow-hidden">

                {{-- Option header --}}
                <div class="bg-gray-50 px-4 py-3 flex items-center gap-3">
                    <input type="hidden" :name="`options[${oi}][id]`" :value="opt.id">

                    <div class="grid grid-cols-12 gap-2 flex-1 items-end">
                        <div class="col-span-2">
                            <label class="font-outfit text-xs text-gray-400 mb-1 block">Clau interna *</label>
                            <input type="text" :name="`options[${oi}][key]`" x-model="opt.key"
                                   placeholder="size"
                                   class="w-full border border-gray-200 rounded-lg px-2 py-1.5
                                          font-outfit text-xs focus:outline-none focus:ring-1 focus:ring-primary">
                        </div>
                        <div class="col-span-3">
                            <label class="font-outfit text-xs text-gray-400 mb-1 block">Nom (ca) *</label>
                            <input type="text" :name="`options[${oi}][label_ca]`" x-model="opt.label_ca"
                                   placeholder="Mida"
                                   class="w-full border border-gray-200 rounded-lg px-2 py-1.5
                                          font-outfit text-xs focus:outline-none focus:ring-1 focus:ring-primary">
                        </div>
                        <div class="col-span-3">
                            <label class="font-outfit text-xs text-gray-400 mb-1 block">Nom (es)</label>
                            <input type="text" :name="`options[${oi}][label_es]`" x-model="opt.label_es"
                                   placeholder="Tamaño"
                                   class="w-full border border-gray-200 rounded-lg px-2 py-1.5
                                          font-outfit text-xs focus:outline-none focus:ring-1 focus:ring-primary">
                        </div>
                        <div class="col-span-2">
                            <label class="font-outfit text-xs text-gray-400 mb-1 block">Tipus</label>
                            <select :name="`options[${oi}][input_type]`" x-model="opt.input_type"
                                    class="w-full border border-gray-200 rounded-lg px-2 py-1.5
                                           font-outfit text-xs focus:outline-none focus:ring-1 focus:ring-primary">
                                <option value="select">Select</option>
                                <option value="radio">Radio</option>
                                <option value="toggle">Toggle</option>
                                <option value="number">Número</option>
                            </select>
                        </div>
                        <div class="col-span-1 flex items-center gap-1 pt-4">
                            <input type="checkbox" :name="`options[${oi}][is_required]`"
                                   :checked="opt.is_required"
                                   @change="opt.is_required = $event.target.checked"
                                   class="rounded accent-primary">
                            <span class="font-outfit text-xs text-gray-500">Req.</span>
                        </div>
                        <div class="col-span-1 flex items-center justify-end pt-4 gap-2">
                            <button type="button" @click="addValue(oi)"
                                    class="text-secondary hover:text-primary transition-colors"
                                    title="Afegir valor">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                            <button type="button" @click="removeOption(oi)"
                                    class="text-red-400 hover:text-red-600 transition-colors"
                                    title="Eliminar opció">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Values --}}
                <div class="divide-y divide-gray-50">
                    <template x-for="(val, vi) in opt.values" :key="val._key">
                        <div class="px-4 py-2 grid grid-cols-12 gap-2 items-center bg-white">
                            <input type="hidden" :name="`options[${oi}][values][${vi}][id]`" :value="val.id">

                            <div class="col-span-2">
                                <input type="text"
                                       :name="`options[${oi}][values][${vi}][value_key]`"
                                       x-model="val.value_key"
                                       placeholder="a4"
                                       class="w-full border border-gray-100 rounded-lg px-2 py-1.5
                                              font-outfit text-xs bg-gray-50
                                              focus:outline-none focus:ring-1 focus:ring-primary">
                            </div>
                            <div class="col-span-2">
                                <input type="text"
                                       :name="`options[${oi}][values][${vi}][label_ca]`"
                                       x-model="val.label_ca"
                                       placeholder="A4 (210×297)"
                                       class="w-full border border-gray-100 rounded-lg px-2 py-1.5
                                              font-outfit text-xs bg-gray-50
                                              focus:outline-none focus:ring-1 focus:ring-primary">
                            </div>
                            <div class="col-span-2">
                                <input type="text"
                                       :name="`options[${oi}][values][${vi}][label_es]`"
                                       x-model="val.label_es"
                                       placeholder="A4 (210×297)"
                                       class="w-full border border-gray-100 rounded-lg px-2 py-1.5
                                              font-outfit text-xs bg-gray-50
                                              focus:outline-none focus:ring-1 focus:ring-primary">
                            </div>
                            <div class="col-span-2">
                                <div class="flex gap-1">
                                    <input type="number"
                                           :name="`options[${oi}][values][${vi}][price_modifier]`"
                                           x-model="val.price_modifier"
                                           step="0.0001" placeholder="0.00"
                                           class="w-full border border-gray-100 rounded-lg px-2 py-1.5
                                                  font-outfit text-xs bg-gray-50
                                                  focus:outline-none focus:ring-1 focus:ring-primary">
                                    <select :name="`options[${oi}][values][${vi}][price_modifier_type]`"
                                            x-model="val.price_modifier_type"
                                            class="border border-gray-100 rounded-lg px-1 py-1.5
                                                   font-outfit text-xs bg-gray-50
                                                   focus:outline-none focus:ring-1 focus:ring-primary">
                                        <option value="flat">€</option>
                                        <option value="percent">%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-span-1">
                                <input type="number"
                                       :name="`options[${oi}][values][${vi}][production_days_modifier]`"
                                       x-model="val.production_days_modifier"
                                       placeholder="0" title="Dies +/-"
                                       class="w-full border border-gray-100 rounded-lg px-2 py-1.5
                                              font-outfit text-xs bg-gray-50
                                              focus:outline-none focus:ring-1 focus:ring-primary">
                            </div>
                            <div class="col-span-2 flex items-center gap-3">
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="checkbox"
                                           :name="`options[${oi}][values][${vi}][is_default]`"
                                           :checked="val.is_default"
                                           @change="val.is_default = $event.target.checked"
                                           class="rounded accent-primary">
                                    <span class="font-outfit text-xs text-gray-500">Defecte</span>
                                </label>
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="checkbox"
                                           :name="`options[${oi}][values][${vi}][is_active]`"
                                           :value="1" :checked="val.is_active"
                                           @change="val.is_active = $event.target.checked"
                                           class="rounded accent-primary">
                                    <span class="font-outfit text-xs text-gray-500">Actiu</span>
                                </label>
                            </div>
                            <div class="col-span-1 flex justify-end">
                                <button type="button" @click="removeValue(oi, vi)"
                                        class="text-red-300 hover:text-red-500 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>

                    <div x-show="opt.values.length === 0"
                         class="px-4 py-3 font-outfit text-xs text-gray-400 text-center bg-white">
                        Cap valor. Fes clic en + per afegir-ne.
                    </div>
                </div>

                {{-- Column headers for values (visible when values exist) --}}
                <div x-show="opt.values.length > 0"
                     class="px-4 py-1.5 grid grid-cols-12 gap-2 bg-gray-50 border-t border-gray-100">
                    <div class="col-span-2 font-outfit text-xs text-gray-400">Clau</div>
                    <div class="col-span-2 font-outfit text-xs text-gray-400">Label (ca)</div>
                    <div class="col-span-2 font-outfit text-xs text-gray-400">Label (es)</div>
                    <div class="col-span-2 font-outfit text-xs text-gray-400">Preu mod. (€/%)</div>
                    <div class="col-span-1 font-outfit text-xs text-gray-400">Dies</div>
                    <div class="col-span-2 font-outfit text-xs text-gray-400">Flags</div>
                </div>
            </div>
        </template>

        <div x-show="options.length === 0"
             class="py-8 text-center font-outfit text-xs text-gray-400
                    border border-dashed border-gray-200 rounded-xl">
            Cap opció definida. Afegeix opcions per permetre la configuració del treball.
        </div>
    </div>
</div>

{{-- ── Quantity Tiers ───────────────────────────────────────────────────── --}}
@php
    $tiersData = $isEdit ? $template->quantityTiers->map(fn($t) => [
        'id' => $t->id, '_key' => $t->id,
        'min_quantity' => $t->min_quantity,
        'discount_percent' => $t->discount_percent,
        'label_ca' => $t->getTranslation('label', 'ca', false) ?? '',
        'label_es' => $t->getTranslation('label', 'es', false) ?? '',
        'is_active' => $t->is_active,
    ])->values() : collect([]);
@endphp

<div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4"
     x-data="tierManager()">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-alumni text-h5 text-dark">Tarifes per volum</h2>
            <p class="font-outfit text-xs text-gray-400 mt-0.5">
                Descomptes automàtics en funció de la quantitat sol·licitada.
            </p>
        </div>
        <button type="button" @click="addTier()"
                class="font-outfit text-xs text-secondary hover:text-primary transition-colors
                       flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Afegir tram
        </button>
    </div>

    <div class="space-y-2">
        <template x-for="(t, ti) in tiers" :key="t._key">
            <div class="grid grid-cols-12 gap-2 items-center bg-gray-50 rounded-xl px-4 py-3">
                <input type="hidden" :name="`tiers[${ti}][id]`" :value="t.id">

                <div class="col-span-2">
                    <label class="font-outfit text-xs text-gray-400 mb-1 block">Qty mín.</label>
                    <input type="number" :name="`tiers[${ti}][min_quantity]`" x-model="t.min_quantity"
                           min="1" placeholder="100"
                           class="w-full border border-gray-200 rounded-lg px-2 py-1.5
                                  font-outfit text-xs focus:outline-none focus:ring-1 focus:ring-primary">
                </div>
                <div class="col-span-2">
                    <label class="font-outfit text-xs text-gray-400 mb-1 block">Descompte %</label>
                    <input type="number" :name="`tiers[${ti}][discount_percent]`" x-model="t.discount_percent"
                           step="0.01" min="0" max="100" placeholder="10"
                           class="w-full border border-gray-200 rounded-lg px-2 py-1.5
                                  font-outfit text-xs focus:outline-none focus:ring-1 focus:ring-primary">
                </div>
                <div class="col-span-3">
                    <label class="font-outfit text-xs text-gray-400 mb-1 block">Etiqueta (ca)</label>
                    <input type="text" :name="`tiers[${ti}][label_ca]`" x-model="t.label_ca"
                           placeholder="100+ unitats"
                           class="w-full border border-gray-200 rounded-lg px-2 py-1.5
                                  font-outfit text-xs focus:outline-none focus:ring-1 focus:ring-primary">
                </div>
                <div class="col-span-3">
                    <label class="font-outfit text-xs text-gray-400 mb-1 block">Etiqueta (es)</label>
                    <input type="text" :name="`tiers[${ti}][label_es]`" x-model="t.label_es"
                           placeholder="100+ unidades"
                           class="w-full border border-gray-200 rounded-lg px-2 py-1.5
                                  font-outfit text-xs focus:outline-none focus:ring-1 focus:ring-primary">
                </div>
                <div class="col-span-2 flex items-center justify-between pt-4">
                    <label class="flex items-center gap-1 cursor-pointer">
                        <input type="checkbox" :name="`tiers[${ti}][is_active]`"
                               :checked="t.is_active"
                               @change="t.is_active = $event.target.checked"
                               class="rounded accent-primary">
                        <span class="font-outfit text-xs text-gray-500">Actiu</span>
                    </label>
                    <button type="button" @click="removeTier(ti)"
                            class="text-red-400 hover:text-red-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </template>

        <div x-show="tiers.length === 0"
             class="py-6 text-center font-outfit text-xs text-gray-400
                    border border-dashed border-gray-200 rounded-xl">
            Sense trams. S'aplicarà el preu base per a totes les quantitats.
        </div>
    </div>
</div>

{{-- ── Compatibility Rules ──────────────────────────────────────────────── --}}
@php
    $rulesData = $isEdit ? $template->compatibilityRules->map(fn($r) => [
        'id' => $r->id, '_key' => $r->id,
        'rule_type' => $r->rule_type,
        'condition_option_key' => $r->condition_option_key,
        'condition_value_key' => $r->condition_value_key,
        'target_option_key' => $r->target_option_key,
        'target_value_key' => $r->target_value_key ?? '',
        'message_ca' => $r->getTranslation('message', 'ca', false) ?? '',
        'message_es' => $r->getTranslation('message', 'es', false) ?? '',
    ])->values() : collect([]);
@endphp

<div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4"
     x-data="ruleManager()">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-alumni text-h5 text-dark">Regles de compatibilitat</h2>
            <p class="font-outfit text-xs text-gray-400 mt-0.5">
                Combinacions incompatibles d'opcions que s'avisen o es bloquegen al configurador.
            </p>
        </div>
        <button type="button" @click="addRule()"
                class="font-outfit text-xs text-secondary hover:text-primary transition-colors
                       flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Afegir regla
        </button>
    </div>

    <div class="space-y-3">
        <template x-for="(r, ri) in rules" :key="r._key">
            <div class="border border-gray-100 rounded-xl p-4 space-y-2 bg-gray-50">
                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-2">
                        <label class="font-outfit text-xs text-gray-400 mb-1 block">Tipus</label>
                        <select :name="`rules[${ri}][rule_type]`" x-model="r.rule_type"
                                class="w-full border border-gray-200 rounded-lg px-2 py-1.5
                                       font-outfit text-xs focus:outline-none focus:ring-1 focus:ring-primary">
                            <option value="incompatible">Incompatible</option>
                            <option value="warning">Avís</option>
                            <option value="requires">Requereix</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="font-outfit text-xs text-gray-400 mb-1 block">Si opció</label>
                        <input type="text" :name="`rules[${ri}][condition_option_key]`"
                               x-model="r.condition_option_key" placeholder="sides"
                               class="w-full border border-gray-200 rounded-lg px-2 py-1.5
                                      font-outfit text-xs focus:outline-none focus:ring-1 focus:ring-primary">
                    </div>
                    <div class="col-span-2">
                        <label class="font-outfit text-xs text-gray-400 mb-1 block">= valor</label>
                        <input type="text" :name="`rules[${ri}][condition_value_key]`"
                               x-model="r.condition_value_key" placeholder="single"
                               class="w-full border border-gray-200 rounded-lg px-2 py-1.5
                                      font-outfit text-xs focus:outline-none focus:ring-1 focus:ring-primary">
                    </div>
                    <div class="col-span-2">
                        <label class="font-outfit text-xs text-gray-400 mb-1 block">Afecta opció</label>
                        <input type="text" :name="`rules[${ri}][target_option_key]`"
                               x-model="r.target_option_key" placeholder="lamination"
                               class="w-full border border-gray-200 rounded-lg px-2 py-1.5
                                      font-outfit text-xs focus:outline-none focus:ring-1 focus:ring-primary">
                    </div>
                    <div class="col-span-2">
                        <label class="font-outfit text-xs text-gray-400 mb-1 block">= valor (buit=tot)</label>
                        <input type="text" :name="`rules[${ri}][target_value_key]`"
                               x-model="r.target_value_key" placeholder="glossy"
                               class="w-full border border-gray-200 rounded-lg px-2 py-1.5
                                      font-outfit text-xs focus:outline-none focus:ring-1 focus:ring-primary">
                    </div>
                    <div class="col-span-2 flex items-end justify-end pb-0.5">
                        <button type="button" @click="removeRule(ri)"
                                class="text-red-400 hover:text-red-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="font-outfit text-xs text-gray-400 mb-1 block">Missatge (ca)</label>
                        <input type="text" :name="`rules[${ri}][message_ca]`" x-model="r.message_ca"
                               placeholder="No es pot aplicar laminat amb impressió simple…"
                               class="w-full border border-gray-200 rounded-lg px-2 py-1.5
                                      font-outfit text-xs focus:outline-none focus:ring-1 focus:ring-primary">
                    </div>
                    <div>
                        <label class="font-outfit text-xs text-gray-400 mb-1 block">Missatge (es)</label>
                        <input type="text" :name="`rules[${ri}][message_es]`" x-model="r.message_es"
                               placeholder="No se puede aplicar laminado con impresión simple…"
                               class="w-full border border-gray-200 rounded-lg px-2 py-1.5
                                      font-outfit text-xs focus:outline-none focus:ring-1 focus:ring-primary">
                    </div>
                </div>
            </div>
        </template>

        <div x-show="rules.length === 0"
             class="py-6 text-center font-outfit text-xs text-gray-400
                    border border-dashed border-gray-200 rounded-xl">
            Sense regles. Totes les combinacions d'opcions seran permeses.
        </div>
    </div>
</div>

{{-- ── Artwork Samples ──────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
    <h2 class="font-alumni text-h5 text-dark">Mostres visuals</h2>
    <p class="font-outfit text-xs text-gray-400 -mt-2">
        Imatges d'exemple que es mostraran al configurador de la botiga.
    </p>

    @if($isEdit && $template->artworks->isNotEmpty())
        <div class="grid grid-cols-4 sm:grid-cols-6 gap-3">
            @foreach($template->artworks as $artwork)
                <div class="relative group" id="artwork-{{ $artwork->id }}">
                    <img src="{{ $artwork->url }}" alt=""
                         class="w-full aspect-square object-cover rounded-xl border border-gray-100">
                    <button type="button"
                            onclick="removeArtwork({{ $artwork->id }})"
                            class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full
                                   text-xs flex items-center justify-center
                                   opacity-0 group-hover:opacity-100 transition-opacity">×</button>
                    <input type="hidden" name="delete_artworks[]" value="{{ $artwork->id }}"
                           disabled id="del-artwork-{{ $artwork->id }}">
                </div>
            @endforeach
        </div>
    @endif

    <input type="file" name="artworks[]" accept="image/*" multiple
           class="font-outfit text-sm text-gray-600 block">
    <p class="font-outfit text-xs text-gray-400">JPG, PNG, WebP — màx. 8 MB cadascuna.</p>
</div>

{{-- ── Specifications (fitxa tècnica) ──────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
    <h2 class="font-alumni text-h5 text-dark">Fitxa tècnica / Especificacions</h2>
    <p class="font-outfit text-xs text-gray-400 -mt-2">
        Document descarregable que el client pot baixar des del configurador. PDF, AI, EPS, ZIP o PSD.
    </p>

    @if($isEdit && $template?->specifications_path)
        <div class="flex items-center gap-4 p-4 bg-blue-50 rounded-xl border border-blue-100">
            <svg class="w-8 h-8 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <div class="flex-1 min-w-0">
                <p class="font-outfit text-sm text-dark font-medium truncate">
                    {{ $template->specifications_label ?: basename($template->specifications_path) }}
                </p>
                <p class="font-outfit text-xs text-gray-400">
                    {{ basename($template->specifications_path) }}
                </p>
            </div>
            <label class="flex items-center gap-1.5 font-outfit text-xs text-red-500 cursor-pointer">
                <input type="checkbox" name="delete_specifications" value="1"
                       class="rounded accent-red-500">
                Eliminar
            </label>
        </div>
    @endif

    <div class="space-y-3">
        <div>
            <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">
                Etiqueta del botó <span class="normal-case font-normal text-gray-400">(opcional)</span>
            </label>
            <input type="text" name="specifications_label"
                   value="{{ old('specifications_label', $template?->specifications_label) }}"
                   placeholder="Descarregar fitxa tècnica"
                   class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div>
            <input type="file" name="specifications"
                   accept=".pdf,.ai,.eps,.zip,.psd"
                   class="font-outfit text-sm text-gray-600 block">
            <p class="font-outfit text-xs text-gray-400 mt-1">PDF, AI, EPS, ZIP, PSD — màx. 50 MB.</p>
        </div>
    </div>
</div>

{{-- ── Scripts ──────────────────────────────────────────────────────────── --}}
@push('scripts')
<script>
function optionManager() {
    return {
        options: @json($optionsData),
        _next: {{ $isEdit ? ($template->options->count() + 100) : 1 }},

        addOption() {
            this.options.push({
                id: null, _key: 'new_opt_' + this._next++,
                key: '', label_ca: '', label_es: '', label_en: '',
                input_type: 'select', is_required: true, sort_order: this.options.length,
                values: [],
            });
        },
        removeOption(i) { this.options.splice(i, 1); },

        addValue(oi) {
            const opt = this.options[oi];
            opt.values.push({
                id: null, _key: 'new_val_' + this._next++,
                value_key: '', label_ca: '', label_es: '', label_en: '',
                price_modifier: 0, price_modifier_type: 'flat',
                production_days_modifier: 0,
                is_default: opt.values.length === 0,
                is_active: true,
                sort_order: opt.values.length,
            });
        },
        removeValue(oi, vi) { this.options[oi].values.splice(vi, 1); },
    };
}

function tierManager() {
    return {
        tiers: @json($tiersData),
        _next: {{ $isEdit ? ($template->quantityTiers->count() + 100) : 1 }},

        addTier() {
            this.tiers.push({
                id: null, _key: 'new_tier_' + this._next++,
                min_quantity: '', discount_percent: '', label_ca: '', label_es: '',
                is_active: true,
            });
        },
        removeTier(i) { this.tiers.splice(i, 1); },
    };
}

function ruleManager() {
    return {
        rules: @json($rulesData),
        _next: {{ $isEdit ? ($template->compatibilityRules->count() + 100) : 1 }},

        addRule() {
            this.rules.push({
                id: null, _key: 'new_rule_' + this._next++,
                rule_type: 'incompatible',
                condition_option_key: '', condition_value_key: '',
                target_option_key: '', target_value_key: '',
                message_ca: '', message_es: '',
            });
        },
        removeRule(i) { this.rules.splice(i, 1); },
    };
}

function removeArtwork(id) {
    document.getElementById('del-artwork-' + id).disabled = false;
    document.getElementById('artwork-' + id).style.opacity = '0.3';
}
</script>
@endpush
