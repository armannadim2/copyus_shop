@extends('layouts.app')
@section('title', $ticket->ticket_number . ' — ' . $ticket->subject)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 pt-16 pb-20">

    {{-- Back --}}
    <a href="{{ route('tickets.index') }}"
       class="font-outfit text-xs text-gray-400 hover:text-primary transition-colors mb-6 inline-flex items-center gap-1">
        ← Tiquets de suport
    </a>

    @if(session('success'))
        <div class="mt-4 mb-6 px-5 py-4 bg-green-50 border border-green-200 rounded-2xl font-outfit text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @php
        $statusLabel = match($ticket->status) {
            'open'        => 'Obert',
            'in_progress' => 'En gestió',
            'resolved'    => 'Resolt',
            'closed'      => 'Tancat',
            default       => $ticket->status,
        };
        $priorityLabel = match($ticket->priority) {
            'urgent' => 'Urgent',
            'high'   => 'Alt',
            'medium' => 'Mitjà',
            'low'    => 'Baix',
            default  => $ticket->priority,
        };
        $isClosed = in_array($ticket->status, ['resolved', 'closed']);
    @endphp

    {{-- Header --}}
    <div class="bg-white rounded-3xl shadow-sm px-8 py-6 mb-6">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap mb-2">
                    <span class="font-outfit text-xs text-gray-400">{{ $ticket->ticket_number }}</span>
                    <span class="font-outfit text-xs px-2.5 py-1 rounded-full {{ $ticket->status_color }}">
                        {{ $statusLabel }}
                    </span>
                    <span class="font-outfit text-xs px-2.5 py-1 rounded-full {{ $ticket->priority_color }}">
                        {{ $priorityLabel }}
                    </span>
                </div>
                <h1 class="font-alumni text-h3 text-dark leading-tight">{{ $ticket->subject }}</h1>
                <p class="font-outfit text-xs text-gray-400 mt-1">
                    Obert el {{ $ticket->created_at->format('d/m/Y \a\l\e\s H:i') }}
                </p>
            </div>

            {{-- Close button --}}
            @if(!$isClosed)
            <form method="POST" action="{{ route('tickets.close', $ticket) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        onclick="return confirm('Tancar el tiquet?')"
                        class="font-outfit text-xs border border-gray-200 text-gray-500 px-4 py-2 rounded-xl
                               hover:border-red-300 hover:text-red-500 transition-colors whitespace-nowrap">
                    Tancar tiquet
                </button>
            </form>
            @endif
        </div>

        {{-- Related order / print job --}}
        @if($ticket->order || $ticket->printJob)
            <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap gap-3">
                @if($ticket->order)
                    <a href="{{ route('orders.show', $ticket->order->order_number) }}"
                       class="inline-flex items-center gap-1.5 font-outfit text-xs text-primary
                              bg-primary/5 px-3 py-1.5 rounded-xl hover:bg-primary/10 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        Comanda #{{ $ticket->order->order_number }}
                    </a>
                @endif
                @if($ticket->printJob)
                    <span class="inline-flex items-center gap-1.5 font-outfit text-xs text-gray-600
                                 bg-gray-50 px-3 py-1.5 rounded-xl">
                        🖨️ {{ $ticket->printJob->template?->getTranslation('name', 'ca') }} #{{ $ticket->printJob->id }}
                    </span>
                @endif
            </div>
        @endif
    </div>

    {{-- Thread --}}
    <div class="space-y-4 mb-6">

        {{-- Original message --}}
        <div class="bg-white rounded-2xl border border-gray-100 px-6 py-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center">
                        <span class="font-alumni text-xs text-primary">
                            {{ substr($ticket->user?->name ?? 'U', 0, 1) }}
                        </span>
                    </div>
                    <div>
                        <p class="font-outfit text-sm font-semibold text-dark">{{ $ticket->user?->name }}</p>
                        <p class="font-outfit text-xs text-gray-400">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <span class="font-outfit text-xs text-gray-300">Missatge inicial</span>
            </div>
            <div class="font-outfit text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $ticket->body }}</div>
        </div>

        {{-- Replies --}}
        @foreach($ticket->replies as $reply)
        @php $isAdmin = $reply->is_admin_reply; @endphp
        <div class="rounded-2xl border px-6 py-5
                    {{ $isAdmin ? 'bg-blue-50 border-blue-100 ml-4' : 'bg-white border-gray-100' }}">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center
                                {{ $isAdmin ? 'bg-blue-200' : 'bg-primary/10' }}">
                        <span class="font-alumni text-xs {{ $isAdmin ? 'text-blue-700' : 'text-primary' }}">
                            {{ $isAdmin ? 'C' : substr($reply->user?->name ?? 'U', 0, 1) }}
                        </span>
                    </div>
                    <div>
                        <p class="font-outfit text-sm font-semibold {{ $isAdmin ? 'text-blue-800' : 'text-dark' }}">
                            {{ $isAdmin ? 'Copyus — Suport' : $reply->user?->name }}
                        </p>
                        <p class="font-outfit text-xs text-gray-400">{{ $reply->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                @if($isAdmin)
                    <span class="font-outfit text-xs bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full">Suport</span>
                @endif
            </div>
            <div class="font-outfit text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $reply->body }}</div>
        </div>
        @endforeach
    </div>

    {{-- Reply form --}}
    @if(!$isClosed)
        <div class="bg-white rounded-3xl shadow-sm px-8 py-6">
            <h2 class="font-alumni text-h5 text-dark mb-4">Afegir resposta</h2>
            <form method="POST" action="{{ route('tickets.reply', $ticket) }}" class="space-y-4">
                @csrf
                <div>
                    <textarea name="body" rows="5" required
                              placeholder="Escriu la teva resposta…"
                              class="w-full border border-gray-200 rounded-2xl px-4 py-3 font-outfit text-sm
                                     focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary resize-none transition
                                     @error('body') border-red-300 @enderror">{{ old('body') }}</textarea>
                    @error('body')
                        <p class="font-outfit text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                            class="font-alumni text-sm-header bg-primary text-white px-8 py-3 rounded-2xl
                                   hover:brightness-110 active:scale-95 transition-all">
                        Enviar resposta
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-gray-50 border border-gray-200 rounded-2xl px-6 py-5 text-center">
            <p class="font-outfit text-sm text-gray-400">
                Aquest tiquet està
                <strong>{{ $isClosed ? 'tancat' : 'resolt' }}</strong>.
                Si necessites més ajuda,
                <a href="{{ route('tickets.create') }}" class="text-primary hover:underline">obre un nou tiquet</a>.
            </p>
        </div>
    @endif
</div>
@endsection
