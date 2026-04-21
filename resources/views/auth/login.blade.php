@extends('layouts.guest')
@section('title', __('app.login'))

@section('content')
<div class="min-h-screen bg-light flex flex-col items-center justify-center px-4 py-12">

    {{-- Hero --}}
    <div class="text-center mb-8">
        <a href="{{ route('home') }}">
            <img src="{{ asset('assets/images/logo/' . rawurlencode('FULL LOGO (Red Sun).svg')) }}"
                 alt="Copyus" class="h-12 mx-auto mb-6">
        </a>
        <h1 class="font-alumni text-h2 text-dark leading-tight">
            {{ __('app.login_title_1') }}
            <span class="text-secondary">{{ __('app.login_title_2') }}</span>
        </h1>
        <p class="font-alumni text-h5 text-primary mt-3">
            {{ __('app.login_subtitle') }}
        </p>
    </div>

    {{-- Card --}}
    <div class="w-full max-w-md bg-white rounded-3xl shadow-sm p-8">

        @if(session('success'))
            <div class="mb-5 bg-green-50 border border-green-200 text-green-700
                        font-outfit text-sm px-4 py-3 rounded-2xl">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-5 bg-red-50 border border-red-200 text-red-600
                        font-outfit text-sm px-4 py-3 rounded-2xl">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block font-outfit text-xs font-semibold tracking-widest
                               text-primary uppercase mb-2">
                    {{ __('app.email') }}
                </label>
                <input type="email" name="email" value="{{ old('email') }}"
                       autocomplete="email" required
                       placeholder="correu@exemple.com"
                       class="w-full bg-light border-0 rounded-2xl px-5 py-4
                              font-outfit text-sm text-dark placeholder:text-gray-400
                              focus:outline-none focus:ring-2 focus:ring-primary/30 transition
                              @error('email') ring-2 ring-red-300 @enderror" />
            </div>

            <div>
                <label class="block font-outfit text-xs font-semibold tracking-widest
                               text-primary uppercase mb-2">
                    {{ __('app.password') }}
                </label>
                <input type="password" name="password"
                       autocomplete="current-password" required
                       placeholder="••••••••"
                       class="w-full bg-light border-0 rounded-2xl px-5 py-4
                              font-outfit text-sm text-dark placeholder:text-gray-400
                              focus:outline-none focus:ring-2 focus:ring-primary/30 transition
                              @error('password') ring-2 ring-red-300 @enderror" />
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember"
                           class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary accent-primary" />
                    <span class="font-outfit text-xs text-gray-500">{{ __('app.remember_me') }}</span>
                </label>
            </div>

            <button type="submit"
                    class="w-full bg-primary text-white font-alumni text-sm-header
                           py-4 rounded-2xl hover:brightness-110 active:scale-95
                           transition-all duration-200 mt-2">
                {{ __('app.login') }} →
            </button>
        </form>

        <p class="text-center font-outfit text-xs text-gray-400 mt-6">
            {{ __('app.no_account') }}
            <a href="{{ route('register') }}"
               class="text-secondary hover:text-primary transition-colors font-semibold ml-1">
                {{ __('app.register') }} →
            </a>
        </p>
    </div>
</div>
@endsection
