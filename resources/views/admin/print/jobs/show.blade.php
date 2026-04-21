@extends('layouts.admin')
@section('title', 'Treball #' . $job->id)

@section('content')
<div class="p-8 max-w-5xl space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('admin.print.jobs.index') }}"
           class="font-outfit text-xs text-gray-400 hover:text-primary transition-colors">
            ← Treballs d'impressió
        </a>
        <span class="text-gray-300">/</span>
        <span class="font-outfit text-xs text-dark">Treball #{{ $job->id }}</span>
    </div>

    @if(session('success'))
        <div class="px-5 py-4 bg-green-50 border border-green-200 rounded-2xl font-outfit text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="px-5 py-4 bg-red-50 border border-red-200 rounded-2xl font-outfit text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Header row --}}
    @php
        $statusBadge = match($job->status) {
            'ordered'       => 'bg-yellow-50 text-yellow-700 border-yellow-200',
            'in_production' => 'bg-orange-50 text-orange-700 border-orange-200',
            'completed'     => 'bg-green-50 text-green-700 border-green-200',
            'cancelled'     => 'bg-red-50 text-red-600 border-red-200',
            default         => 'bg-gray-100 text-gray-500 border-gray-200',
        };
        $statusLabel = match($job->status) {
            'ordered'       => 'Encarregat',
            'in_production' => 'En producció',
            'completed'     => 'Completat',
            'cancelled'     => 'Cancel·lat',
            default         => $job->status,
        };
    @endphp
    <div class="flex items-start justify-between flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <span class="text-4xl">{{ $job->template->icon ?? '🖨️' }}</span>
            <div>
                <h1 class="font-alumni text-h3 text-dark">
                    {{ $job->template->getTranslation('name', 'ca') }}
                </h1>
                <p class="font-outfit text-xs text-gray-400">
                    Treball #{{ $job->id }} · {{ $job->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
        </div>
        <span class="font-outfit text-sm font-semibold px-4 py-2 rounded-xl border {{ $statusBadge }}">
            {{ $statusLabel }}
        </span>
    </div>

    <div class="grid lg:grid-cols-[1fr_340px] gap-6">

        {{-- ── LEFT COLUMN ─────────────────────────────────────────────────────── --}}
        <div class="space-y-6">

            {{-- Client --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="font-alumni text-h5 text-dark mb-4">Client</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="font-outfit text-xs text-gray-400">Nom</p>
                        <p class="font-outfit text-sm text-dark">{{ $job->user?->name }}</p>
                    </div>
                    <div>
                        <p class="font-outfit text-xs text-gray-400">Empresa</p>
                        <p class="font-outfit text-sm text-dark">{{ $job->user?->company_name }}</p>
                    </div>
                    <div>
                        <p class="font-outfit text-xs text-gray-400">Email</p>
                        <p class="font-outfit text-sm text-dark">{{ $job->user?->email }}</p>
                    </div>
                    <div>
                        <p class="font-outfit text-xs text-gray-400">Telèfon</p>
                        <p class="font-outfit text-sm text-dark">{{ $job->user?->phone ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Configuration --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="font-alumni text-h5 text-dark mb-4">Configuració</h2>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($job->configuration_labels as $label => $value)
                        <div class="bg-light rounded-xl px-4 py-3">
                            <p class="font-outfit text-xs text-gray-400">{{ $label }}</p>
                            <p class="font-outfit text-sm font-semibold text-dark">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>
                @if($job->artwork_notes)
                    <div class="mt-4 bg-amber-50 border border-amber-100 rounded-xl px-4 py-3">
                        <p class="font-outfit text-xs text-amber-600 font-semibold mb-1">Notes del client</p>
                        <p class="font-outfit text-sm text-amber-800">{{ $job->artwork_notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Artwork --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="font-alumni text-h5 text-dark mb-4">Arxiu de disseny</h2>
                @if($job->artwork_path)
                    <div class="flex items-center gap-4 bg-green-50 border border-green-100 rounded-xl px-4 py-3 mb-4">
                        <svg class="w-8 h-8 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="font-outfit text-sm font-semibold text-green-800 truncate">
                                {{ basename($job->artwork_path) }}
                            </p>
                            <p class="font-outfit text-xs text-green-600">Arxiu carregat</p>
                        </div>
                        <a href="{{ Storage::disk('public')->url($job->artwork_path) }}"
                           target="_blank"
                           class="font-outfit text-xs text-green-700 border border-green-300
                                  px-3 py-1.5 rounded-xl hover:bg-green-100 transition-colors whitespace-nowrap">
                            Descarregar
                        </a>
                    </div>
                @else
                    <div class="bg-gray-50 border border-dashed border-gray-200 rounded-xl px-4 py-6 text-center mb-4">
                        <p class="font-outfit text-sm text-gray-400">Encara no hi ha arxiu de disseny.</p>
                    </div>
                @endif

                {{-- Admin upload --}}
                <form method="POST" action="{{ route('admin.print.jobs.artwork', $job) }}"
                      enctype="multipart/form-data" class="flex gap-3 items-end">
                    @csrf
                    <div class="flex-1">
                        <label class="block font-outfit text-xs text-gray-400 mb-1">
                            {{ $job->artwork_path ? 'Substituir arxiu' : 'Carregar arxiu' }}
                        </label>
                        <input type="file" name="artwork"
                               accept=".pdf,.ai,.eps,.svg,.png,.jpg,.jpeg,.tiff,.psd"
                               class="w-full text-sm font-outfit text-gray-600
                                      file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0
                                      file:text-xs file:font-semibold file:bg-primary/10 file:text-primary
                                      hover:file:bg-primary/20 transition">
                    </div>
                    <button type="submit"
                            class="bg-primary text-white font-outfit text-xs px-4 py-2.5 rounded-xl
                                   hover:brightness-110 transition-all whitespace-nowrap">
                        Carregar
                    </button>
                </form>
                @error('artwork')
                    <p class="font-outfit text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Production log --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="font-alumni text-h5 text-dark mb-4">Registre de producció</h2>
                @if($job->productionLog->isEmpty())
                    <p class="font-outfit text-sm text-gray-400">Cap registre encara.</p>
                @else
                    <ol class="relative border-l-2 border-gray-100 ml-3 space-y-4">
                        @foreach($job->productionLog as $log)
                        <li class="ml-5">
                            <span class="absolute -left-[9px] w-4 h-4 rounded-full border-2 border-white
                                         {{ match($log->event) {
                                             'status_change'    => 'bg-primary',
                                             'artwork_uploaded' => 'bg-green-500',
                                             default            => 'bg-gray-300',
                                         } }}"></span>
                            <div>
                                <p class="font-outfit text-xs text-gray-400">
                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                    @if($log->admin)
                                        · <span class="text-dark">{{ $log->admin->name }}</span>
                                    @else
                                        · Sistema
                                    @endif
                                </p>
                                @if($log->event === 'status_change')
                                    <p class="font-outfit text-sm text-dark mt-0.5">
                                        Canvi d'estat:
                                        <span class="font-semibold">{{ $log->previous_status }}</span>
                                        →
                                        <span class="font-semibold text-primary">{{ $log->new_status }}</span>
                                    </p>
                                @elseif($log->event === 'artwork_uploaded')
                                    <p class="font-outfit text-sm text-dark mt-0.5">Arxiu de disseny carregat</p>
                                @else
                                    <p class="font-outfit text-sm text-dark mt-0.5">{{ $log->event }}</p>
                                @endif
                                @if($log->note)
                                    <p class="font-outfit text-xs text-gray-500 mt-0.5 italic">{{ $log->note }}</p>
                                @endif
                            </div>
                        </li>
                        @endforeach
                    </ol>
                @endif
            </div>

        </div>

        {{-- ── RIGHT COLUMN ────────────────────────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Pricing --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="font-alumni text-h5 text-dark mb-4">Preu</h2>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="font-outfit text-xs text-gray-400">Quantitat</span>
                        <span class="font-outfit text-sm text-dark">{{ number_format($job->quantity, 0, ',', '.') }} ut.</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-outfit text-xs text-gray-400">Preu unitari (s/ IVA)</span>
                        <span class="font-outfit text-sm text-dark">{{ number_format($job->unit_price, 4, ',', '.') }} €</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-outfit text-xs text-gray-400">Subtotal</span>
                        <span class="font-outfit text-sm text-dark">{{ number_format($job->total_price, 2, ',', '.') }} €</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-outfit text-xs text-gray-400">IVA {{ $job->template->vat_rate }}%</span>
                        <span class="font-outfit text-sm text-dark">
                            {{ number_format($job->total_price * ($job->template->vat_rate / 100), 2, ',', '.') }} €
                        </span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-gray-100">
                        <span class="font-alumni text-h6 text-dark">Total</span>
                        <span class="font-alumni text-h4 text-primary">
                            {{ number_format($job->total_price * (1 + $job->template->vat_rate / 100), 2, ',', '.') }} €
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-outfit text-xs text-gray-400">Dies producció</span>
                        <span class="font-outfit text-sm text-dark">{{ $job->production_days }}d</span>
                    </div>
                </div>
            </div>

            {{-- Status update --}}
            @if(!empty($allowedTransitions))
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="font-alumni text-h5 text-dark mb-4">Canviar estat</h2>
                <form method="POST" action="{{ route('admin.print.jobs.status', $job) }}" class="space-y-3">
                    @csrf @method('PATCH')
                    <div>
                        <label class="block font-outfit text-xs text-gray-400 mb-1">Nou estat</label>
                        <select name="status" required
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                       font-outfit text-sm focus:outline-none focus:ring-2
                                       focus:ring-primary/40 focus:border-primary transition">
                            @foreach($allowedTransitions as $transition)
                                <option value="{{ $transition }}">
                                    {{ match($transition) {
                                        'in_production' => 'En producció',
                                        'completed'     => 'Completat',
                                        'cancelled'     => 'Cancel·lat',
                                        default         => $transition,
                                    } }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-outfit text-xs text-gray-400 mb-1">Nota interna (opcional)</label>
                        <textarea name="note" rows="2"
                                  placeholder="Observació per al registre..."
                                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                         font-outfit text-sm focus:outline-none focus:ring-2
                                         focus:ring-primary/40 resize-none transition"></textarea>
                    </div>
                    <button type="submit"
                            class="w-full bg-primary text-white font-alumni text-sm-header
                                   py-3 rounded-2xl hover:brightness-110 transition-all">
                        Actualitzar estat
                    </button>
                </form>
            </div>
            @endif

            {{-- Delivery date + admin notes --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="font-alumni text-h5 text-dark mb-4">Lliurament i notes</h2>
                <form method="POST" action="{{ route('admin.print.jobs.delivery', $job) }}" class="space-y-3">
                    @csrf @method('PATCH')
                    <div>
                        <label class="block font-outfit text-xs text-gray-400 mb-1">Data estimada de lliurament</label>
                        <input type="date" name="expected_delivery_at"
                               value="{{ $job->expected_delivery_at?->format('Y-m-d') }}"
                               min="{{ now()->toDateString() }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                      font-outfit text-sm focus:outline-none focus:ring-2
                                      focus:ring-primary/40 focus:border-primary transition">
                    </div>
                    <div>
                        <label class="block font-outfit text-xs text-gray-400 mb-1">Notes internes</label>
                        <textarea name="admin_notes" rows="3"
                                  placeholder="Notes visibles només per l'admin..."
                                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                         font-outfit text-sm focus:outline-none focus:ring-2
                                         focus:ring-primary/40 resize-none transition">{{ $job->admin_notes }}</textarea>
                    </div>
                    <button type="submit"
                            class="w-full border-2 border-primary text-primary font-alumni text-sm-header
                                   py-3 rounded-2xl hover:bg-primary hover:text-white transition-all">
                        Guardar
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
