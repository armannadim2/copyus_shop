@extends('layouts.admin')
@section('title', $contactMessage->subject)

@section('content')
<div class="p-8 max-w-5xl space-y-6">

    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('admin.contact-messages.index') }}"
           class="font-outfit text-xs text-gray-400 hover:text-primary transition-colors">
            ← Missatges de contacte
        </a>
        <span class="text-gray-300">/</span>
        <span class="font-outfit text-xs text-dark">#{{ $contactMessage->id }}</span>
    </div>

    @if(session('success'))
        <div class="px-5 py-4 bg-green-50 border border-green-200 rounded-2xl font-outfit text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-start justify-between flex-wrap gap-4">
        <div>
            <div class="flex items-center gap-2 flex-wrap mb-2">
                <span class="font-outfit text-xs px-2.5 py-1 rounded-full {{ $contactMessage->status_color }}">
                    {{ $contactMessage->status_label }}
                </span>
            </div>
            <h1 class="font-alumni text-h3 text-dark">{{ $contactMessage->subject }}</h1>
            <p class="font-outfit text-xs text-gray-400 mt-1">
                Rebut el {{ $contactMessage->created_at->format('d/m/Y \a\l\e\s H:i') }}
            </p>
        </div>
    </div>

    <div class="grid lg:grid-cols-[1fr_320px] gap-6">

        <div class="space-y-4">

            <div class="bg-white rounded-2xl border border-gray-100 px-6 py-5">
                <p class="font-outfit text-xs text-gray-400 mb-2">Missatge</p>
                <div class="font-outfit text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $contactMessage->message }}</div>
            </div>

            <form method="POST" action="{{ route('admin.contact-messages.update', $contactMessage) }}"
                  class="bg-white rounded-2xl border border-gray-100 px-6 py-5 space-y-4">
                @csrf @method('PATCH')
                <p class="font-outfit text-xs text-gray-400">Gestió interna</p>

                <div>
                    <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">Estat</label>
                    <select name="status"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                   font-outfit text-sm focus:outline-none
                                   focus:ring-2 focus:ring-primary/40 focus:border-primary
                                   transition bg-white">
                        @foreach(['new' => 'Nou', 'read' => 'Llegit', 'replied' => 'Respost', 'archived' => 'Arxivat'] as $key => $label)
                            <option value="{{ $key }}" @selected($contactMessage->status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">Notes internes</label>
                    <textarea name="admin_notes" rows="4" maxlength="5000"
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                     font-outfit text-sm focus:outline-none
                                     focus:ring-2 focus:ring-primary/40 focus:border-primary
                                     transition">{{ old('admin_notes', $contactMessage->admin_notes) }}</textarea>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="bg-primary text-white font-alumni text-sm-header px-6 py-2.5
                                   rounded-xl hover:brightness-110 transition-all">
                        Desar canvis
                    </button>
                    <a href="mailto:{{ $contactMessage->email }}?subject=Re: {{ urlencode($contactMessage->subject) }}"
                       class="font-outfit text-sm font-semibold text-primary hover:underline">
                        Respondre per correu →
                    </a>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.contact-messages.destroy', $contactMessage) }}"
                  onsubmit="return confirm('Eliminar aquest missatge? Aquesta acció no es pot desfer.')">
                @csrf @method('DELETE')
                <button type="submit" class="font-outfit text-xs text-red-500 hover:underline">
                    Eliminar missatge
                </button>
            </form>
        </div>

        <aside class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 px-6 py-5">
                <p class="font-outfit text-xs text-gray-400 mb-3">Remitent</p>
                <div class="space-y-3 font-outfit text-sm">
                    <div>
                        <p class="text-xs text-gray-400">Nom</p>
                        <p class="text-dark">{{ $contactMessage->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Correu</p>
                        <a href="mailto:{{ $contactMessage->email }}"
                           class="text-primary hover:underline break-all">
                            {{ $contactMessage->email }}
                        </a>
                    </div>
                    @if($contactMessage->phone)
                        <div>
                            <p class="text-xs text-gray-400">Telèfon</p>
                            <a href="tel:{{ $contactMessage->phone }}"
                               class="text-primary hover:underline">
                                {{ $contactMessage->phone }}
                            </a>
                        </div>
                    @endif
                    @if($contactMessage->user)
                        <div class="pt-3 border-t border-gray-100">
                            <p class="text-xs text-gray-400">Usuari registrat</p>
                            <a href="{{ route('admin.users.show', $contactMessage->user_id) }}"
                               class="text-primary hover:underline">
                                Veure perfil →
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
