@extends('layouts.app')
@section('title', 'Les meves configuracions guardades')

@section('content')

<div class="text-center pt-16 pb-12 px-4">
    <h1 class="font-alumni text-h1 text-dark leading-tight mb-4">
        Configuracions
        <span class="text-secondary">guardades</span>
    </h1>
    <p class="font-alumni text-h5 text-primary max-w-xl mx-auto">
        Totes les configuracions d'impressió que has desat per reutilitzar
    </p>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 pb-20">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-2xl px-5 py-4 mb-6 font-outfit text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if($configs->isEmpty())
        <div class="bg-white rounded-3xl shadow-sm p-16 text-center">
            <div class="w-16 h-16 mx-auto bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                </svg>
            </div>
            <h2 class="font-alumni text-h4 text-dark mb-3">Cap configuració guardada</h2>
            <p class="font-outfit text-sm text-gray-400 mb-6">
                Ves al configurador d'impressió i desa les opcions que fas servir habitualment.
            </p>
            <a href="{{ route('print.index') }}"
               class="inline-flex items-center gap-2 bg-primary text-white font-alumni text-sm-header
                      px-8 py-3 rounded-2xl hover:brightness-110 active:scale-95 transition-all">
                Explorar plantilles
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($configs as $config)
                <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="flex items-start gap-4 flex-1 min-w-0">
                            <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center shrink-0">
                                <span class="text-xl">{{ $config->template->icon ?? '🖨️' }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-alumni text-sm-header text-dark">{{ $config->name }}</p>
                                <p class="font-outfit text-xs text-gray-400 mt-0.5">
                                    {{ $config->template->getTranslation('name', app()->getLocale()) }}
                                    · {{ number_format($config->quantity, 0, ',', '.') }} unitats
                                    · Guardat {{ $config->created_at->diffForHumans() }}
                                </p>
                                @if($config->configuration)
                                    <div class="flex flex-wrap gap-1.5 mt-2">
                                        @foreach($config->configuration as $val)
                                            <span class="font-outfit text-xs bg-light text-dark/60 px-2 py-0.5 rounded-full">
                                                {{ $val }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 shrink-0">
                            {{-- Load in configurator --}}
                            @if($config->template->is_active)
                                <a href="{{ route('print.builder', $config->template->slug) }}?config_id={{ $config->id }}"
                                   class="font-outfit text-xs text-secondary hover:text-primary transition-colors">
                                    Configurar →
                                </a>

                                {{-- Add directly to cart --}}
                                <form method="POST" action="{{ route('print.configs.add-to-cart', $config) }}">
                                    @csrf
                                    <button type="submit"
                                            class="font-outfit text-xs bg-primary/10 text-primary px-3 py-1.5
                                                   rounded-xl hover:bg-primary/20 transition-colors">
                                        + Afegir a cistella
                                    </button>
                                </form>
                            @else
                                <span class="font-outfit text-xs text-gray-300">Plantilla inactiva</span>
                            @endif

                            {{-- Delete --}}
                            <form method="POST" action="{{ route('print.configs.destroy', $config) }}">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="font-outfit text-xs text-gray-300 hover:text-red-400 transition-colors"
                                        onclick="return confirm('Eliminar aquesta configuració?')">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($configs->hasPages())
            <div class="mt-6">{{ $configs->links() }}</div>
        @endif
    @endif

    <div class="mt-10 flex flex-wrap gap-4 justify-center">
        <a href="{{ route('print.index') }}"
           class="font-outfit text-sm text-secondary hover:text-primary transition-colors">
            ← Explorar plantilles d'impressió
        </a>
        <a href="{{ route('print.my-jobs') }}"
           class="font-outfit text-sm text-gray-400 hover:text-primary transition-colors">
            Veure treballs d'impressió
        </a>
    </div>
</div>
@endsection
