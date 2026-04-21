@extends('layouts.app')
@section('title', __('app.saved_addresses'))

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-20" x-data="{ showForm: false, editing: null }">

    <div class="mb-10 flex items-end justify-between">
        <div>
            <a href="{{ route('profile.edit') }}"
               class="font-outfit text-xs text-gray-400 hover:text-primary transition-colors mb-3 inline-flex items-center gap-1">
                ← {{ __('app.profile') }}
            </a>
            <h1 class="font-alumni text-h1 text-dark leading-tight">
                {{ __('app.saved_addresses') }}<span class="text-secondary">.</span>
            </h1>
        </div>
        <button @click="showForm = !showForm"
                class="font-alumni text-sm-header bg-primary text-white px-6 py-3 rounded-2xl
                       hover:brightness-110 active:scale-95 transition-all">
            + {{ __('app.add_address') }}
        </button>
    </div>

    @if(session('success'))
        <div class="mb-6 px-5 py-4 bg-green-50 border border-green-200 rounded-2xl font-outfit text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Add Address Form --}}
    <div x-show="showForm" x-cloak
         class="bg-white rounded-3xl shadow-sm p-6 mb-8 border-2 border-primary/20">
        <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-5">
            {{ __('app.add_address') }}
        </p>
        <form method="POST" action="{{ route('addresses.store') }}">
            @csrf
            @include('shop.addresses._form')
            <div class="mt-5 flex gap-3 justify-end">
                <button type="button" @click="showForm = false"
                        class="font-alumni text-sm-header border border-gray-200 text-gray-600
                               px-6 py-3 rounded-2xl hover:bg-gray-50 transition-colors">
                    {{ __('app.cancel') }}
                </button>
                <button type="submit"
                        class="font-alumni text-sm-header bg-primary text-white px-8 py-3 rounded-2xl
                               hover:brightness-110 active:scale-95 transition-all">
                    {{ __('app.save') }}
                </button>
            </div>
        </form>
    </div>

    {{-- Address List --}}
    @if($addresses->isEmpty())
        <div class="text-center py-20 bg-white rounded-3xl shadow-sm">
            <p class="text-5xl mb-4">📍</p>
            <p class="font-alumni text-h5 text-dark">{{ __('app.no_saved_addresses') }}</p>
            <p class="font-outfit text-sm text-gray-400 mt-2">{{ __('app.no_saved_addresses_hint') }}</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($addresses as $addr)
                <div class="bg-white rounded-3xl shadow-sm overflow-hidden"
                     x-data="{ open: false }">
                    <div class="p-6 flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-1">
                                <p class="font-alumni text-h6 text-dark">{{ $addr->label }}</p>
                                @if($addr->is_default)
                                    <span class="font-outfit text-xs bg-primary/10 text-primary
                                                 px-2 py-0.5 rounded-full">
                                        {{ __('app.default') }}
                                    </span>
                                @endif
                            </div>
                            @if($addr->contact_name)
                                <p class="font-outfit text-sm text-gray-600">{{ $addr->contact_name }}</p>
                            @endif
                            <p class="font-outfit text-sm text-gray-500">
                                {{ $addr->address }}, {{ $addr->city }} {{ $addr->postal_code }} · {{ $addr->country }}
                            </p>
                            @if($addr->phone)
                                <p class="font-outfit text-xs text-gray-400 mt-0.5">{{ $addr->phone }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            @unless($addr->is_default)
                                <form method="POST" action="{{ route('addresses.default', $addr->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="font-outfit text-xs text-gray-400 hover:text-primary transition-colors px-3 py-2 rounded-xl hover:bg-light">
                                        {{ __('app.set_default') }}
                                    </button>
                                </form>
                            @endunless
                            <button @click="open = !open"
                                    class="font-outfit text-xs text-primary hover:text-primary/70 transition-colors px-3 py-2 rounded-xl hover:bg-light">
                                {{ __('app.edit') }}
                            </button>
                            <form method="POST" action="{{ route('addresses.destroy', $addr->id) }}">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('{{ __('app.confirm_delete_address') }}')"
                                        class="font-outfit text-xs text-red-400 hover:text-red-600 transition-colors px-3 py-2 rounded-xl hover:bg-red-50">
                                    {{ __('app.delete') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Edit form (collapsible) --}}
                    <div x-show="open" x-cloak
                         class="border-t border-gray-100 px-6 pb-6 pt-5 bg-light/50">
                        <form method="POST" action="{{ route('addresses.update', $addr->id) }}">
                            @csrf @method('PUT')
                            @include('shop.addresses._form', ['address' => $addr])
                            <div class="mt-5 flex gap-3 justify-end">
                                <button type="button" @click="open = false"
                                        class="font-alumni text-sm-header border border-gray-200 text-gray-600
                                               px-6 py-3 rounded-2xl hover:bg-gray-50 transition-colors">
                                    {{ __('app.cancel') }}
                                </button>
                                <button type="submit"
                                        class="font-alumni text-sm-header bg-primary text-white px-8 py-3 rounded-2xl
                                               hover:brightness-110 active:scale-95 transition-all">
                                    {{ __('app.save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
