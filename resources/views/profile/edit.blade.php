@extends('layouts.app')
@section('title', __('app.profile'))

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-20">

    {{-- Header --}}
    <div class="mb-10">
        <h1 class="font-alumni text-h1 text-dark leading-tight">
            {{ __('app.profile') }}<span class="text-secondary">.</span>
        </h1>
        <p class="font-outfit text-sm text-gray-400 mt-1">{{ $user->email }}</p>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-6 px-5 py-4 bg-green-50 border border-green-200 rounded-2xl font-outfit text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Personal & Company Info ──────────────────────────────── --}}
    <div class="bg-white rounded-3xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-gray-100">
            <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase">{{ __('app.personal_info') }}</p>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label class="block font-outfit text-xs font-medium text-gray-600 mb-1">{{ __('app.full_name') }}</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                               class="w-full font-outfit text-sm border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-primary transition-colors @error('name') border-red-400 @enderror">
                        @error('name')<p class="font-outfit text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block font-outfit text-xs font-medium text-gray-600 mb-1">{{ __('app.email') }}</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                               class="w-full font-outfit text-sm border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-primary transition-colors @error('email') border-red-400 @enderror">
                        @error('email')<p class="font-outfit text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block font-outfit text-xs font-medium text-gray-600 mb-1">{{ __('app.phone') }}</label>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                               class="w-full font-outfit text-sm border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-primary transition-colors">
                    </div>

                </div>

                {{-- Company section --}}
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-4">{{ __('app.company_information') }}</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div>
                            <label class="block font-outfit text-xs font-medium text-gray-600 mb-1">{{ __('app.company_name') }}</label>
                            <input type="text" name="company_name" value="{{ old('company_name', $user->company_name) }}"
                                   class="w-full font-outfit text-sm border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-primary transition-colors">
                        </div>

                        <div>
                            <label class="block font-outfit text-xs font-medium text-gray-600 mb-1">{{ __('app.cif') }}</label>
                            <input type="text" name="cif" value="{{ old('cif', $user->cif) }}"
                                   class="w-full font-outfit text-sm border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-primary transition-colors">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block font-outfit text-xs font-medium text-gray-600 mb-1">{{ __('app.address') }}</label>
                            <input type="text" name="address" value="{{ old('address', $user->address) }}"
                                   class="w-full font-outfit text-sm border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-primary transition-colors">
                        </div>

                        <div>
                            <label class="block font-outfit text-xs font-medium text-gray-600 mb-1">{{ __('app.city') }}</label>
                            <input type="text" name="city" value="{{ old('city', $user->city) }}"
                                   class="w-full font-outfit text-sm border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-primary transition-colors">
                        </div>

                        <div>
                            <label class="block font-outfit text-xs font-medium text-gray-600 mb-1">{{ __('app.postal_code') }}</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}"
                                   class="w-full font-outfit text-sm border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-primary transition-colors">
                        </div>

                        <div>
                            <label class="block font-outfit text-xs font-medium text-gray-600 mb-1">{{ __('app.country') }}</label>
                            <input type="text" name="country" value="{{ old('country', $user->country) }}"
                                   maxlength="2" placeholder="ES"
                                   class="w-full font-outfit text-sm border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-primary transition-colors uppercase">
                        </div>

                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                            class="font-alumni text-sm-header bg-primary text-white px-8 py-3 rounded-2xl hover:brightness-110 active:scale-95 transition-all">
                        {{ __('app.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Change Password ───────────────────────────────────────── --}}
    <div class="bg-white rounded-3xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-gray-100">
            <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase">{{ __('app.change_password') }}</p>
        </div>
        <div class="p-6">

            @if($errors->updatePassword->any())
                <div class="mb-4 px-5 py-4 bg-red-50 border border-red-200 rounded-2xl">
                    @foreach($errors->updatePassword->all() as $error)
                        <p class="font-outfit text-xs text-red-600">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('profile.password') }}">
                @csrf
                @method('PATCH')

                <div class="space-y-4">
                    <div>
                        <label class="block font-outfit text-xs font-medium text-gray-600 mb-1">{{ __('app.current_password') }}</label>
                        <input type="password" name="current_password" autocomplete="current-password"
                               class="w-full font-outfit text-sm border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-primary transition-colors">
                    </div>
                    <div>
                        <label class="block font-outfit text-xs font-medium text-gray-600 mb-1">{{ __('app.new_password') }}</label>
                        <input type="password" name="password" autocomplete="new-password"
                               class="w-full font-outfit text-sm border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-primary transition-colors">
                    </div>
                    <div>
                        <label class="block font-outfit text-xs font-medium text-gray-600 mb-1">{{ __('app.confirm_new_password') }}</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password"
                               class="w-full font-outfit text-sm border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-primary transition-colors">
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                            class="font-alumni text-sm-header bg-secondary text-white px-8 py-3 rounded-2xl hover:brightness-110 active:scale-95 transition-all">
                        {{ __('app.change_password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Saved Addresses ─────────────────────────────────────────── --}}
    <div class="bg-white rounded-3xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase">{{ __('app.saved_addresses') }}</p>
            <a href="{{ route('addresses.index') }}"
               class="font-outfit text-xs text-primary hover:underline">
                {{ __('app.manage') }} →
            </a>
        </div>
        <div class="p-6">
            <p class="font-outfit text-sm text-gray-500">{{ __('app.saved_addresses_profile_hint') }}</p>
        </div>
    </div>

    {{-- ── Wishlist ────────────────────────────────────────────── --}}
    <div class="bg-white rounded-3xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase">{{ __('app.wishlist') }}</p>
            <a href="{{ route('wishlist.index') }}"
               class="font-outfit text-xs text-primary hover:underline">
                {{ __('app.view_wishlist') }} →
            </a>
        </div>
        <div class="p-6">
            <p class="font-outfit text-sm text-gray-500">{{ __('app.wishlist_profile_hint') }}</p>
        </div>
    </div>

    {{-- ── Delete Account ────────────────────────────────────────── --}}
    <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-red-100" x-data="{ open: false }">
        <div class="px-6 py-5 border-b border-red-100">
            <p class="font-outfit text-xs font-semibold tracking-widest text-red-500 uppercase">{{ __('app.delete_account') }}</p>
        </div>
        <div class="p-6">
            <p class="font-outfit text-sm text-gray-500 mb-4">{{ __('app.delete_account_warning') }}</p>

            <button @click="open = true"
                    class="font-alumni text-sm-header border border-red-300 text-red-600 px-6 py-3 rounded-2xl hover:bg-red-50 transition-colors">
                {{ __('app.delete_account') }}
            </button>

            <div x-show="open" x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
                 @keydown.escape.window="open = false">
                <div class="bg-white rounded-3xl shadow-xl max-w-md w-full mx-4 p-8" @click.stop>

                    <h3 class="font-alumni text-h4 text-dark mb-3">{{ __('app.delete_account') }}</h3>
                    <p class="font-outfit text-sm text-gray-500 mb-6">{{ __('app.delete_account_confirm') }}</p>

                    @if($errors->userDeletion->any())
                        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-2xl">
                            @foreach($errors->userDeletion->all() as $error)
                                <p class="font-outfit text-xs text-red-600">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.destroy') }}">
                        @csrf
                        @method('DELETE')
                        <div class="mb-6">
                            <label class="block font-outfit text-xs font-medium text-gray-600 mb-1">{{ __('app.password') }}</label>
                            <input type="password" name="password" autocomplete="current-password"
                                   class="w-full font-outfit text-sm border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-red-400 transition-colors">
                        </div>
                        <div class="flex gap-3 justify-end">
                            <button type="button" @click="open = false"
                                    class="font-alumni text-sm-header border border-gray-200 text-gray-600 px-6 py-3 rounded-2xl hover:bg-gray-50 transition-colors">
                                {{ __('app.cancel') }}
                            </button>
                            <button type="submit"
                                    class="font-alumni text-sm-header bg-red-500 text-white px-6 py-3 rounded-2xl hover:brightness-110 active:scale-95 transition-all">
                                {{ __('app.delete_account') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
