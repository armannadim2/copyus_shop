@extends('layouts.guest')
@section('title', __('app.register'))

@section('content')
<div class="min-h-screen bg-light flex flex-col items-center justify-center px-4 py-12">

    {{-- Hero --}}
    <div class="text-center mb-8">
        <a href="{{ route('home') }}">
            <img src="{{ asset('assets/images/logo/' . rawurlencode('FULL LOGO (Red Sun).svg')) }}"
                 alt="Copyus" class="h-12 mx-auto mb-6">
        </a>
        <h1 class="font-alumni text-h2 text-dark leading-tight">
            {{ __('app.register_title_1') }}
            <span class="text-secondary">{{ __('app.register_title_2') }}</span>
        </h1>
        <p class="font-alumni text-h5 text-primary mt-3 max-w-md mx-auto">
            {{ __('app.register_subtitle') }}
        </p>
    </div>

    {{-- Card --}}
    <div class="w-full max-w-2xl bg-white rounded-3xl shadow-sm p-8">

        @if($errors->any())
            <div class="mb-5 bg-red-50 border border-red-200 text-red-600
                        font-outfit text-sm px-4 py-3 rounded-2xl">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-8">
            @csrf

            {{-- Personal Info --}}
            <div>
                <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-5">
                    {{ __('app.personal_info') }}
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>
                        <label class="block font-outfit text-xs font-semibold tracking-widest
                                       text-primary uppercase mb-2">
                            {{ __('app.name') }} <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full bg-light border-0 rounded-2xl px-5 py-4
                                      font-outfit text-sm text-dark placeholder:text-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-primary/30 transition
                                      @error('name') ring-2 ring-red-300 @enderror" />
                    </div>

                    <div>
                        <label class="block font-outfit text-xs font-semibold tracking-widest
                                       text-primary uppercase mb-2">
                            {{ __('app.email') }} <span class="text-red-400">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full bg-light border-0 rounded-2xl px-5 py-4
                                      font-outfit text-sm text-dark placeholder:text-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-primary/30 transition
                                      @error('email') ring-2 ring-red-300 @enderror" />
                    </div>

                    <div>
                        <label class="block font-outfit text-xs font-semibold tracking-widest
                                       text-primary uppercase mb-2">
                            {{ __('app.password') }} <span class="text-red-400">*</span>
                        </label>
                        <input type="password" name="password" required autocomplete="new-password"
                               class="w-full bg-light border-0 rounded-2xl px-5 py-4
                                      font-outfit text-sm text-dark placeholder:text-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-primary/30 transition
                                      @error('password') ring-2 ring-red-300 @enderror" />
                    </div>

                    <div>
                        <label class="block font-outfit text-xs font-semibold tracking-widest
                                       text-primary uppercase mb-2">
                            {{ __('app.confirm_password') }} <span class="text-red-400">*</span>
                        </label>
                        <input type="password" name="password_confirmation" required autocomplete="new-password"
                               class="w-full bg-light border-0 rounded-2xl px-5 py-4
                                      font-outfit text-sm text-dark placeholder:text-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-primary/30 transition" />
                    </div>
                </div>
            </div>

            {{-- Company Info --}}
            <div x-data="{
                requireInvoice: {{ old('requires_invoice') ? 'true' : 'false' }},
                hasCompany:     {{ old('company_name') ? 'true' : 'false' }},
                get needsCif() { return this.requireInvoice || this.hasCompany }
            }">
                <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-5">
                    {{ __('app.company_info') }}
                </p>

                <label class="flex items-start gap-3 mb-5 cursor-pointer group
                               bg-light rounded-2xl p-4">
                    <input type="checkbox" name="requires_invoice" value="1"
                           x-model="requireInvoice"
                           {{ old('requires_invoice') ? 'checked' : '' }}
                           class="mt-0.5 w-4 h-4 rounded border-gray-300 text-primary
                                  focus:ring-primary accent-primary" />
                    <div>
                        <span class="font-outfit text-sm font-medium text-dark group-hover:text-primary transition-colors">
                            {{ __('app.requires_business_invoice') }}
                        </span>
                        <p class="font-outfit text-xs text-gray-400 mt-0.5">
                            {{ __('app.requires_business_invoice_hint') }}
                        </p>
                    </div>
                </label>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>
                        <label class="block font-outfit text-xs font-semibold tracking-widest
                                       text-primary uppercase mb-2">
                            {{ __('app.company_name') }}
                            <span class="normal-case tracking-normal font-normal text-gray-400">({{ __('app.optional') }})</span>
                        </label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}"
                               x-on:input="hasCompany = $event.target.value.trim().length > 0"
                               class="w-full bg-light border-0 rounded-2xl px-5 py-4
                                      font-outfit text-sm text-dark placeholder:text-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-primary/30 transition" />
                    </div>

                    <div>
                        <label class="block font-outfit text-xs font-semibold tracking-widest
                                       text-primary uppercase mb-2">
                            NIF / CIF / VAT
                            <span x-show="needsCif" class="text-red-400">*</span>
                            <span x-show="!needsCif"
                                  class="normal-case tracking-normal font-normal text-gray-400">
                                ({{ __('app.optional') }})
                            </span>
                        </label>
                        <input type="text" name="cif" value="{{ old('cif') }}"
                               :required="needsCif"
                               placeholder="B12345678 · 12345678Z · X1234567L"
                               class="w-full bg-light border-0 rounded-2xl px-5 py-4
                                      font-outfit text-sm text-dark placeholder:text-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-primary/30 transition
                                      @error('cif') ring-2 ring-red-300 @enderror" />
                        @error('cif')
                            <p class="mt-1 font-outfit text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block font-outfit text-xs font-semibold tracking-widest
                                       text-primary uppercase mb-2">
                            {{ __('app.phone') }}
                        </label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               class="w-full bg-light border-0 rounded-2xl px-5 py-4
                                      font-outfit text-sm text-dark placeholder:text-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-primary/30 transition" />
                    </div>

                    <div>
                        <label class="block font-outfit text-xs font-semibold tracking-widest
                                       text-primary uppercase mb-2">
                            {{ __('app.address') }}
                        </label>
                        <input type="text" name="address" value="{{ old('address') }}"
                               class="w-full bg-light border-0 rounded-2xl px-5 py-4
                                      font-outfit text-sm text-dark placeholder:text-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-primary/30 transition" />
                    </div>

                    <div>
                        <label class="block font-outfit text-xs font-semibold tracking-widest
                                       text-primary uppercase mb-2">
                            {{ __('app.city') }}
                        </label>
                        <input type="text" name="city" value="{{ old('city') }}"
                               class="w-full bg-light border-0 rounded-2xl px-5 py-4
                                      font-outfit text-sm text-dark placeholder:text-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-primary/30 transition" />
                    </div>

                    <div>
                        <label class="block font-outfit text-xs font-semibold tracking-widest
                                       text-primary uppercase mb-2">
                            {{ __('app.postal_code') }}
                        </label>
                        <input type="text" name="postal_code" value="{{ old('postal_code') }}"
                               class="w-full bg-light border-0 rounded-2xl px-5 py-4
                                      font-outfit text-sm text-dark placeholder:text-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-primary/30 transition" />
                    </div>

                    <div class="md:col-span-2">
                        <label class="block font-outfit text-xs font-semibold tracking-widest
                                       text-primary uppercase mb-2">
                            {{ __('app.country') }}
                        </label>
                        <select name="country"
                                class="w-full bg-light border-0 rounded-2xl px-5 py-4
                                       font-outfit text-sm text-dark
                                       focus:outline-none focus:ring-2 focus:ring-primary/30 transition">
                            <option value="ES" {{ old('country') === 'ES' ? 'selected' : '' }}>🇪🇸 España</option>
                            <option value="FR" {{ old('country') === 'FR' ? 'selected' : '' }}>🇫🇷 France</option>
                            <option value="PT" {{ old('country') === 'PT' ? 'selected' : '' }}>🇵🇹 Portugal</option>
                            <option value="DE" {{ old('country') === 'DE' ? 'selected' : '' }}>🇩🇪 Deutschland</option>
                            <option value="GB" {{ old('country') === 'GB' ? 'selected' : '' }}>🇬🇧 United Kingdom</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-primary text-white font-alumni text-sm-header
                           py-4 rounded-2xl hover:brightness-110 active:scale-95
                           transition-all duration-200">
                {{ __('app.create_account') }} →
            </button>
        </form>
    </div>

    <p class="text-center font-outfit text-xs text-gray-400 mt-6">
        {{ __('app.already_have_account') }}
        <a href="{{ route('login') }}"
           class="text-secondary hover:text-primary transition-colors font-semibold ml-1">
            {{ __('app.login') }} →
        </a>
    </p>
</div>
@endsection
