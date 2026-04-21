@extends('layouts.guest')
@section('title', __('app.reset_password_title'))

@section('content')
<div class="min-h-screen bg-light flex flex-col items-center justify-center px-4 py-12">

    {{-- Hero --}}
    <div class="text-center mb-8">
        <a href="{{ route('home') }}">
            <img src="{{ asset('assets/images/logo/' . rawurlencode('FULL LOGO (Red Sun).svg')) }}"
                 alt="Copyus" class="h-12 mx-auto mb-6">
        </a>
        <h1 class="font-alumni text-h2 text-dark leading-tight">
            {{ __('app.reset_password_title_1') }}
            <span class="text-secondary">{{ __('app.reset_password_title_2') }}</span>
        </h1>
        <p class="font-alumni text-h5 text-primary mt-3">
            {{ __('app.reset_password_subtitle') }}
        </p>
    </div>

    {{-- Card --}}
    <div class="w-full max-w-md bg-white rounded-3xl shadow-sm p-8">

        @if($errors->any())
            <div class="mb-6 px-5 py-4 bg-red-50 border border-red-200 rounded-2xl">
                @foreach($errors->all() as $error)
                    <p class="font-outfit text-sm text-red-600">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label class="block font-outfit text-xs font-medium text-gray-600 mb-1">
                    {{ __('app.email') }}
                </label>
                <input type="email" name="email" value="{{ old('email', request()->email) }}"
                       required autofocus autocomplete="username"
                       class="w-full font-outfit text-sm border border-gray-200 rounded-2xl px-4 py-3
                              focus:outline-none focus:border-primary transition-colors
                              @error('email') border-red-400 @enderror">
                @error('email')
                    <p class="font-outfit text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block font-outfit text-xs font-medium text-gray-600 mb-1">
                    {{ __('app.new_password') }}
                </label>
                <input type="password" name="password"
                       required autocomplete="new-password"
                       class="w-full font-outfit text-sm border border-gray-200 rounded-2xl px-4 py-3
                              focus:outline-none focus:border-primary transition-colors
                              @error('password') border-red-400 @enderror">
                @error('password')
                    <p class="font-outfit text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block font-outfit text-xs font-medium text-gray-600 mb-1">
                    {{ __('app.confirm_new_password') }}
                </label>
                <input type="password" name="password_confirmation"
                       required autocomplete="new-password"
                       class="w-full font-outfit text-sm border border-gray-200 rounded-2xl px-4 py-3
                              focus:outline-none focus:border-primary transition-colors">
            </div>

            <button type="submit"
                    class="w-full bg-primary text-white font-alumni text-sm-header py-4 rounded-2xl
                           hover:brightness-110 active:scale-[0.98] transition-all">
                {{ __('app.reset_password_btn') }}
            </button>
        </form>

    </div>

</div>
@endsection
