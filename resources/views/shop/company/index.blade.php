@extends('layouts.app')
@section('title', $company->name . ' — Empresa')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-12 space-y-8">

    {{-- Header --}}
    <div class="flex items-start justify-between flex-wrap gap-4">
        <div>
            <h1 class="font-alumni text-h2 text-dark">{{ $company->name }}</h1>
            @if($company->cif_vat)
                <p class="font-outfit text-sm text-gray-400">{{ $company->cif_vat }}</p>
            @endif
        </div>
        <div class="flex items-center gap-2">
            @if(auth()->user()->canManageCompany())
                <span class="font-outfit text-xs bg-primary/10 text-primary px-3 py-1 rounded-full">
                    {{ auth()->user()->isCompanyOwner() ? 'Propietari' : 'Gestor' }}
                </span>
            @endif
            <span class="font-outfit text-xs bg-secondary/10 text-secondary px-3 py-1 rounded-full">
                Terminis: {{ $company->payment_terms_label }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-2xl px-5 py-4 font-outfit text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-2xl px-5 py-4 font-outfit text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Members --}}
    <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-alumni text-h5 text-dark">Membres ({{ $members->count() }})</h2>
            @if(auth()->user()->canManageCompany())
                <button x-data @click="$dispatch('open-invite')"
                        class="font-outfit text-xs text-primary hover:text-secondary transition-colors flex items-center gap-1">
                    + Convidar membre
                </button>
            @endif
        </div>

        <div class="divide-y divide-gray-50">
            @foreach($members as $member)
                @php
                    $roleLabel = match($member->company_role) {
                        'owner'   => 'Propietari',
                        'manager' => 'Gestor',
                        'buyer'   => 'Comprador',
                        'viewer'  => 'Visualitzador',
                        default   => $member->company_role,
                    };
                    $roleColor = match($member->company_role) {
                        'owner'   => 'bg-primary/10 text-primary',
                        'manager' => 'bg-secondary/10 text-secondary',
                        'viewer'  => 'bg-gray-100 text-gray-500',
                        default   => 'bg-blue-50 text-blue-600',
                    };
                @endphp
                <div class="px-6 py-4 flex items-center gap-4">
                    <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                        <span class="font-alumni text-sm text-primary">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-outfit text-sm font-semibold text-dark">
                            {{ $member->name }}
                            @if($member->id === auth()->id())
                                <span class="text-gray-400 font-normal">(tu)</span>
                            @endif
                        </p>
                        <p class="font-outfit text-xs text-gray-400">{{ $member->email }}</p>
                    </div>
                    <span class="font-outfit text-xs px-2.5 py-1 rounded-full {{ $roleColor }}">{{ $roleLabel }}</span>
                    @if($member->spending_limit)
                        <span class="font-outfit text-xs text-gray-400">Límit: {{ number_format($member->spending_limit, 2, ',', '.') }} €</span>
                    @endif

                    @if(auth()->user()->isCompanyOwner() && $member->id !== auth()->id())
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="text-gray-300 hover:text-gray-500 transition-colors px-2">⋮</button>
                            <div x-show="open" @click.outside="open = false" x-cloak
                                 class="absolute right-0 top-6 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-10 min-w-36">
                                @foreach(['manager' => 'Gestor', 'buyer' => 'Comprador', 'viewer' => 'Visualitzador'] as $role => $label)
                                    @if($member->company_role !== $role)
                                        <form method="POST" action="{{ route('company.members.role', $member) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="role" value="{{ $role }}">
                                            <button type="submit" class="w-full text-left px-4 py-2 font-outfit text-xs text-gray-600 hover:bg-gray-50">
                                                Fer {{ $label }}
                                            </button>
                                        </form>
                                    @endif
                                @endforeach
                                <div class="border-t border-gray-100 mt-1 pt-1">
                                    <form method="POST" action="{{ route('company.members.remove', $member) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="w-full text-left px-4 py-2 font-outfit text-xs text-red-500 hover:bg-red-50">
                                            Eliminar de l'empresa
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Pending invitations --}}
    @if($invitations->isNotEmpty() && auth()->user()->canManageCompany())
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="font-alumni text-h5 text-dark">Invitacions pendents</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($invitations as $inv)
                    <div class="px-6 py-4 flex items-center gap-4">
                        <p class="font-outfit text-sm text-dark flex-1">{{ $inv->email }}</p>
                        <span class="font-outfit text-xs text-gray-400">{{ ucfirst($inv->role) }}</span>
                        <span class="font-outfit text-xs text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">
                            Caduca {{ $inv->expires_at->diffForHumans() }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Edit company + Leave --}}
    <div class="grid sm:grid-cols-2 gap-6">
        @if(auth()->user()->canManageCompany())
        <div class="bg-white rounded-3xl shadow-sm p-6">
            <h2 class="font-alumni text-h5 text-dark mb-4">Dades de l'empresa</h2>
            <form method="POST" action="{{ route('company.update') }}" class="space-y-3">
                @csrf @method('PATCH')
                <input type="text" name="name" value="{{ old('name', $company->name) }}" placeholder="Nom"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 font-outfit text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <input type="email" name="email" value="{{ old('email', $company->email) }}" placeholder="Email empresa"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 font-outfit text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" placeholder="Telèfon"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 font-outfit text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <button type="submit"
                        class="w-full bg-primary text-white font-alumni text-sm-header py-3 rounded-2xl hover:brightness-110 transition-all">
                    Guardar canvis
                </button>
            </form>
        </div>
        @endif

        @if(!auth()->user()->isCompanyOwner())
        <div class="bg-white rounded-3xl shadow-sm p-6 flex flex-col justify-between">
            <div>
                <h2 class="font-alumni text-h5 text-dark mb-2">Abandonar empresa</h2>
                <p class="font-outfit text-xs text-gray-400 mb-4">
                    Deixaràs de tenir accés a les comandes compartides de l'empresa.
                </p>
            </div>
            <form method="POST" action="{{ route('company.leave') }}">
                @csrf @method('DELETE')
                <button type="submit"
                        class="w-full border-2 border-red-200 text-red-500 font-alumni text-sm-header py-3 rounded-2xl hover:bg-red-50 transition-all"
                        onclick="return confirm('Segur que vols abandonar l\'empresa?')">
                    Abandonar empresa
                </button>
            </form>
        </div>
        @endif
    </div>
</div>

{{-- Invite modal --}}
@if(auth()->user()->canManageCompany())
<div x-data="{ open: false }"
     @open-invite.window="open = true"
     x-show="open" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
     @keydown.escape.window="open = false">
    <div class="bg-white rounded-3xl shadow-xl max-w-sm w-full mx-4 p-8" @click.stop>
        <h3 class="font-alumni text-h5 text-dark mb-6">Convidar membre</h3>
        <form method="POST" action="{{ route('company.invite') }}" class="space-y-4">
            @csrf
            <div>
                <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">Email *</label>
                <input type="email" name="email" required autofocus
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 font-outfit text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">Rol *</label>
                <select name="role" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 font-outfit text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="buyer">Comprador — pot fer comandes</option>
                    <option value="manager">Gestor — pot gestionar l'empresa</option>
                    <option value="viewer">Visualitzador — només pot veure</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" @click="open = false"
                        class="flex-1 border-2 border-gray-200 text-gray-500 font-alumni text-sm-header py-3 rounded-2xl hover:bg-gray-50 transition-all">
                    Cancel·lar
                </button>
                <button type="submit"
                        class="flex-1 bg-primary text-white font-alumni text-sm-header py-3 rounded-2xl hover:brightness-110 transition-all">
                    Enviar invitació
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
