@extends('layouts.app')
@section('title', __('app.register'))

@section('content')
<div class="min-h-screen bg-light flex items-center justify-center py-12 px-4">
    <div class="max-w-2xl w-full bg-white rounded-2xl shadow-lg p-8">

        {{-- Header --}}
        <div class="text-center mb-8">
            <span class="font-alumni text-h3 text-dark font-bold">
                COPY<span class="text-primary">US</span>
            </span>
            <h1 class="font-alumni text-h4 text-dark mt-2">
                {{ __('app.register') }} B2B
            </h1>
            <p class="font-outfit text-body-lg text-gray-500 mt-2">
                {{ __('app.register_subtitle') }}
            </p>
        </div>

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-primary rounded-lg p-4 mb-6">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li class="font-outfit text-body-md text-red-700">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('b2b.register.store') }}" class="space-y-6">
            @csrf

            {{-- Section: Company Info --}}
            <div>
                <h2 class="font-alumni text-h6 text-dark border-b border-gray-200 pb-2 mb-4">
                    🏢 {{ __('app.company_information') }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Company Name --}}
                    <div class="md:col-span-2">
                        <label class="block font-alumni text-sm-header text-dark mb-1">
                            {{ __('app.company_name') }} <span class="text-primary">*</span>
                        </label>
                        <input type="text" name="company_name"
                               value="{{ old('company_name') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2
                                      font-outfit text-body-lg focus:outline-none
                                      focus:ring-2 focus:ring-primary focus:border-transparent
                                      @error('company_name') border-red-400 @enderror"
                               placeholder="Empresa S.L." required />
                        @error('company_name')
                            <p class="text-primary font-outfit text-body-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- CIF --}}
                    <div>
                        <label class="block font-alumni text-sm-header text-dark mb-1">
                            {{ __('app.cif') }} <span class="text-primary">*</span>
                        </label>
                        <input type="text" name="cif"
                               value="{{ old('cif') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2
                                      font-outfit text-body-lg focus:outline-none
                                      focus:ring-2 focus:ring-primary focus:border-transparent
                                      @error('cif') border-red-400 @enderror"
                               placeholder="B12345678" required />
                        @error('cif')
                            <p class="text-primary font-outfit text-body-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="block font-alumni text-sm-header text-dark mb-1">
                            {{ __('app.phone') }} <span class="text-primary">*</span>
                        </label>
                        <input type="tel" name="phone"
                               value="{{ old('phone') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2
                                      font-outfit text-body-lg focus:outline-none
                                      focus:ring-2 focus:ring-primary focus:border-transparent
                                      @error('phone') border-red-400 @enderror"
                               placeholder="+34 600 000 000" required />
                        @error('phone')
                            <p class="text-primary font-outfit text-body-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- Section: Address --}}
            <div>
                <h2 class="font-alumni text-h6 text-dark border-b border-gray-200 pb-2 mb-4">
                    📍 {{ __('app.address') }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Address --}}
                    <div class="md:col-span-2">
                        <label class="block font-alumni text-sm-header text-dark mb-1">
                            {{ __('app.address') }} <span class="text-primary">*</span>
                        </label>
                        <input type="text" name="address"
                               value="{{ old('address') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2
                                      font-outfit text-body-lg focus:outline-none
                                      focus:ring-2 focus:ring-primary focus:border-transparent
                                      @error('address') border-red-400 @enderror"
                               placeholder="Carrer de l'Exemple, 123" required />
                        @error('address')
                            <p class="text-primary font-outfit text-body-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- City --}}
                    <div>
                        <label class="block font-alumni text-sm-header text-dark mb-1">
                            {{ __('app.city') }} <span class="text-primary">*</span>
                        </label>
                        <input type="text" name="city"
                               value="{{ old('city') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2
                                      font-outfit text-body-lg focus:outline-none
                                      focus:ring-2 focus:ring-primary focus:border-transparent
                                      @error('city') border-red-400 @enderror"
                               placeholder="Barcelona" required />
                        @error('city')
                            <p class="text-primary font-outfit text-body-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Postal Code --}}
                    <div>
                        <label class="block font-alumni text-sm-header text-dark mb-1">
                            {{ __('app.postal_code') }} <span class="text-primary">*</span>
                        </label>
                        <input type="text" name="postal_code"
                               value="{{ old('postal_code') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2
                                      font-outfit text-body-lg focus:outline-none
                                      focus:ring-2 focus:ring-primary focus:border-transparent
                                      @error('postal_code') border-red-400 @enderror"
                               placeholder="08001" required />
                        @error('postal_code')
                            <p class="text-primary font-outfit text-body-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Country --}}
                    <div>
                        <label class="block font-alumni text-sm-header text-dark mb-1">
                            {{ __('app.country') }} <span class="text-primary">*</span>
                        </label>
                        <select name="country"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2
                                       font-outfit text-body-lg focus:outline-none
                                       focus:ring-2 focus:ring-primary focus:border-transparent
                                       @error('country') border-red-400 @enderror">
                            <option value="ES" {{ old('country','ES') === 'ES' ? 'selected' : '' }}>🇪🇸 España</option>
                            <option value="FR" {{ old('country') === 'FR' ? 'selected' : '' }}>🇫🇷 France</option>
                            <option value="PT" {{ old('country') === 'PT' ? 'selected' : '' }}>🇵🇹 Portugal</option>
                            <option value="DE" {{ old('country') === 'DE' ? 'selected' : '' }}>🇩🇪 Deutschland</option>
                            <option value="GB" {{ old('country') === 'GB' ? 'selected' : '' }}>🇬🇧 United Kingdom</option>
                        </select>
                        @error('country')
                            <p class="text-primary font-outfit text-body-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- Section: Contact Person --}}
            <div>
                <h2 class="font-alumni text-h6 text-dark border-b border-gray-200 pb-2 mb-4">
                    👤 {{ __('app.contact_person') }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Full Name --}}
                    <div>
                        <label class="block font-alumni text-sm-header text-dark mb-1">
                            {{ __('app.full_name') }} <span class="text-primary">*</span>
                        </label>
                        <input type="text" name="name"
                               value="{{ old('name') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2
                                      font-outfit text-body-lg focus:outline-none
                                      focus:ring-2 focus:ring-primary focus:border-transparent
                                      @error('name') border-red-400 @enderror"
                               placeholder="Joan Garcia" required />
                        @error('name')
                            <p class="text-primary font-outfit text-body-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block font-alumni text-sm-header text-dark mb-1">
                            {{ __('app.email') }} <span class="text-primary">*</span>
                        </label>
                        <input type="email" name="email"
                               value="{{ old('email') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2
                                      font-outfit text-body-lg focus:outline-none
                                      focus:ring-2 focus:ring-primary focus:border-transparent
                                      @error('email') border-red-400 @enderror"
                               placeholder="joan@empresa.com" required />
                        @error('email')
                            <p class="text-primary font-outfit text-body-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- Section: Password --}}
            <div>
                <h2 class="font-alumni text-h6 text-dark border-b border-gray-200 pb-2 mb-4">
                    🔐 {{ __('app.password') }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Password --}}
                    <div>
                        <label class="block font-alumni text-sm-header text-dark mb-1">
                            {{ __('app.password') }} <span class="text-primary">*</span>
                        </label>
                        <input type="password" name="password"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2
                                      font-outfit text-body-lg focus:outline-none
                                      focus:ring-2 focus:ring-primary focus:border-transparent
                                      @error('password') border-red-400 @enderror"
                               required />
                        @error('password')
                            <p class="text-primary font-outfit text-body-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label class="block font-alumni text-sm-header text-dark mb-1">
                            {{ __('app.confirm_password') }} <span class="text-primary">*</span>
                        </label>
                        <input type="password" name="password_confirmation"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2
                                      font-outfit text-body-lg focus:outline-none
                                      focus:ring-2 focus:ring-primary focus:border-transparent"
                               required />
                    </div>

                </div>
            </div>

            {{-- Terms --}}
            <div class="flex items-start gap-3">
                <input type="checkbox" name="terms" id="terms"
                       class="mt-1 w-4 h-4 accent-primary" required />
                <label for="terms" class="font-outfit text-body-lg text-gray-600">
                    {{ __('app.accept_terms_prefix') }}
                    <a href="#" class="text-primary underline hover:opacity-80">
                        {{ __('app.terms_and_conditions') }}
                    </a>
                    {{ __('app.accept_terms_suffix') }}
                </label>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full bg-primary text-white font-alumni text-sm-header
                           py-3 rounded-lg hover:bg-opacity-90 transition-all
                           focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                {{ __('app.register') }}
            </button>

            {{-- Login Link --}}
            <p class="text-center font-outfit text-body-lg text-gray-500">
                {{ __('app.already_have_account') }}
                <a href="{{ route('login') }}" class="text-primary underline hover:opacity-80">
                    {{ __('app.login') }}
                </a>
            </p>

        </form>
    </div>
</div>
@endsection
