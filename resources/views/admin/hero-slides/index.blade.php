@extends('layouts.admin')
@section('title', 'Slides portada')

@section('content')
<div class="p-8">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="font-alumni text-h5 text-dark">Slides portada</h1>
            <p class="font-outfit text-body-sm text-gray-500 mt-1">
                Gestiona les imatges del slider del banner principal.
            </p>
        </div>
        <a href="{{ route('admin.hero-slides.create') }}"
           class="inline-flex items-center gap-2 bg-primary text-white font-outfit text-sm
                  px-5 py-2.5 rounded-xl hover:brightness-110 active:scale-95 transition-all">
            + Nou slide
        </a>
    </div>

    @if($slides->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
            <p class="font-outfit text-4xl mb-4">🖼️</p>
            <p class="font-alumni text-h6 text-dark mb-2">Cap slide configurat</p>
            <p class="font-outfit text-sm text-gray-400 mb-6">
                Afegeix imatges per activar el slider de la portada.
            </p>
            <a href="{{ route('admin.hero-slides.create') }}"
               class="inline-flex items-center gap-2 bg-primary text-white font-outfit text-sm
                      px-6 py-3 rounded-xl hover:brightness-110 transition-all">
                Afegir primer slide
            </a>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-100 bg-light/50">
                        <th class="font-outfit text-xs font-semibold text-gray-400 uppercase tracking-wider px-5 py-4 w-24">Ordre</th>
                        <th class="font-outfit text-xs font-semibold text-gray-400 uppercase tracking-wider px-5 py-4 w-36">Previsualització</th>
                        <th class="font-outfit text-xs font-semibold text-gray-400 uppercase tracking-wider px-5 py-4">Text</th>
                        <th class="font-outfit text-xs font-semibold text-gray-400 uppercase tracking-wider px-5 py-4 w-28">Estat</th>
                        <th class="font-outfit text-xs font-semibold text-gray-400 uppercase tracking-wider px-5 py-4 text-right w-36">Accions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($slides as $slide)
                    <tr class="hover:bg-light/40 transition-colors group">

                        {{-- Sort order arrows --}}
                        <td class="px-5 py-4">
                            <div class="flex flex-col items-center gap-0.5">
                                <form method="POST" action="{{ route('admin.hero-slides.move-up', $slide) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-300 hover:text-dark transition-colors"
                                            title="Pujar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        </svg>
                                    </button>
                                </form>
                                <span class="font-outfit text-xs text-gray-400 w-6 text-center leading-none">
                                    {{ $slide->sort_order }}
                                </span>
                                <form method="POST" action="{{ route('admin.hero-slides.move-down', $slide) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-300 hover:text-dark transition-colors"
                                            title="Baixar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>

                        {{-- Thumbnail --}}
                        <td class="px-5 py-4">
                            <div class="w-28 h-16 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0 shadow-sm">
                                <img src="{{ $slide->imageUrl() }}"
                                     alt="{{ $slide->eyebrow ?? 'Slide' }}"
                                     class="w-full h-full object-cover">
                            </div>
                        </td>

                        {{-- Text --}}
                        <td class="px-5 py-4">
                            @if($slide->eyebrow)
                                <p class="font-outfit text-xs font-semibold text-gray-400 uppercase tracking-widest mb-0.5">
                                    {{ $slide->eyebrow }}
                                </p>
                            @endif
                            @if($slide->title)
                                <p class="font-outfit text-sm font-medium text-dark">
                                    {{ $slide->title }}
                                </p>
                            @endif
                            @if(! $slide->eyebrow && ! $slide->title)
                                <span class="font-outfit text-xs text-gray-300 italic">Sense text</span>
                            @endif
                        </td>

                        {{-- Active toggle --}}
                        <td class="px-5 py-4">
                            <form method="POST" action="{{ route('admin.hero-slides.toggle', $slide) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-outfit font-semibold transition-colors
                                               {{ $slide->is_active
                                                  ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                                  : 'bg-gray-100 text-gray-400 hover:bg-gray-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $slide->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                    {{ $slide->is_active ? 'Actiu' : 'Inactiu' }}
                                </button>
                            </form>
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.hero-slides.edit', $slide) }}"
                                   class="inline-flex items-center px-3 py-1.5 rounded-lg
                                          bg-secondary/10 text-secondary hover:bg-secondary/20
                                          font-outfit text-xs font-medium transition-colors">
                                    Editar
                                </a>
                                <form method="POST" action="{{ route('admin.hero-slides.destroy', $slide) }}"
                                      onsubmit="return confirm('Segur que vols eliminar aquest slide? L\'acció no es pot desfer.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg
                                                   bg-red-50 text-red-500 hover:bg-red-100
                                                   font-outfit text-xs font-medium transition-colors">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <p class="font-outfit text-xs text-gray-400 mt-4">
            {{ $slides->count() }} {{ $slides->count() === 1 ? 'slide' : 'slides' }} ·
            {{ $slides->where('is_active', true)->count() }} actius
        </p>
    @endif

</div>
@endsection
