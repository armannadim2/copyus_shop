@extends('layouts.app')
@section('title', 'Nou tiquet de suport')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 pt-16 pb-20">

    <div class="mb-8">
        <a href="{{ route('tickets.index') }}"
           class="font-outfit text-xs text-gray-400 hover:text-primary transition-colors mb-4 inline-flex items-center gap-1">
            ← Tiquets de suport
        </a>
        <h1 class="font-alumni text-h2 text-dark mt-2">Nou tiquet</h1>
        <p class="font-outfit text-sm text-gray-400 mt-1">
            Descriu el teu problema i et respondrem el més aviat possible.
        </p>
    </div>

    <div class="bg-white rounded-3xl shadow-sm p-8">
        <form method="POST" action="{{ route('tickets.store') }}" class="space-y-6">
            @csrf

            {{-- Subject --}}
            <div>
                <label class="block font-outfit text-sm font-semibold text-dark mb-2">
                    Assumpte <span class="text-red-400">*</span>
                </label>
                <input type="text" name="subject" value="{{ old('subject') }}" required
                       placeholder="Descriu breument el problema…"
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 font-outfit text-sm
                              focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition
                              @error('subject') border-red-300 @enderror">
                @error('subject')
                    <p class="font-outfit text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Body --}}
            <div>
                <label class="block font-outfit text-sm font-semibold text-dark mb-2">
                    Descripció <span class="text-red-400">*</span>
                </label>
                <textarea name="body" rows="6" required
                          placeholder="Explica en detall el teu problema o pregunta…"
                          class="w-full border border-gray-200 rounded-2xl px-4 py-3 font-outfit text-sm
                                 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary resize-none transition
                                 @error('body') border-red-300 @enderror">{{ old('body') }}</textarea>
                @error('body')
                    <p class="font-outfit text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
                <p class="font-outfit text-xs text-gray-400 mt-1 text-right">
                    Màxim 5.000 caràcters
                </p>
            </div>

            {{-- Related order (optional) --}}
            @if($orders->isNotEmpty())
            <div>
                <label class="block font-outfit text-sm font-semibold text-dark mb-2">
                    Comanda relacionada
                    <span class="font-outfit text-xs font-normal text-gray-400">(opcional)</span>
                </label>
                <select name="order_id"
                        class="w-full border border-gray-200 rounded-2xl px-4 py-3 font-outfit text-sm
                               focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition">
                    <option value="">— Cap comanda —</option>
                    @foreach($orders as $order)
                        <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>
                            #{{ $order->order_number }}
                            · {{ $order->created_at->format('d/m/Y') }}
                            · {{ number_format($order->total, 2, ',', '.') }} €
                        </option>
                    @endforeach
                </select>
                @error('order_id')
                    <p class="font-outfit text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endif

            {{-- Related print job (optional) --}}
            @if($printJobs->isNotEmpty())
            <div>
                <label class="block font-outfit text-sm font-semibold text-dark mb-2">
                    Treball d'impressió relacionat
                    <span class="font-outfit text-xs font-normal text-gray-400">(opcional)</span>
                </label>
                <select name="print_job_id"
                        class="w-full border border-gray-200 rounded-2xl px-4 py-3 font-outfit text-sm
                               focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition">
                    <option value="">— Cap treball —</option>
                    @foreach($printJobs as $job)
                        <option value="{{ $job->id }}" {{ old('print_job_id') == $job->id ? 'selected' : '' }}>
                            #{{ $job->id }}
                            · {{ $job->template?->getTranslation('name', 'ca') }}
                            · {{ $job->created_at->format('d/m/Y') }}
                        </option>
                    @endforeach
                </select>
                @error('print_job_id')
                    <p class="font-outfit text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endif

            {{-- Info box --}}
            <div class="bg-blue-50 border border-blue-100 rounded-2xl px-5 py-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="font-outfit text-xs text-blue-700">
                    Et respondrem en un termini màxim de 24 hores en dies laborables.
                    Rebràs una notificació per correu quan tinguem una resposta.
                </p>
            </div>

            {{-- Submit --}}
            <div class="flex gap-3 pt-2">
                <a href="{{ route('tickets.index') }}"
                   class="flex-1 text-center font-alumni text-sm-header border-2 border-gray-200 text-gray-600
                          py-3.5 rounded-2xl hover:bg-gray-50 transition-colors">
                    Cancel·lar
                </a>
                <button type="submit"
                        class="flex-1 font-alumni text-sm-header bg-primary text-white
                               py-3.5 rounded-2xl hover:brightness-110 active:scale-95 transition-all">
                    Enviar tiquet
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
