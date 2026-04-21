@extends('layouts.admin')
@section('title', $ticket->ticket_number)

@section('content')
<div class="p-8 max-w-5xl space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('admin.tickets.index') }}"
           class="font-outfit text-xs text-gray-400 hover:text-primary transition-colors">
            ← Tiquets de suport
        </a>
        <span class="text-gray-300">/</span>
        <span class="font-outfit text-xs text-dark">{{ $ticket->ticket_number }}</span>
    </div>

    @if(session('success'))
        <div class="px-5 py-4 bg-green-50 border border-green-200 rounded-2xl font-outfit text-sm text-green-700">
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
    @endphp

    {{-- Header row --}}
    <div class="flex items-start justify-between flex-wrap gap-4">
        <div>
            <div class="flex items-center gap-2 flex-wrap mb-2">
                <span class="font-outfit text-xs text-gray-400">{{ $ticket->ticket_number }}</span>
                <span class="font-outfit text-xs px-2.5 py-1 rounded-full {{ $ticket->status_color }}">
                    {{ $statusLabel }}
                </span>
                <span class="font-outfit text-xs px-2.5 py-1 rounded-full {{ $ticket->priority_color }}">
                    {{ $priorityLabel }}
                </span>
            </div>
            <h1 class="font-alumni text-h3 text-dark">{{ $ticket->subject }}</h1>
            <p class="font-outfit text-xs text-gray-400 mt-1">
                Creat el {{ $ticket->created_at->format('d/m/Y \a\l\e\s H:i') }}
                @if($ticket->resolved_at)
                    · Resolt el {{ $ticket->resolved_at->format('d/m/Y') }}
                @endif
            </p>
        </div>
    </div>

    <div class="grid lg:grid-cols-[1fr_320px] gap-6">

        {{-- ── LEFT: conversation thread ──────────────────────────────────── --}}
        <div class="space-y-4">

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
                            <p class="font-outfit text-xs text-gray-400">{{ $ticket->user?->email }}</p>
                        </div>
                    </div>
                    <p class="font-outfit text-xs text-gray-400">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="font-outfit text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $ticket->body }}</div>

                {{-- Related records --}}
                @if($ticket->order || $ticket->printJob)
                <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap gap-3">
                    @if($ticket->order)
                        <a href="{{ route('admin.orders.show', $ticket->order->order_number) }}"
                           class="inline-flex items-center gap-1.5 font-outfit text-xs text-primary
                                  bg-primary/5 px-3 py-1.5 rounded-xl hover:bg-primary/10 transition-colors">
                            Comanda #{{ $ticket->order->order_number }}
                        </a>
                    @endif
                    @if($ticket->printJob)
                        <a href="{{ route('admin.print.jobs.show', $ticket->printJob) }}"
                           class="inline-flex items-center gap-1.5 font-outfit text-xs text-gray-600
                                  bg-gray-50 px-3 py-1.5 rounded-xl hover:bg-gray-100 transition-colors">
                            🖨️ Treball #{{ $ticket->printJob->id }}
                            — {{ $ticket->printJob->template?->getTranslation('name', 'ca') }}
                        </a>
                    @endif
                </div>
                @endif
            </div>

            {{-- Replies --}}
            @foreach($ticket->replies as $reply)
            @php $isAdmin = $reply->is_admin_reply; @endphp
            <div class="rounded-2xl border px-6 py-5
                        {{ $isAdmin ? 'bg-blue-50 border-blue-100 ml-6' : 'bg-white border-gray-100' }}">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center
                                    {{ $isAdmin ? 'bg-blue-200' : 'bg-primary/10' }}">
                            <span class="font-alumni text-xs {{ $isAdmin ? 'text-blue-700' : 'text-primary' }}">
                                {{ substr($reply->user?->name ?? '?', 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="font-outfit text-sm font-semibold {{ $isAdmin ? 'text-blue-800' : 'text-dark' }}">
                                    {{ $reply->user?->name }}
                                </p>
                                @if($isAdmin)
                                    <span class="font-outfit text-xs bg-blue-200 text-blue-700 px-1.5 py-0.5 rounded-full">Admin</span>
                                @endif
                            </div>
                            <p class="font-outfit text-xs text-gray-400">{{ $reply->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
                <div class="font-outfit text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $reply->body }}</div>
            </div>
            @endforeach

            {{-- Reply form --}}
            @if(!in_array($ticket->status, ['resolved', 'closed']))
            <div class="bg-white rounded-2xl border border-gray-100 px-6 py-5">
                <h2 class="font-alumni text-h5 text-dark mb-4">Respondre al client</h2>
                <form method="POST" action="{{ route('admin.tickets.reply', $ticket) }}" class="space-y-4">
                    @csrf
                    <textarea name="body" rows="5" required
                              placeholder="Escriu la resposta per al client…"
                              class="w-full border border-gray-200 rounded-xl px-4 py-3 font-outfit text-sm
                                     focus:outline-none focus:ring-2 focus:ring-primary/40 resize-none transition
                                     @error('body') border-red-300 @enderror">{{ old('body') }}</textarea>
                    @error('body')
                        <p class="font-outfit text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
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
            <div class="bg-gray-50 border border-gray-200 rounded-2xl px-6 py-4 text-center">
                <p class="font-outfit text-sm text-gray-400">El tiquet està tancat. No es poden afegir més respostes.</p>
            </div>
            @endif

        </div>

        {{-- ── RIGHT: info + actions ───────────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Client info --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="font-alumni text-h5 text-dark mb-4">Client</h2>
                <div class="space-y-3">
                    <div>
                        <p class="font-outfit text-xs text-gray-400">Nom</p>
                        <p class="font-outfit text-sm text-dark font-semibold">{{ $ticket->user?->name }}</p>
                    </div>
                    <div>
                        <p class="font-outfit text-xs text-gray-400">Email</p>
                        <a href="mailto:{{ $ticket->user?->email }}"
                           class="font-outfit text-sm text-primary hover:underline">
                            {{ $ticket->user?->email }}
                        </a>
                    </div>
                    @if($ticket->user?->company_name)
                    <div>
                        <p class="font-outfit text-xs text-gray-400">Empresa</p>
                        <p class="font-outfit text-sm text-dark">{{ $ticket->user->company_name }}</p>
                    </div>
                    @endif
                    <div class="pt-2">
                        <a href="{{ route('admin.users.show', $ticket->user) }}"
                           class="font-outfit text-xs text-primary hover:underline">
                            Veure perfil del client →
                        </a>
                    </div>
                </div>
            </div>

            {{-- Status change --}}
            @if(!empty($allowedTransitions))
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="font-alumni text-h5 text-dark mb-4">Canviar estat</h2>
                <form method="POST" action="{{ route('admin.tickets.status', $ticket) }}" class="space-y-3">
                    @csrf @method('PATCH')
                    <select name="status" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                   font-outfit text-sm focus:outline-none focus:ring-2
                                   focus:ring-primary/40 focus:border-primary transition">
                        @foreach($allowedTransitions as $transition)
                            <option value="{{ $transition }}">
                                {{ match($transition) {
                                    'open'        => 'Obert',
                                    'in_progress' => 'En gestió',
                                    'resolved'    => 'Resolt',
                                    'closed'      => 'Tancat',
                                    default       => $transition,
                                } }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit"
                            class="w-full bg-dark text-white font-alumni text-sm-header
                                   py-3 rounded-2xl hover:bg-primary transition-all">
                        Actualitzar estat
                    </button>
                </form>
            </div>
            @endif

            {{-- Ticket details --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="font-alumni text-h5 text-dark mb-4">Detalls</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="font-outfit text-xs text-gray-400">Estat</span>
                        <span class="font-outfit text-xs px-2 py-0.5 rounded-full {{ $ticket->status_color }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-outfit text-xs text-gray-400">Prioritat</span>
                        <span class="font-outfit text-xs px-2 py-0.5 rounded-full {{ $ticket->priority_color }}">
                            {{ $priorityLabel }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-outfit text-xs text-gray-400">Respostes</span>
                        <span class="font-outfit text-sm text-dark">{{ $ticket->replies->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-outfit text-xs text-gray-400">Creat</span>
                        <span class="font-outfit text-xs text-gray-600">{{ $ticket->created_at->format('d/m/Y') }}</span>
                    </div>
                    @if($ticket->resolved_at)
                    <div class="flex justify-between">
                        <span class="font-outfit text-xs text-gray-400">Tancat</span>
                        <span class="font-outfit text-xs text-gray-600">{{ $ticket->resolved_at->format('d/m/Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
