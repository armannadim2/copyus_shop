@extends('layouts.admin')
@section('title', $quoteRequest->reference)

@section('content')
<div class="p-8 max-w-5xl space-y-6">

    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('admin.quote-requests.index') }}"
           class="font-outfit text-xs text-gray-400 hover:text-primary transition-colors">
            ← Sol·licituds de pressupost
        </a>
        <span class="text-gray-300">/</span>
        <span class="font-outfit text-xs text-dark">{{ $quoteRequest->reference }}</span>
    </div>

    @if(session('success'))
        <div class="px-5 py-4 bg-green-50 border border-green-200 rounded-2xl font-outfit text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-start justify-between flex-wrap gap-4">
        <div>
            <div class="flex items-center gap-2 flex-wrap mb-2">
                <span class="font-outfit text-xs text-gray-400">{{ $quoteRequest->reference }}</span>
                <span class="font-outfit text-xs px-2.5 py-1 rounded-full {{ $quoteRequest->status_color }}">
                    {{ $quoteRequest->status_label }}
                </span>
            </div>
            <h1 class="font-alumni text-h3 text-dark">{{ $quoteRequest->service_type }}</h1>
            <p class="font-outfit text-xs text-gray-400 mt-1">
                Rebuda el {{ $quoteRequest->created_at->format('d/m/Y \a\l\e\s H:i') }}
            </p>
        </div>
    </div>

    <div class="grid lg:grid-cols-[1fr_320px] gap-6">

        {{-- LEFT: details --}}
        <div class="space-y-4">

            {{-- Description --}}
            <div class="bg-white rounded-2xl border border-gray-100 px-6 py-5">
                <p class="font-outfit text-xs text-gray-400 mb-2">Descripció del projecte</p>
                <div class="font-outfit text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $quoteRequest->description }}</div>
            </div>

            {{-- Project meta --}}
            <div class="bg-white rounded-2xl border border-gray-100 px-6 py-5">
                <p class="font-outfit text-xs text-gray-400 mb-3">Detalls</p>
                <dl class="grid sm:grid-cols-3 gap-4 font-outfit text-sm">
                    <div>
                        <dt class="text-xs text-gray-400">Servei</dt>
                        <dd class="text-dark mt-0.5">{{ $quoteRequest->service_type }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400">Quantitat</dt>
                        <dd class="text-dark mt-0.5">
                            {{ $quoteRequest->quantity ? number_format($quoteRequest->quantity, 0, ',', '.') : '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400">Termini</dt>
                        <dd class="text-dark mt-0.5">{{ $quoteRequest->deadline ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400">Pressupost</dt>
                        <dd class="text-dark mt-0.5">{{ $quoteRequest->budget_range ?: '—' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-gray-400">Adjunt</dt>
                        <dd class="mt-0.5">
                            @if($quoteRequest->attachment_path)
                                <a href="{{ asset('storage/' . $quoteRequest->attachment_path) }}"
                                   target="_blank"
                                   class="text-primary hover:underline">
                                    Descarregar fitxer adjunt ↗
                                </a>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Admin form --}}
            <form method="POST" action="{{ route('admin.quote-requests.update', $quoteRequest) }}"
                  class="bg-white rounded-2xl border border-gray-100 px-6 py-5 space-y-4">
                @csrf @method('PATCH')
                <p class="font-outfit text-xs text-gray-400">Gestió interna</p>

                <div>
                    <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">Estat</label>
                    <select name="status"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                   font-outfit text-sm focus:outline-none
                                   focus:ring-2 focus:ring-primary/40 focus:border-primary
                                   transition bg-white">
                        @foreach(['new' => 'Nova', 'in_review' => 'En revisió', 'quoted' => 'Pressupostada', 'closed' => 'Tancada'] as $key => $label)
                            <option value="{{ $key }}" @selected($quoteRequest->status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">Notes internes</label>
                    <textarea name="admin_notes" rows="4" maxlength="5000"
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                     font-outfit text-sm focus:outline-none
                                     focus:ring-2 focus:ring-primary/40 focus:border-primary
                                     transition">{{ old('admin_notes', $quoteRequest->admin_notes) }}</textarea>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="bg-primary text-white font-alumni text-sm-header px-6 py-2.5
                                   rounded-xl hover:brightness-110 transition-all">
                        Desar canvis
                    </button>
                </div>
            </form>

            {{-- Delete --}}
            <form method="POST" action="{{ route('admin.quote-requests.destroy', $quoteRequest) }}"
                  onsubmit="return confirm('Eliminar aquesta sol·licitud? Aquesta acció no es pot desfer.')">
                @csrf @method('DELETE')
                <button type="submit" class="font-outfit text-xs text-red-500 hover:underline">
                    Eliminar sol·licitud
                </button>
            </form>
        </div>

        {{-- RIGHT: contact aside --}}
        <aside class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 px-6 py-5">
                <p class="font-outfit text-xs text-gray-400 mb-3">Sol·licitant</p>
                <div class="space-y-3 font-outfit text-sm">
                    <div>
                        <p class="text-xs text-gray-400">Nom</p>
                        <p class="text-dark">{{ $quoteRequest->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Correu</p>
                        <a href="mailto:{{ $quoteRequest->email }}"
                           class="text-primary hover:underline break-all">
                            {{ $quoteRequest->email }}
                        </a>
                    </div>
                    @if($quoteRequest->phone)
                        <div>
                            <p class="text-xs text-gray-400">Telèfon</p>
                            <a href="tel:{{ $quoteRequest->phone }}"
                               class="text-primary hover:underline">
                                {{ $quoteRequest->phone }}
                            </a>
                        </div>
                    @endif
                    @if($quoteRequest->company_name)
                        <div>
                            <p class="text-xs text-gray-400">Empresa</p>
                            <p class="text-dark">{{ $quoteRequest->company_name }}</p>
                        </div>
                    @endif
                    @if($quoteRequest->cif)
                        <div>
                            <p class="text-xs text-gray-400">CIF/NIF</p>
                            <p class="text-dark">{{ $quoteRequest->cif }}</p>
                        </div>
                    @endif
                    @if($quoteRequest->user)
                        <div class="pt-3 border-t border-gray-100">
                            <p class="text-xs text-gray-400">Usuari registrat</p>
                            <a href="{{ route('admin.users.show', $quoteRequest->user_id) }}"
                               class="text-primary hover:underline">
                                Veure perfil →
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
