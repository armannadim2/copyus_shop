@extends('layouts.app')
@section('title', 'Invitació a ' . $invitation->company->name)

@section('content')
<div class="max-w-lg mx-auto px-4 py-20 text-center">
    <div class="w-16 h-16 mx-auto bg-primary/10 rounded-2xl flex items-center justify-center mb-6">
        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
    </div>

    <h1 class="font-alumni text-h2 text-dark mb-2">Has estat convidat/da</h1>
    <p class="font-outfit text-sm text-gray-500 mb-8">
        <strong>{{ $invitation->company->name }}</strong> t'invita a unir-te com a
        <strong>{{ match($invitation->role) { 'manager' => 'Gestor', 'viewer' => 'Visualitzador', default => 'Comprador' } }}</strong>.
    </p>

    @if($existingUser)
        @if(auth()->check() && strtolower(auth()->user()->email) === $invitation->email)
            <form method="POST" action="{{ route('company.invitation.accept', $invitation->token) }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-primary text-white font-alumni text-sm-header
                               px-10 py-4 rounded-2xl hover:brightness-110 active:scale-95 transition-all">
                    Acceptar i unir-me a {{ $invitation->company->name }}
                </button>
            </form>
        @else
            <p class="font-outfit text-sm text-gray-500 mb-4">
                Inicia sessió amb <strong>{{ $invitation->email }}</strong> per acceptar la invitació.
            </p>
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 bg-primary text-white font-alumni text-sm-header
                      px-10 py-4 rounded-2xl hover:brightness-110 transition-all">
                Iniciar sessió
            </a>
        @endif
    @else
        <p class="font-outfit text-sm text-gray-500 mb-4">
            No tens compte a Copyus. Registra't amb <strong>{{ $invitation->email }}</strong> i la invitació s'aplicarà automàticament.
        </p>
        <a href="{{ route('register') }}"
           class="inline-flex items-center gap-2 bg-primary text-white font-alumni text-sm-header
                  px-10 py-4 rounded-2xl hover:brightness-110 transition-all">
            Crear compte i unir-me
        </a>
    @endif

    <p class="font-outfit text-xs text-gray-400 mt-6">
        Invitació vàlida fins {{ $invitation->expires_at->format('d/m/Y') }}
    </p>
</div>
@endsection
