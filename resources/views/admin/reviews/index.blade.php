@extends('layouts.admin')
@section('title', 'Ressenyes')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center justify-between mb-8">
        <h1 class="font-alumni text-h1 text-dark">Ressenyes de productes</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.reviews.index') }}"
               class="font-outfit text-sm px-4 py-2 rounded-xl border {{ !request('status') ? 'bg-primary text-white border-primary' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} transition-colors">
                Totes
            </a>
            <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}"
               class="font-outfit text-sm px-4 py-2 rounded-xl border {{ request('status') === 'pending' ? 'bg-orange-500 text-white border-orange-500' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} transition-colors">
                Pendents
            </a>
            <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}"
               class="font-outfit text-sm px-4 py-2 rounded-xl border {{ request('status') === 'approved' ? 'bg-green-600 text-white border-green-600' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} transition-colors">
                Aprovades
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 font-outfit text-sm px-4 py-3 rounded-2xl">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-4">
        @forelse($reviews as $review)
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <div class="flex items-start justify-between gap-4">

                    {{-- Left: review content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center flex-wrap gap-3 mb-2">
                            {{-- Stars --}}
                            <div class="flex gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>

                            {{-- Status badge --}}
                            @if($review->is_approved)
                                <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 font-outfit text-xs px-2.5 py-1 rounded-full">
                                    ✓ Aprovada
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-orange-50 text-orange-600 font-outfit text-xs px-2.5 py-1 rounded-full">
                                    ⏳ Pendent
                                </span>
                            @endif

                            {{-- Verified purchase --}}
                            @if($review->is_verified_purchase)
                                <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-600 font-outfit text-xs px-2.5 py-1 rounded-full">
                                    ✓ Compra verificada
                                </span>
                            @endif
                        </div>

                        @if($review->title)
                            <h3 class="font-alumni text-h6 text-dark mb-1">{{ $review->title }}</h3>
                        @endif

                        <p class="font-outfit text-sm text-gray-600 leading-relaxed mb-3">{{ $review->body }}</p>

                        {{-- Photos --}}
                        @if($review->photos)
                            <div class="flex flex-wrap gap-2 mb-3">
                                @foreach($review->photos as $photo)
                                    <a href="{{ asset('storage/' . $photo) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $photo) }}"
                                             alt="foto ressenya"
                                             class="w-16 h-16 object-cover rounded-xl border border-gray-100 hover:opacity-80 transition-opacity">
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        {{-- Meta --}}
                        <div class="flex flex-wrap items-center gap-4 font-outfit text-xs text-gray-400">
                            <span>
                                <span class="text-gray-600 font-medium">{{ $review->user->name }}</span>
                                · {{ $review->user->company_name ?? $review->user->email }}
                            </span>
                            @if($review->product)
                                <span>
                                    Producte:
                                    <a href="{{ route('admin.products.edit', $review->product->id) }}"
                                       class="text-primary hover:underline">
                                        {{ $review->product->getTranslation('name', app()->getLocale()) }}
                                    </a>
                                </span>
                            @endif
                            @if($review->order)
                                <span>
                                    Comanda:
                                    <a href="{{ route('admin.orders.show', $review->order->order_number) }}"
                                       class="text-primary hover:underline">
                                        #{{ $review->order->order_number }}
                                    </a>
                                </span>
                            @endif
                            <span>{{ $review->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>

                    {{-- Right: action buttons --}}
                    <div class="flex flex-col gap-2 shrink-0">
                        @if(!$review->is_approved)
                            <form method="POST" action="{{ route('admin.reviews.approve', $review->id) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="w-full bg-green-600 text-white font-outfit text-xs px-4 py-2
                                               rounded-xl hover:bg-green-700 transition-colors">
                                    Aprovar
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.reviews.reject', $review->id) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="w-full bg-orange-500 text-white font-outfit text-xs px-4 py-2
                                               rounded-xl hover:bg-orange-600 transition-colors">
                                    Rebutjar
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.reviews.destroy', $review->id) }}"
                              onsubmit="return confirm('Eliminar aquesta ressenya?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="w-full border border-red-200 text-red-500 font-outfit text-xs px-4 py-2
                                           rounded-xl hover:bg-red-50 transition-colors">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
                <p class="font-alumni text-h4 text-gray-300 mb-2">Cap ressenya</p>
                <p class="font-outfit text-sm text-gray-400">
                    {{ request('status') === 'pending' ? 'No hi ha ressenyes pendents d\'aprovació.' : 'Encara no hi ha ressenyes.' }}
                </p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($reviews->hasPages())
        <div class="mt-8">
            {{ $reviews->links() }}
        </div>
    @endif

</div>
@endsection
