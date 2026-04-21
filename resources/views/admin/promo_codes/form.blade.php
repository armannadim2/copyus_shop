@extends('layouts.admin')
@section('title', $code ? 'Editar codi' : 'Nou codi de descompte')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.promo-codes.index') }}"
           class="font-outfit text-sm text-gray-500 hover:text-primary transition-colors">← Codis</a>
        <h1 class="font-alumni text-h1 text-dark">
            {{ $code ? 'Editar codi' : 'Nou codi de descompte' }}
        </h1>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-600 font-outfit text-sm px-4 py-3 rounded-2xl">
            <ul class="space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST"
          action="{{ $code ? route('admin.promo-codes.update', $code->id) : route('admin.promo-codes.store') }}"
          class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
        @csrf
        @if($code) @method('PUT') @endif

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1">Codi *</label>
                <input type="text" name="code" required maxlength="50"
                       value="{{ old('code', $code?->code) }}"
                       placeholder="BENVINGUT10"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm uppercase
                              focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1">Descripció</label>
                <input type="text" name="description" maxlength="255"
                       value="{{ old('description', $code?->description) }}"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                              focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1">Tipus *</label>
                <select name="type" required
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                               focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="percent" {{ old('type', $code?->type) === 'percent' ? 'selected' : '' }}>Percentatge (%)</option>
                    <option value="fixed"   {{ old('type', $code?->type) === 'fixed'   ? 'selected' : '' }}>Import fix (€)</option>
                </select>
            </div>
            <div>
                <label class="block font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1">Valor *</label>
                <input type="number" name="value" required step="0.01" min="0"
                       value="{{ old('value', $code?->value) }}"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                              focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1">Comanda mínima (€)</label>
                <input type="number" name="min_order_total" step="0.01" min="0"
                       value="{{ old('min_order_total', $code?->min_order_total) }}"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                              focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1">Màx. usos totals</label>
                <input type="number" name="max_uses" min="1"
                       value="{{ old('max_uses', $code?->max_uses) }}"
                       placeholder="Il·limitat"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                              focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1">Màx. usos per usuari</label>
                <input type="number" name="max_uses_per_user" min="1"
                       value="{{ old('max_uses_per_user', $code?->max_uses_per_user) }}"
                       placeholder="Il·limitat"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                              focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1">Vàlid des de</label>
                <input type="date" name="valid_from"
                       value="{{ old('valid_from', $code?->valid_from?->format('Y-m-d')) }}"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                              focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1">Vàlid fins a</label>
                <input type="date" name="valid_until"
                       value="{{ old('valid_until', $code?->valid_until?->format('Y-m-d')) }}"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 font-outfit text-sm
                              focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div class="flex items-end pb-1">
                <label class="flex items-center gap-2 cursor-pointer font-outfit text-sm text-gray-600">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $code?->is_active ?? true) ? 'checked' : '' }}
                           class="rounded accent-primary">
                    Actiu
                </label>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-primary text-white font-alumni text-sm-header px-8 py-3
                           rounded-2xl hover:brightness-110 transition-all">
                {{ $code ? 'Guardar canvis' : 'Crear codi' }}
            </button>
            <a href="{{ route('admin.promo-codes.index') }}"
               class="font-outfit text-sm text-gray-500 px-6 py-3 rounded-2xl
                      border border-gray-200 hover:bg-gray-50 transition-colors">
                Cancel·lar
            </a>
        </div>
    </form>
</div>
@endsection
