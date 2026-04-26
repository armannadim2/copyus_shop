@extends('layouts.admin')
@section('title', 'Missatges de contacte')

@section('content')
<div class="p-8 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-alumni text-h4 text-dark">Missatges de contacte</h1>
            <p class="font-outfit text-xs text-gray-400 mt-0.5">
                Missatges rebuts des de la pàgina pública «Contacte»
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="px-5 py-4 bg-green-50 border border-green-200 rounded-2xl font-outfit text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @php
        $statuses = [
            ''         => 'Tots',
            'new'      => 'Nous',
            'read'     => 'Llegits',
            'replied'  => 'Respostos',
            'archived' => 'Arxivats',
        ];
        $activeStatus = request('status', '');
    @endphp
    <div class="flex flex-wrap gap-2">
        @foreach($statuses as $key => $label)
            <a href="{{ route('admin.contact-messages.index', array_merge(request()->except('status', 'page'), $key ? ['status' => $key] : [])) }}"
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

    <form method="GET" class="flex flex-wrap gap-2">
        @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cercar per nom, correu o assumpte…"
               class="border border-gray-200 rounded-xl px-4 py-2 font-outfit text-sm
                      focus:outline-none focus:ring-2 focus:ring-primary w-80">
        <button type="submit"
                class="bg-dark text-white font-outfit text-sm px-4 py-2 rounded-xl hover:bg-primary transition-colors">
            Filtrar
        </button>
        @if(request('search'))
            <a href="{{ route('admin.contact-messages.index', request()->only('status')) }}"
               class="font-outfit text-sm text-gray-400 hover:text-primary px-4 py-2 rounded-xl border border-gray-200 transition-colors">
                Netejar
            </a>
        @endif
    </form>

    @if($messages->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 px-8 py-12 text-center">
            <p class="font-outfit text-sm text-gray-400">Cap missatge trobat.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-light">
                        <th class="text-left font-outfit text-xs text-gray-400 px-6 py-3">Remitent</th>
                        <th class="text-left font-outfit text-xs text-gray-400 px-4 py-3">Assumpte</th>
                        <th class="text-left font-outfit text-xs text-gray-400 px-4 py-3">Estat</th>
                        <th class="text-left font-outfit text-xs text-gray-400 px-4 py-3">Data</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($messages as $msg)
                    <tr class="hover:bg-light/50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-outfit text-sm font-semibold text-dark">{{ $msg->name }}</p>
                            <p class="font-outfit text-xs text-gray-400">{{ $msg->email }}</p>
                        </td>
                        <td class="px-4 py-4">
                            <a href="{{ route('admin.contact-messages.show', $msg) }}"
                               class="font-outfit text-sm text-dark hover:text-primary transition-colors
                                      max-w-md truncate block">
                                {{ $msg->subject }}
                            </a>
                        </td>
                        <td class="px-4 py-4">
                            <span class="font-outfit text-xs px-2.5 py-1 rounded-full {{ $msg->status_color }}">
                                {{ $msg->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <p class="font-outfit text-xs text-gray-500">{{ $msg->created_at->format('d/m/Y') }}</p>
                            <p class="font-outfit text-xs text-gray-400">{{ $msg->created_at->format('H:i') }}</p>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <a href="{{ route('admin.contact-messages.show', $msg) }}"
                               class="font-outfit text-xs text-primary hover:underline">
                                Veure →
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $messages->links() }}</div>
    @endif
</div>
@endsection
