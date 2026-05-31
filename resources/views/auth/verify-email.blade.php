@extends('layouts.guest')
@section('title', __('app.verify_email_title'))

@section('content')
<div class="min-h-screen bg-light flex flex-col items-center justify-center px-4 py-12">

    {{-- Logo --}}
    <div class="text-center mb-8">
        <a href="{{ route('home') }}">
            <img src="{{ asset('assets/images/logo/' . rawurlencode('FULL LOGO (Red Sun).svg')) }}"
                 alt="Copyus" class="h-12 mx-auto mb-6">
        </a>
        <h1 class="font-alumni text-h2 text-dark leading-tight">
            {{ __('app.verify_email_heading_1') }}
            <span class="text-secondary">{{ __('app.verify_email_heading_2') }}</span>
        </h1>
        <p class="font-alumni text-h5 text-primary mt-3">
            {{ __('app.verify_email_subheading') }}
        </p>
    </div>

    {{-- Card --}}
    <div class="w-full max-w-md bg-white rounded-3xl shadow-sm p-8">

        {{-- Resent success message --}}
        @if(session('status') === 'verification-link-sent')
            <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200
                        rounded-2xl px-5 py-4">
                <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="font-outfit text-sm text-green-700">
                    {{ __('app.verify_email_sent_success') }}
                </p>
            </div>
        @endif

        {{-- Email icon --}}
        <div class="flex items-center justify-center w-20 h-20 bg-secondary/10
                    rounded-full mx-auto mb-6">
            <svg class="w-9 h-9 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>

        <p class="font-outfit text-sm text-gray-500 text-center mb-2">
            {{ __('app.verify_email_sent_to') }}
        </p>
        <p class="font-alumni text-sm-header text-dark text-center mb-6">
            {{ auth()->user()->email }}
        </p>

        <p class="font-outfit text-sm text-gray-500 text-center mb-8 leading-relaxed">
            {{ __('app.verify_email_instruction') }}
        </p>

        {{-- Resend form --}}
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                    class="w-full bg-primary text-white font-alumni text-sm-header
                           py-4 rounded-2xl hover:brightness-110 active:scale-95 transition-all">
                {{ __('app.verify_email_resend_btn') }}
            </button>
        </form>

        <p class="font-outfit text-xs text-gray-400 text-center mt-4">
            {{ __('app.verify_email_spam_hint') }}
        </p>

        {{-- Logout --}}
        <div class="mt-8 pt-6 border-t border-gray-100 text-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="font-outfit text-sm text-gray-400 hover:text-dark transition-colors">
                    {{ __('app.logout') }} →
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
