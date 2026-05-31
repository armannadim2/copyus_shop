@extends('layouts.app')
@section('title', __('app.pending_approval'))

@section('content')
<div class="min-h-screen bg-light flex items-center justify-center py-12 px-4">
    <div class="max-w-lg w-full bg-white rounded-2xl shadow-lg p-10 text-center">

        {{-- Email not verified banner --}}
        @if(auth()->user() && ! auth()->user()->hasVerifiedEmail())
            <div class="mb-6 flex items-start gap-3 bg-amber-50 border border-amber-200
                        rounded-2xl px-5 py-4 text-left">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1">
                    <p class="font-outfit text-sm font-semibold text-amber-800 mb-1">
                        {{ __('app.pending_verify_email_required') }}
                    </p>
                    <p class="font-outfit text-xs text-amber-700 mb-3">
                        {{ __('app.pending_verify_email_hint') }}
                    </p>
                    <a href="{{ route('verification.notice') }}"
                       class="inline-flex items-center gap-1.5 font-outfit text-xs font-semibold
                              text-white bg-amber-500 hover:bg-amber-600 px-4 py-2 rounded-xl
                              transition-colors">
                        {{ __('app.pending_verify_email_btn') }} →
                    </a>
                </div>
            </div>
        @endif

        {{-- Icon --}}
        <div class="flex items-center justify-center w-20 h-20 bg-yellow-50
                    rounded-full mx-auto mb-6">
            <span class="text-4xl">{{ auth()->user()?->hasVerifiedEmail() ? '⏳' : '📧' }}</span>
        </div>

        <h1 class="font-alumni text-h3 text-dark mb-3">
            {{ __('app.pending_approval_title') }}
        </h1>
        <p class="font-outfit text-body-lg text-gray-500 mb-6">
            {{ __('app.pending_approval_message') }}
        </p>

        {{-- Info Card --}}
        <div class="bg-light rounded-xl p-4 mb-8 text-left space-y-2">
            <p class="font-outfit text-body-lg text-dark">
                🏢 <span class="font-semibold">{{ auth()->user()->company_name }}</span>
            </p>
            <p class="font-outfit text-body-lg text-dark">
                📧 <span class="font-semibold">{{ auth()->user()->email }}</span>
            </p>
            <p class="font-outfit text-body-lg text-dark">
                🪪 <span class="font-semibold">{{ auth()->user()->cif }}</span>
            </p>
        </div>

        {{-- Steps --}}
        @php $verified = auth()->user()?->hasVerifiedEmail(); @endphp
        <div class="text-left space-y-3 mb-8">
            <div class="flex items-start gap-3">
                <span class="w-6 h-6 rounded-full flex items-center justify-center
                             font-alumni text-body-sm flex-shrink-0 mt-0.5
                             {{ $verified ? 'bg-green-500 text-white' : 'bg-primary text-white' }}">
                    {{ $verified ? '✓' : '1' }}
                </span>
                <p class="font-outfit text-body-lg {{ $verified ? 'text-green-600 line-through' : 'text-gray-600' }}">
                    {{ __('app.pending_step_email') }}
                </p>
            </div>
            <div class="flex items-start gap-3">
                <span class="w-6 h-6 rounded-full flex items-center justify-center
                             font-alumni text-body-sm flex-shrink-0 mt-0.5
                             {{ $verified ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500' }}">2</span>
                <p class="font-outfit text-body-lg {{ $verified ? 'text-gray-600' : 'text-gray-400' }}">
                    {{ __('app.pending_step_1') }}
                </p>
            </div>
            <div class="flex items-start gap-3">
                <span class="w-6 h-6 bg-gray-200 text-gray-500 rounded-full flex items-center
                             justify-center font-alumni text-body-sm flex-shrink-0 mt-0.5">3</span>
                <p class="font-outfit text-body-lg text-gray-400">
                    {{ __('app.pending_step_2') }}
                </p>
            </div>
            <div class="flex items-start gap-3">
                <span class="w-6 h-6 bg-gray-200 text-gray-500 rounded-full flex items-center
                             justify-center font-alumni text-body-sm flex-shrink-0 mt-0.5">4</span>
                <p class="font-outfit text-body-lg text-gray-400">
                    {{ __('app.pending_step_3') }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full border-2 border-primary text-primary font-alumni
                           text-sm-header py-3 rounded-lg hover:bg-primary
                           hover:text-white transition-all">
                {{ __('app.logout') }}
            </button>
        </form>

    </div>
</div>
@endsection
