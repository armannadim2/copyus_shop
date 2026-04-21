@extends('layouts.app')
@section('title', 'Invitació caducada')

@section('content')
<div class="max-w-lg mx-auto px-4 py-20 text-center">
    <div class="w-16 h-16 mx-auto bg-red-100 rounded-2xl flex items-center justify-center mb-6">
        <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <h1 class="font-alumni text-h2 text-dark mb-2">Invitació no vàlida</h1>
    <p class="font-outfit text-sm text-gray-500 mb-8">
        Aquesta invitació ha caducat o ja ha estat acceptada. Demana una nova invitació al propietari de l'empresa.
    </p>
    <a href="{{ route('home') }}"
       class="inline-flex items-center gap-2 bg-primary text-white font-alumni text-sm-header
              px-8 py-3.5 rounded-2xl hover:brightness-110 transition-all">
        Tornar a l'inici
    </a>
</div>
@endsection
