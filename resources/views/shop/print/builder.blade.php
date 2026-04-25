@extends('layouts.app')
@section('title', $template->getTranslation('name', app()->getLocale()))

@section('content')

<div class="max-w-6xl mx-auto px-4 sm:px-6 py-10"
     x-data="printBuilder()"
     x-init="init()">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 mb-8">
        <a href="{{ route('print.index') }}"
           class="font-outfit text-xs text-gray-400 hover:text-primary transition-colors">
            {{ __('app.print_breadcrumb') }}
        </a>
        <span class="text-gray-300">/</span>
        <span class="font-outfit text-xs text-dark">
            {{ $template->getTranslation('name', app()->getLocale()) }}
        </span>
    </div>

    <div class="grid lg:grid-cols-[1fr_360px] gap-8">

        {{-- ── LEFT: Configurator ─────────────────────────────────────────────── --}}
        <div class="space-y-6">

            {{-- Template header --}}
            <div class="bg-white rounded-3xl border border-gray-100 p-8">
                <div class="flex items-start gap-4 mb-2">
                    @if($template->icon)
                        <span class="text-4xl">{{ $template->icon }}</span>
                    @endif
                    <div class="flex-1 min-w-0">
                        <h1 class="font-alumni text-h3 text-dark leading-tight">
                            {{ $template->getTranslation('name', app()->getLocale()) }}
                        </h1>
                        @if($template->getTranslation('description', app()->getLocale()))
                            <p class="font-outfit text-sm text-gray-400 mt-1">
                                {{ $template->getTranslation('description', app()->getLocale()) }}
                            </p>
                        @endif
                        @if($template->specifications_path)
                            <a href="{{ Storage::url($template->specifications_path) }}"
                               target="_blank" rel="noopener"
                               class="inline-flex items-center gap-1.5 mt-3 font-outfit text-xs
                                      text-primary hover:text-secondary transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0120 9.414V19a2 2 0 01-2 2z"/>
                                </svg>
                                {{ $template->specifications_label ?: __('app.print_download_spec') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Preview gallery (when template has reference images) --}}
            @if($template->artworks->isNotEmpty())
                <div class="bg-white rounded-3xl border border-gray-100 p-6"
                     x-data="{ active: 0 }">
                    <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-4">
                        {{ __('app.print_product_samples') }}
                    </p>

                    {{-- Main image --}}
                    <div class="relative rounded-2xl overflow-hidden bg-light aspect-[4/3] mb-3">
                        @foreach($template->artworks as $i => $artwork)
                            <img src="{{ $artwork->url }}"
                                 alt="{{ $artwork->getTranslation('label', app()->getLocale()) ?: $template->getTranslation('name', app()->getLocale()) }}"
                                 x-show="active === {{ $i }}"
                                 x-transition:enter="transition-opacity duration-300"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 class="w-full h-full object-contain"
                                 style="{{ $i > 0 ? 'display:none' : '' }}">
                        @endforeach

                        {{-- Navigation arrows (more than 1 image) --}}
                        @if($template->artworks->count() > 1)
                            <button type="button"
                                    @click="active = (active - 1 + {{ $template->artworks->count() }}) % {{ $template->artworks->count() }}"
                                    class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-white/80 hover:bg-white
                                           rounded-full flex items-center justify-center shadow-sm transition-all">
                                <svg class="w-4 h-4 text-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button type="button"
                                    @click="active = (active + 1) % {{ $template->artworks->count() }}"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-white/80 hover:bg-white
                                           rounded-full flex items-center justify-center shadow-sm transition-all">
                                <svg class="w-4 h-4 text-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        @endif
                    </div>

                    {{-- Thumbnails (outside clipped container) --}}
                    @if($template->artworks->count() > 1)
                        <div class="flex gap-2 overflow-x-auto pb-1 mb-2">
                            @foreach($template->artworks as $i => $artwork)
                                <button type="button"
                                        @click="active = {{ $i }}"
                                        :class="active === {{ $i }} ? 'ring-2 ring-primary' : 'opacity-60 hover:opacity-100'"
                                        class="shrink-0 w-14 h-14 rounded-xl overflow-hidden border border-gray-100 transition-all">
                                    <img src="{{ $artwork->url }}" alt="" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif

                    {{-- Label of active image --}}
                    @foreach($template->artworks as $i => $artwork)
                        @if($artwork->getTranslation('label', app()->getLocale()))
                            <p x-show="active === {{ $i }}"
                               class="font-outfit text-xs text-gray-400 text-center"
                               style="{{ $i > 0 ? 'display:none' : '' }}">
                                {{ $artwork->getTranslation('label', app()->getLocale()) }}
                            </p>
                        @endif
                    @endforeach
                </div>
            @endif

            {{-- Compatibility warnings --}}
            <div x-show="warnings.length > 0 || hardErrors.length > 0"
                 x-transition
                 class="space-y-2"
                 style="display:none">
                <template x-for="err in hardErrors" :key="err.message">
                    <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-2xl px-5 py-4">
                        <span class="text-red-500 mt-0.5 flex-shrink-0">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        <p class="font-outfit text-xs text-red-700" x-text="err.message"></p>
                    </div>
                </template>
                <template x-for="w in warnings" :key="w.message">
                    <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4">
                        <span class="text-amber-500 mt-0.5 flex-shrink-0">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        <p class="font-outfit text-xs text-amber-700" x-text="w.message"></p>
                    </div>
                </template>
            </div>

            {{-- Options --}}
            @foreach($template->options as $option)
            @php
                $locale      = app()->getLocale();
                $label       = $option->getTranslation('label', $locale);
                $inputType   = $option->input_type;
                $values      = $option->activeValues;
            @endphp
            <div class="bg-white rounded-3xl border border-gray-100 p-8">
                <h3 class="font-alumni text-h6 text-dark mb-4 flex items-center gap-2">
                    {{ $label }}
                    @if($option->is_required)
                        <span class="font-outfit text-xs text-primary">*</span>
                    @endif
                </h3>

                @if($inputType === 'select')
                    {{-- Select dropdown --}}
                    <select x-model="config['{{ $option->key }}']"
                            @change="onConfigChange()"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                   font-outfit text-sm text-dark focus:outline-none
                                   focus:ring-2 focus:ring-primary/40 focus:border-primary transition">
                        @foreach($values as $val)
                        <option value="{{ $val->value_key }}">
                            {{ $val->getTranslation('label', $locale) }}
                            @if($val->price_modifier != 0)
                                ({{ $val->price_modifier > 0 ? '+' : '' }}{{ $val->price_modifier_type === 'percent'
                                    ? number_format(abs($val->price_modifier), 0).'%'
                                    : number_format(abs($val->price_modifier), 4, ',', '.').' €' }})
                            @endif
                        </option>
                        @endforeach
                    </select>

                @elseif($inputType === 'radio')
                    {{-- Radio cards --}}
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($values as $val)
                        <label class="cursor-pointer">
                            <input type="radio"
                                   x-model="config['{{ $option->key }}']"
                                   @change="onConfigChange()"
                                   value="{{ $val->value_key }}"
                                   class="sr-only peer">
                            <div class="border-2 border-gray-200 rounded-xl px-4 py-3
                                        peer-checked:border-primary peer-checked:bg-primary/5
                                        hover:border-gray-300 transition-all">
                                <p class="font-outfit text-sm font-semibold text-dark">
                                    {{ $val->getTranslation('label', $locale) }}
                                </p>
                                @if($val->price_modifier != 0)
                                    <p class="font-outfit text-xs mt-0.5
                                               {{ $val->price_modifier > 0 ? 'text-primary' : 'text-green-600' }}">
                                        {{ $val->price_modifier > 0 ? '+' : '' }}{{ $val->price_modifier_type === 'percent'
                                            ? number_format(abs($val->price_modifier), 0).'%'
                                            : number_format(abs($val->price_modifier), 4, ',', '.').' €' }}
                                    </p>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>

                @elseif($inputType === 'toggle')
                    {{-- Toggle --}}
                    @php $firstVal = $values->first(); $secondVal = $values->skip(1)->first(); @endphp
                    @if($firstVal)
                    <label class="flex items-center gap-3 cursor-pointer w-fit">
                        <div class="relative">
                            <input type="checkbox"
                                   @change="config['{{ $option->key }}'] = $event.target.checked ? '{{ $secondVal?->value_key ?? '' }}' : '{{ $firstVal->value_key }}'; onConfigChange()"
                                   :checked="config['{{ $option->key }}'] === '{{ $secondVal?->value_key ?? '' }}'"
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer
                                        peer-checked:bg-primary transition-colors"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow
                                        peer-checked:translate-x-5 transition-transform"></div>
                        </div>
                        <span class="font-outfit text-sm text-dark" x-text="config['{{ $option->key }}'] === '{{ $secondVal?->value_key }}' ? '{{ $secondVal?->getTranslation('label', $locale) }}' : '{{ $firstVal->getTranslation('label', $locale) }}'">
                            {{ $firstVal->getTranslation('label', $locale) }}
                        </span>
                    </label>
                    @endif

                @elseif($inputType === 'number')
                    <input type="number"
                           x-model="config['{{ $option->key }}']"
                           @change="onConfigChange()"
                           min="1"
                           class="w-32 border border-gray-200 rounded-xl px-4 py-2.5
                                  font-outfit text-sm text-dark focus:outline-none
                                  focus:ring-2 focus:ring-primary/40 focus:border-primary transition">
                @endif
            </div>
            @endforeach

            {{-- Quantity --}}
            <div class="bg-white rounded-3xl border border-gray-100 p-8">
                <h3 class="font-alumni text-h6 text-dark mb-4">{{ __('app.print_quantity_title') }}</h3>

                {{-- Tier quick-picks --}}
                @if($template->quantityTiers->isNotEmpty())
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach($template->quantityTiers as $tier)
                    <button type="button"
                            @click="quantity = {{ $tier->min_quantity }}; onConfigChange()"
                            :class="quantity == {{ $tier->min_quantity }}
                                    ? 'bg-primary text-white border-primary'
                                    : 'bg-white text-gray-600 border-gray-200 hover:border-primary/60'"
                            class="border-2 rounded-xl px-3 py-1.5 font-outfit text-xs font-semibold transition-all">
                        {{ number_format($tier->min_quantity, 0, ',', '.') }} {{ __('app.print_units_abbr') }}
                        @if($tier->discount_percent > 0)
                            <span class="opacity-70">−{{ $tier->discount_percent }}%</span>
                        @endif
                    </button>
                    @endforeach
                </div>
                @endif

                @php $minQty = $template->quantityTiers->min('min_quantity') ?? 1; @endphp
                <div class="flex items-center gap-3">
                    <button type="button"
                            @click="quantity = Math.max({{ $minQty }}, quantity - 1); onConfigChange()"
                            class="w-10 h-10 flex items-center justify-center rounded-xl
                                   border-2 border-gray-200 text-gray-500 hover:border-primary
                                   hover:text-primary transition-all font-bold text-lg">−</button>
                    <input type="number" x-model.number="quantity"
                           @change="quantity = Math.max({{ $minQty }}, quantity); onConfigChange()"
                           min="{{ $minQty }}"
                           class="w-24 text-center border-2 border-gray-200 rounded-xl px-3 py-2
                                  font-outfit text-sm focus:outline-none focus:ring-2
                                  focus:ring-primary/40 focus:border-primary transition">
                    <button type="button"
                            @click="quantity++; onConfigChange()"
                            class="w-10 h-10 flex items-center justify-center rounded-xl
                                   border-2 border-gray-200 text-gray-500 hover:border-primary
                                   hover:text-primary transition-all font-bold text-lg">+</button>
                    <span class="font-outfit text-xs text-gray-400">{{ __('app.print_units') }}</span>
                    @if($minQty > 1)
                        <span class="font-outfit text-xs text-gray-400">{{ __('app.print_minimum', ['min' => number_format($minQty, 0, ',', '.')]) }}</span>
                    @endif
                </div>
            </div>

            {{-- Artwork notes --}}
            <div class="bg-white rounded-3xl border border-gray-100 p-8">
                <h3 class="font-alumni text-h6 text-dark mb-2">{{ __('app.print_artwork_notes') }} <span class="font-outfit text-xs text-gray-400 font-normal">({{ __('app.optional') }})</span></h3>
                <p class="font-outfit text-xs text-gray-400 mb-4">
                    {{ __('app.print_artwork_hint') }}
                </p>
                <textarea x-model="artworkNotes"
                          rows="3"
                          placeholder="{{ __('app.print_artwork_placeholder') }}"
                          class="w-full border border-gray-200 rounded-xl px-4 py-3
                                 font-outfit text-sm text-dark placeholder-gray-300
                                 focus:outline-none focus:ring-2 focus:ring-primary/40
                                 focus:border-primary transition resize-none"></textarea>
            </div>

        </div>

        {{-- ── RIGHT: Price summary + CTA ───────────────────────────────────────── --}}
        <div class="lg:sticky lg:top-24 space-y-4 self-start">

        @auth
            {{-- Price card --}}
            <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">

                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-alumni text-h5 text-dark">{{ __('app.print_price_summary') }}</h2>
                    <div x-show="loading" class="w-4 h-4 border-2 border-primary/30 border-t-primary rounded-full animate-spin" style="display:none"></div>
                </div>

                {{-- Unit price --}}
                <div class="space-y-3 pb-4 border-b border-gray-100">
                    <div class="flex justify-between items-center">
                        <span class="font-outfit text-xs text-gray-400">{{ __('app.print_unit_price_excl_vat') }}</span>
                        <span class="font-outfit text-sm text-dark" x-text="unitPriceFmt + ' €'">
                            {{ $initialPrice->unitPrice > 0 ? number_format($initialPrice->unitPrice, 4, ',', '.') : '—' }} €
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-outfit text-xs text-gray-400">{{ __('app.print_quantity_title') }}</span>
                        <span class="font-outfit text-sm text-dark" x-text="quantity + ' {{ __('app.print_units_abbr') }}'">100 {{ __('app.print_units_abbr') }}</span>
                    </div>
                    <div x-show="tierDiscount > 0"
                         x-transition
                         class="flex justify-between items-center"
                         style="display:none">
                        <span class="font-outfit text-xs text-green-600">{{ __('app.print_volume_discount') }}</span>
                        <span class="font-outfit text-sm font-semibold text-green-600"
                              x-text="'−' + tierDiscount + '%'"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-outfit text-xs text-gray-400">
                            {{ __('app.print_subtotal_excl_vat') }}
                        </span>
                        <span class="font-outfit text-sm text-dark" x-text="subtotalFmt + ' €'">—</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-outfit text-xs text-gray-400">IVA {{ $template->vat_rate }}%</span>
                        <span class="font-outfit text-sm text-dark" x-text="vatFmt + ' €'">—</span>
                    </div>
                </div>

                {{-- Total --}}
                <div class="flex justify-between items-end pt-4">
                    <span class="font-outfit text-sm font-semibold text-dark">Total</span>
                    <div class="text-right">
                        <p class="font-alumni text-h3 text-primary leading-none"
                           x-text="totalFmt + ' €'">—</p>
                        <p class="font-outfit text-xs text-gray-400 mt-0.5">{{ __('app.print_vat_included') }}</p>
                    </div>
                </div>

                {{-- Production days --}}
                <div class="mt-4 flex items-center gap-2 bg-light rounded-xl px-4 py-2.5">
                    <svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="font-outfit text-xs text-gray-600">
                        {{ __('app.print_production_time') }} <strong x-text="productionDays + ' {{ __('app.print_working_days') }}'">{{ $initialPrice->productionDays }} {{ __('app.print_working_days') }}</strong>
                    </span>
                </div>
            </div>

            {{-- Price breakdown --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5"
                 x-show="breakdown.length > 0"
                 x-transition
                 style="display:none">
                <p class="font-outfit text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">
                    {{ __('app.print_price_breakdown') }}
                </p>
                <div class="space-y-1.5">
                    <template x-for="item in breakdown" :key="item.label">
                        <div class="flex justify-between items-center">
                            <span class="font-outfit text-xs text-gray-500" x-text="item.label"></span>
                            <span class="font-outfit text-xs text-dark"
                                  x-text="(item.value >= 0 ? '+' : '') + item.value.toFixed(4).replace('.', ',') + ' €'"></span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Add to cart form --}}
            <form method="POST"
                  action="{{ route('print.add-to-cart', $template->slug) }}"
                  @submit="prepareSubmit($event)">
                @csrf

                {{-- Hidden fields populated by Alpine --}}
                <div id="config-fields"></div>
                <input type="hidden" name="quantity" :value="quantity">
                <input type="hidden" name="artwork_notes" :value="artworkNotes">

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-3">
                        @foreach($errors->all() as $error)
                            <p class="font-outfit text-xs text-red-600">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <button type="submit"
                        :disabled="hardErrors.length > 0 || loading"
                        :class="hardErrors.length > 0 ? 'opacity-50 cursor-not-allowed' : 'hover:brightness-110 active:scale-95'"
                        class="w-full bg-primary text-white font-alumni text-sm-header
                               px-6 py-4 rounded-2xl transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    {{ __('app.print_add_to_cart') }}
                </button>
            </form>

            {{-- Save configuration --}}
            <div x-data="{ open: false, name: '', saving: false, saved: false }" class="mt-2">
                <button type="button"
                        @click="open = !open"
                        class="w-full border-2 border-gray-200 text-gray-500 font-alumni text-sm-header
                               py-3 rounded-2xl hover:border-primary hover:text-primary transition-all
                               flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                    </svg>
                    {{ __('app.print_save_config') }}
                </button>

                <div x-show="open" x-transition class="mt-3 bg-white rounded-2xl border border-gray-200 p-4 space-y-3" style="display:none">
                    <p class="font-outfit text-xs text-gray-400">{{ __('app.print_save_config_hint') }}</p>
                    <input type="text" x-model="name" placeholder="{{ __('app.print_config_placeholder') }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <div class="flex gap-2">
                        <button type="button" @click="open = false"
                                class="flex-1 border border-gray-200 text-gray-400 font-outfit text-xs py-2 rounded-xl hover:bg-gray-50 transition-colors">
                            {{ __('app.cancel') }}
                        </button>
                        <button type="button"
                                :disabled="!name.trim() || saving"
                                @click="
                                    saving = true;
                                    const fields = {};
                                    Object.entries($root.querySelector('[x-data]').__x.$data.config ?? {}).forEach(([k,v]) => fields[k]=v);
                                    fetch('{{ route('print.configs.store') }}', {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                                        body: JSON.stringify({
                                            print_template_id: {{ $template->id }},
                                            name: name,
                                            configuration: $data.config ?? {},
                                            quantity: $data.quantity,
                                            artwork_notes: $data.artworkNotes,
                                        })
                                    })
                                    .then(r => r.json())
                                    .then(d => { if(d.success){ saved = true; open = false; name = ''; } })
                                    .finally(() => saving = false)
                                "
                                class="flex-1 bg-primary text-white font-outfit text-xs py-2 rounded-xl hover:brightness-110 transition-all disabled:opacity-50">
                            <span x-show="!saving">{{ __('app.save') }}</span>
                            <span x-show="saving" class="inline-block w-3 h-3 border-2 border-white/40 border-t-white rounded-full animate-spin"></span>
                        </button>
                    </div>
                    <div x-show="saved" x-transition class="flex items-center gap-2 text-green-600 font-outfit text-xs" style="display:none">
                        {{ __('app.print_config_saved') }} <a href="{{ route('print.configs.index') }}" class="underline">{{ __('app.print_view_all_configs') }}</a>
                    </div>
                </div>
            </div>

        @else
            {{-- Guest: login prompt --}}
            <div class="bg-white rounded-3xl border border-gray-100 p-8 shadow-sm text-center space-y-4">
                <div class="w-14 h-14 bg-primary/10 rounded-full flex items-center justify-center mx-auto">
                    <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-alumni text-h5 text-dark">{{ __('app.print_guest_title') }}</p>
                    <p class="font-outfit text-sm text-gray-400 mt-1">
                        {{ __('app.print_guest_hint') }}
                    </p>
                </div>
                <a href="{{ route('login') }}"
                   class="block w-full bg-primary text-white font-alumni text-sm-header
                          px-6 py-4 rounded-2xl hover:brightness-110 transition-all text-center">
                    {{ __('app.login') }}
                </a>
                <a href="{{ route('register') }}"
                   class="block w-full border-2 border-gray-200 text-gray-600 font-alumni text-sm-header
                          py-3 rounded-2xl hover:border-primary hover:text-primary transition-all text-center">
                    {{ __('app.print_create_free_account') }}
                </a>
            </div>
        @endauth

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function printBuilder() {
    return {
        // State
        config: @json($defaultConfig),
        quantity: {{ max(100, $minQty) }},
        minQuantity: {{ $minQty }},
        artworkNotes: '',
        loading: false,
        isAuth: {{ Auth::check() ? 'true' : 'false' }},

        // Price display
        unitPriceFmt: '{{ number_format($initialPrice->unitPrice, 4, ',', '.') }}',
        subtotalFmt:  '{{ number_format($initialPrice->totalPrice, 2, ',', '.') }}',
        totalFmt:     '{{ number_format($initialPrice->totalPrice * (1 + $template->vat_rate / 100), 2, ',', '.') }}',
        vatFmt:       '{{ number_format($initialPrice->totalPrice * ($template->vat_rate / 100), 2, ',', '.') }}',
        tierDiscount: {{ $initialPrice->tierDiscountPercent }},
        productionDays: {{ $initialPrice->productionDays }},
        breakdown:    @json($initialPrice->breakdown ?? []),

        // Compatibility
        compatibilityRules: @json($template->compatibility_rules_for_frontend),
        warnings: [],
        hardErrors: [],

        // Debounce timer
        _timer: null,

        init() {
            this.checkCompatibility();
        },

        onConfigChange() {
            this.checkCompatibility();
            if (!this.isAuth) return; // guests don't see prices, skip the API roundtrip
            clearTimeout(this._timer);
            this._timer = setTimeout(() => this.fetchPrice(), 350);
        },

        checkCompatibility() {
            this.warnings   = [];
            this.hardErrors = [];

            const locale = '{{ app()->getLocale() }}';

            for (const rule of this.compatibilityRules) {
                if (this.config[rule.condition_option_key] !== rule.condition_value_key) continue;

                const targetVal = this.config[rule.target_option_key];
                if (rule.target_value_key && targetVal !== rule.target_value_key) continue;

                const msg = rule.message?.[locale] ?? rule.message?.ca ?? rule.message?.es ?? '';
                if (!msg) continue;

                if (rule.rule_type === 'incompatible') {
                    this.hardErrors.push({ message: msg });
                } else if (rule.rule_type === 'warning') {
                    this.warnings.push({ message: msg });
                }
            }
        },

        async fetchPrice() {
            this.loading = true;
            try {
                const res = await fetch('{{ route('print.calculate', $template->slug) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ config: this.config, quantity: this.quantity }),
                });
                const data = await res.json();
                this.unitPriceFmt   = data.unit_price_fmt;
                this.subtotalFmt    = data.total_price_fmt;
                this.totalFmt       = data.total_with_vat_fmt;
                this.vatFmt         = data.vat_amount_fmt;
                this.tierDiscount   = data.tier_discount_percent;
                this.productionDays = data.production_days;
                this.breakdown      = data.breakdown ?? [];
            } catch(e) {
                console.error('Price fetch failed', e);
            } finally {
                this.loading = false;
            }
        },

        prepareSubmit(event) {
            if (this.hardErrors.length > 0) {
                event.preventDefault();
                return;
            }
            // Inject config as hidden fields
            const container = document.getElementById('config-fields');
            container.innerHTML = '';
            for (const [key, value] of Object.entries(this.config)) {
                const input = document.createElement('input');
                input.type  = 'hidden';
                input.name  = `config[${key}]`;
                input.value = value;
                container.appendChild(input);
            }
        },
    };
}
</script>
@endpush
