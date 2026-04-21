@extends('layouts.guest')
@section('title', __('app.pending_title'))

@section('content')
<div class="min-h-screen bg-light flex flex-col items-center justify-center px-4 py-12">

    {{-- Logo --}}
    <a href="{{ route('home') }}" class="mb-10">
        <img src="{{ asset('assets/images/logo/' . rawurlencode('FULL LOGO (Red Sun).svg')) }}"
             alt="Copyus" class="h-12 mx-auto">
    </a>

    <div class="w-full max-w-md">
        <div class="bg-white rounded-3xl shadow-sm p-10 text-center">

            {{-- Icon --}}
            <div class="w-16 h-16 bg-yellow-50 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <h1 class="font-alumni text-h2 text-dark leading-tight mb-2">
                {{ __('app.pending_title_1') }}
                <span class="text-secondary">{{ __('app.pending_title_2') }}</span>
            </h1>
            <p class="font-alumni text-h5 text-primary mb-6">
                {{ __('app.pending_subtitle') }}
            </p>

            <p class="font-outfit text-sm text-gray-500 mb-6 leading-relaxed">
                {{ __('app.pending_body') }}
            </p>

            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl px-5 py-4 mb-6">
                <p class="font-outfit text-xs text-yellow-700">
                    {{ __('app.pending_response_time') }}
                </p>
            </div>

            <p class="font-outfit text-xs text-gray-400 mb-6">
                {{ __('app.pending_questions') }}
                <a href="mailto:info@copyus.es"
                   class="text-secondary hover:text-primary transition-colors font-semibold">
                    {{ __('app.contact_us') }}
                </a>
            </p>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="font-outfit text-xs text-gray-400 hover:text-primary transition-colors underline">
                    {{ __('app.logout') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
