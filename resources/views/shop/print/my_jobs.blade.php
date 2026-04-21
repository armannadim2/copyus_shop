@extends('layouts.app')
@section('title', 'Els meus treballs d\'impressió')

@section('content')

{{-- Hero --}}
<div class="text-center pt-16 pb-12 px-4">
    <h1 class="font-alumni text-h1 text-dark leading-tight mb-4">
        Els meus treballs
        <span class="text-secondary">d'impressió</span>
    </h1>
    <p class="font-alumni text-h5 text-primary max-w-xl mx-auto">
        Historial de tots els encàrrecs d'impressió enviats
    </p>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 pb-20">

    {{-- Status filter tabs --}}
    @php
        $statuses = ['ordered', 'in_production', 'completed', 'cancelled'];
        $statusLabels = [
            'ordered'       => 'Rebuts',
            'in_production' => 'En producció',
            'completed'     => 'Completats',
            'cancelled'     => 'Cancel·lats',
        ];
        $statusColors = [
            'ordered'       => 'bg-yellow-50 text-yellow-700 border-yellow-200',
            'in_production' => 'bg-orange-50 text-orange-700 border-orange-200',
            'completed'     => 'bg-green-50 text-green-700 border-green-200',
            'cancelled'     => 'bg-red-50 text-red-600 border-red-200',
        ];
        $badgeColors = [
            'ordered'       => 'bg-yellow-50 text-yellow-700',
            'in_production' => 'bg-orange-50 text-orange-700',
            'completed'     => 'bg-green-50 text-green-700',
            'cancelled'     => 'bg-red-50 text-red-600',
        ];
        $totalAll = $counts->sum();
        $currentStatus = request('status');
    @endphp

    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('print.my-jobs') }}"
           class="font-outfit text-xs px-4 py-2 rounded-full border transition-colors
                  {{ !$currentStatus ? 'bg-primary text-white border-primary' : 'bg-white text-gray-500 border-gray-200 hover:border-primary hover:text-primary' }}">
            Tots <span class="ml-1 opacity-70">({{ $totalAll }})</span>
        </a>
        @foreach($statuses as $s)
            @if(($counts[$s] ?? 0) > 0 || $currentStatus === $s)
                <a href="{{ route('print.my-jobs', ['status' => $s]) }}"
                   class="font-outfit text-xs px-4 py-2 rounded-full border transition-colors
                          {{ $currentStatus === $s ? 'bg-primary text-white border-primary' : 'bg-white text-gray-500 border-gray-200 hover:border-primary hover:text-primary' }}">
                    {{ $statusLabels[$s] }}
                    <span class="ml-1 opacity-70">({{ $counts[$s] ?? 0 }})</span>
                </a>
            @endif
        @endforeach
    </div>

    @if($jobs->isEmpty())
        <div class="bg-white rounded-3xl shadow-sm p-16 text-center">
            <div class="w-16 h-16 mx-auto bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
            </div>
            <h2 class="font-alumni text-h4 text-dark mb-3">Cap treball trobat</h2>
            <p class="font-outfit text-sm text-gray-400 mb-6">
                @if($currentStatus)
                    No tens treballs amb l'estat "{{ $statusLabels[$currentStatus] ?? $currentStatus }}".
                @else
                    Encara no has fet cap encàrrec d'impressió.
                @endif
            </p>
            <a href="{{ route('print.index') }}"
               class="inline-flex items-center gap-2 bg-primary text-white font-alumni text-sm-header
                      px-8 py-3 rounded-2xl hover:brightness-110 active:scale-95 transition-all">
                Explorar serveis d'impressió
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($jobs as $job)
                @php
                    $color = $badgeColors[$job->status] ?? 'bg-gray-50 text-gray-600';
                    $locale = app()->getLocale();
                @endphp
                <div class="bg-white rounded-3xl shadow-sm overflow-hidden
                            {{ $job->status === 'in_production' ? 'ring-2 ring-orange-200' : '' }}">
                    <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center gap-4">

                        {{-- Icon + info --}}
                        <div class="flex items-start gap-4 flex-1 min-w-0">
                            <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <p class="font-alumni text-sm-header text-dark">
                                        {{ $job->template->getTranslation('name', $locale) }}
                                    </p>
                                    <span class="inline-block font-outfit text-xs px-2.5 py-0.5 rounded-full {{ $color }}">
                                        @php
                                            $statusText = [
                                                'ordered'       => 'Rebut',
                                                'in_production' => 'En producció',
                                                'completed'     => 'Completat',
                                                'cancelled'     => 'Cancel·lat',
                                            ];
                                        @endphp
                                        {{ $statusText[$job->status] ?? $job->status }}
                                    </span>
                                    @if(in_array($job->status, ['ordered', 'in_production']) && !$job->artwork_path)
                                        <span class="inline-flex items-center gap-1 font-outfit text-xs px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700">
                                            ⚠ Sense arxiu
                                        </span>
                                    @endif
                                </div>
                                <p class="font-outfit text-xs text-gray-400">
                                    Treball #{{ $job->id }} · {{ $job->created_at->format('d/m/Y') }}
                                    · {{ number_format($job->quantity, 0, ',', '.') }} unitats
                                    · {{ number_format($job->total_price, 2, ',', '.') }} €
                                </p>
                                @if($job->configuration)
                                    <div class="flex flex-wrap gap-1.5 mt-2">
                                        @foreach($job->configuration as $key => $val)
                                            <span class="font-outfit text-xs bg-light text-dark/60 px-2 py-0.5 rounded-full">
                                                {{ $val }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                                @if($job->expected_delivery_at)
                                    <p class="font-outfit text-xs text-primary mt-2">
                                        Entrega prevista: <strong>{{ $job->expected_delivery_at->format('d/m/Y') }}</strong>
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-wrap items-center gap-3 shrink-0 self-start sm:self-center">

                            {{-- Cancel (only while ordered, not in production yet) --}}
                            @if($job->status === 'ordered')
                                <div x-data="{ openCancel: false }">
                                    <button @click="openCancel = true"
                                            class="font-outfit text-xs text-red-400 hover:text-red-600 transition-colors">
                                        Cancel·lar
                                    </button>
                                    <div x-show="openCancel" x-cloak
                                         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
                                         @keydown.escape.window="openCancel = false">
                                        <div class="bg-white rounded-3xl shadow-xl max-w-sm w-full mx-4 p-8" @click.stop>
                                            <h3 class="font-alumni text-h5 text-dark mb-2">Cancel·lar treball #{{ $job->id }}?</h3>
                                            <p class="font-outfit text-sm text-gray-500 mb-6">Aquesta acció no es pot desfer.</p>
                                            <div class="flex gap-3 justify-end">
                                                <button @click="openCancel = false"
                                                        class="font-alumni text-sm-header border border-gray-200 text-gray-600
                                                               px-5 py-2.5 rounded-2xl hover:bg-gray-50 transition-colors">
                                                    Tornar
                                                </button>
                                                <form method="POST" action="{{ route('print.jobs.cancel', $job->id) }}">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                            class="font-alumni text-sm-header bg-red-500 text-white
                                                                   px-5 py-2.5 rounded-2xl hover:brightness-110 transition-all">
                                                        Sí, cancel·lar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Confirm received (completed, not yet confirmed) --}}
                            @if($job->status === 'completed' && !$job->received_at)
                                <form method="POST" action="{{ route('print.jobs.received', $job->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="font-outfit text-xs bg-green-50 border border-green-200
                                                   text-green-700 px-3 py-1.5 rounded-lg hover:bg-green-100 transition-colors">
                                        ✓ Confirmar recepció
                                    </button>
                                </form>
                            @elseif($job->status === 'completed' && $job->received_at)
                                <span class="font-outfit text-xs text-green-600">
                                    ✓ Rebut {{ $job->received_at->format('d/m/Y') }}
                                </span>
                            @endif

                            {{-- View orders --}}
                            <a href="{{ route('orders.index') }}"
                               class="font-outfit text-xs text-secondary hover:text-primary transition-colors">
                                Comanda →
                            </a>

                            {{-- Reorder (completed / cancelled only) --}}
                            @if(in_array($job->status, ['completed', 'cancelled']) && $job->template->is_active)
                                <form method="POST" action="{{ route('print.jobs.reorder', $job->id) }}">
                                    @csrf
                                    <button type="submit"
                                            class="font-outfit text-xs text-gray-400 hover:text-primary transition-colors"
                                            title="Repetir encàrrec">
                                        ↺ Repetir
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- Production progress bar for in_production --}}
                    @if($job->status === 'in_production')
                        <div class="h-1 bg-orange-100">
                            <div class="h-1 bg-orange-400 w-1/2 transition-all duration-500"></div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($jobs->hasPages())
            <div class="mt-6">{{ $jobs->links() }}</div>
        @endif
    @endif

    {{-- Footer links --}}
    <div class="mt-10 flex flex-wrap gap-4 justify-center">
        <a href="{{ route('print.index') }}"
           class="font-outfit text-sm text-secondary hover:text-primary transition-colors">
            ← Explorar plantilles d'impressió
        </a>
        <a href="{{ route('orders.index') }}"
           class="font-outfit text-sm text-gray-400 hover:text-primary transition-colors">
            Veure totes les comandes
        </a>
    </div>
</div>

@endsection
