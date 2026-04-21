@extends('layouts.app')
@section('title', 'Els meus tiquets de suport')

@section('content')

{{-- Hero --}}
<div class="text-center pt-16 pb-12 px-4">
    <h1 class="font-alumni text-h1 text-dark leading-tight mb-4">
        Suport
        <span class="text-secondary">al client</span>
    </h1>
    <p class="font-alumni text-h5 text-primary max-w-xl mx-auto">
        Historial de les teves sol·licituds de suport
    </p>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 pb-20">

    @if(session('success'))
        <div class="mb-6 px-5 py-4 bg-green-50 border border-green-200 rounded-2xl font-outfit text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Actions --}}
    <div class="flex justify-end mb-6">
        <a href="{{ route('tickets.create') }}"
           class="font-alumni text-sm-header bg-primary text-white px-6 py-3 rounded-2xl
                  hover:brightness-110 active:scale-95 transition-all">
            + Nou tiquet
        </a>
    </div>

    @if($tickets->isEmpty())
        <div class="bg-white rounded-3xl shadow-sm px-8 py-16 text-center">
            <p class="text-4xl mb-4">🎫</p>
            <p class="font-alumni text-h5 text-dark mb-2">Cap tiquet obert</p>
            <p class="font-outfit text-sm text-gray-400 mb-6">
                Si tens algun problema o dubte, obre un tiquet i t'ajudarem.
            </p>
            <a href="{{ route('tickets.create') }}"
               class="font-alumni text-sm-header bg-primary text-white px-8 py-3 rounded-2xl
                      hover:brightness-110 transition-all">
                Obrir primer tiquet
            </a>
        </div>
    @else
        <div class="space-y-3">
            @foreach($tickets as $ticket)
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
            @endphp
            <a href="{{ route('tickets.show', $ticket) }}"
               class="block bg-white rounded-2xl border border-gray-100 px-6 py-5 hover:border-primary/30
                      hover:shadow-sm transition-all group">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <span class="font-outfit text-xs text-gray-400">{{ $ticket->ticket_number }}</span>
                            <span class="font-outfit text-xs px-2 py-0.5 rounded-full {{ $ticket->status_color }}">
                                {{ $statusLabel }}
                            </span>
                            <span class="font-outfit text-xs px-2 py-0.5 rounded-full {{ $ticket->priority_color }}">
                                {{ $priorityLabel }}
                            </span>
                        </div>
                        <p class="font-alumni text-h6 text-dark group-hover:text-primary transition-colors truncate">
                            {{ $ticket->subject }}
                        </p>
                        <p class="font-outfit text-xs text-gray-400 mt-1">
                            {{ $ticket->created_at->format('d/m/Y H:i') }}
                            @if($ticket->replies_count > 0)
                                · {{ $ticket->replies_count }} {{ $ticket->replies_count === 1 ? 'resposta' : 'respostes' }}
                            @endif
                        </p>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-primary shrink-0 mt-1 transition-colors"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $tickets->links() }}
        </div>
    @endif
</div>
@endsection
