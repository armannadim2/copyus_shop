@extends('layouts.admin')
@section('title', 'Tiquets de suport')

@section('content')
<div class="p-8 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-alumni text-h4 text-dark">Tiquets de suport</h1>
            <p class="font-outfit text-xs text-gray-400 mt-0.5">Gestiona les sol·licituds dels clients</p>
        </div>
    </div>

    {{-- Status tabs --}}
    @php
        $statuses = [
            ''            => 'Tots',
            'open'        => 'Oberts',
            'in_progress' => 'En gestió',
            'resolved'    => 'Resolts',
            'closed'      => 'Tancats',
        ];
        $activeStatus = request('status', '');
    @endphp
    <div class="flex flex-wrap gap-2">
        @foreach($statuses as $key => $label)
            <a href="{{ route('admin.tickets.index', array_merge(request()->except('status', 'page'), $key ? ['status' => $key] : [])) }}"
               class="inline-flex items-center gap-2 font-outfit text-xs font-semibold px-4 py-2 rounded-xl border-2 transition-all
                      {{ $activeStatus === $key
                          ? 'bg-dark text-white border-dark'
                          : 'bg-white text-gray-500 border-gray-200 hover:border-gray-400' }}">
                {{ $label }}
                @if($key && isset($counts[$key]))
                    <span class="bg-white/20 text-inherit px-1.5 py-0.5 rounded-full text-xs">{{ $counts[$key] }}</span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap gap-2">
        @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cercar per tiquet, assumpte o client…"
               class="border border-gray-200 rounded-xl px-4 py-2 font-outfit text-sm
                      focus:outline-none focus:ring-2 focus:ring-primary w-72">
        <select name="priority"
                class="border border-gray-200 rounded-xl px-4 py-2 font-outfit text-sm
                       focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">Totes les prioritats</option>
            <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
            <option value="high"   {{ request('priority') === 'high'   ? 'selected' : '' }}>Alta</option>
            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Mitjana</option>
            <option value="low"    {{ request('priority') === 'low'    ? 'selected' : '' }}>Baixa</option>
        </select>
        <button type="submit"
                class="bg-dark text-white font-outfit text-sm px-4 py-2 rounded-xl hover:bg-primary transition-colors">
            Filtrar
        </button>
        @if(request('search') || request('priority'))
            <a href="{{ route('admin.tickets.index', request()->only('status')) }}"
               class="font-outfit text-sm text-gray-400 hover:text-primary px-4 py-2 rounded-xl border border-gray-200 transition-colors">
                Netejar
            </a>
        @endif
    </form>

    {{-- Table --}}
    @if($tickets->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 px-8 py-12 text-center">
            <p class="font-outfit text-sm text-gray-400">Cap tiquet trobat.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-light">
                        <th class="text-left font-outfit text-xs text-gray-400 px-6 py-3">Tiquet</th>
                        <th class="text-left font-outfit text-xs text-gray-400 px-4 py-3">Client</th>
                        <th class="text-left font-outfit text-xs text-gray-400 px-4 py-3">Estat</th>
                        <th class="text-left font-outfit text-xs text-gray-400 px-4 py-3">Prioritat</th>
                        <th class="text-left font-outfit text-xs text-gray-400 px-4 py-3">Data</th>
                        <th class="text-left font-outfit text-xs text-gray-400 px-4 py-3">Respostes</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
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
                    <tr class="hover:bg-light/50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.tickets.show', $ticket) }}"
                               class="font-outfit text-xs text-gray-400 hover:text-primary transition-colors block">
                                {{ $ticket->ticket_number }}
                            </a>
                            <a href="{{ route('admin.tickets.show', $ticket) }}"
                               class="font-outfit text-sm font-semibold text-dark hover:text-primary transition-colors
                                      max-w-xs truncate block">
                                {{ $ticket->subject }}
                            </a>
                        </td>
                        <td class="px-4 py-4">
                            <p class="font-outfit text-sm text-dark">{{ $ticket->user?->name }}</p>
                            <p class="font-outfit text-xs text-gray-400">{{ $ticket->user?->email }}</p>
                        </td>
                        <td class="px-4 py-4">
                            <span class="font-outfit text-xs px-2.5 py-1 rounded-full {{ $ticket->status_color }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="font-outfit text-xs px-2.5 py-1 rounded-full {{ $ticket->priority_color }}">
                                {{ $priorityLabel }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <p class="font-outfit text-xs text-gray-500">{{ $ticket->created_at->format('d/m/Y') }}</p>
                            <p class="font-outfit text-xs text-gray-400">{{ $ticket->created_at->format('H:i') }}</p>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($ticket->replies_count > 0)
                                <span class="font-outfit text-xs text-gray-500">{{ $ticket->replies_count }}</span>
                            @else
                                <span class="font-outfit text-xs text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right">
                            <a href="{{ route('admin.tickets.show', $ticket) }}"
                               class="font-outfit text-xs text-primary hover:underline">
                                Veure →
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $tickets->links() }}
        </div>
    @endif
</div>
@endsection
