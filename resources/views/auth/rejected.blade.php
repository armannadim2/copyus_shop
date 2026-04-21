@extends('layouts.guest')
@section('title', __('app.rejected_title'))

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
            <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <h1 class="font-alumni text-h2 text-dark leading-tight mb-2">
                {{ __('app.rejected_title_1') }}
                <span class="text-secondary">{{ __('app.rejected_title_2') }}</span>
            </h1>
            <p class="font-alumni text-h5 text-primary mb-6">
                {{ __('app.rejected_subtitle') }}
            </p>

            <p class="font-outfit text-sm text-gray-500 mb-6 leading-relaxed">
                {{ __('app.rejected_body') }}
            </p>

            <div class="bg-red-50 border border-red-200 rounded-2xl px-5 py-4 mb-6">
                <p class="font-outfit text-xs text-red-600">
                    {{ __('app.rejected_hint') }}
                </p>
            </div>

            <a href="mailto:info@copyus.es"
               class="inline-flex items-center gap-2 bg-secondary text-white font-alumni text-sm-header
                      px-6 py-3 rounded-2xl hover:brightness-110 active:scale-95 transition-all mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                {{ __('app.contact_support') }}
            </a>

            <div>
                <a href="{{ route('login') }}"
                   class="font-outfit text-xs text-gray-400 hover:text-primary transition-colors">
                    ← {{ __('app.back_to_login') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
