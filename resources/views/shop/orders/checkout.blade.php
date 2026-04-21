@extends('layouts.app')
@section('title', __('app.checkout'))

@section('content')

{{-- Hero --}}
<div class="text-center pt-16 pb-12 px-4">
    <h1 class="font-alumni text-h1 text-dark leading-tight mb-4">
        {{ __('app.checkout_title_1') }}
        <span class="text-secondary">{{ __('app.checkout_title_2') }}</span>
    </h1>
    <p class="font-alumni text-h5 text-primary max-w-xl mx-auto">
        {{ __('app.checkout_subtitle') }}
    </p>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 pb-20">
    <form method="POST" action="{{ route('orders.place') }}">
        @csrf
        <div class="flex flex-col lg:flex-row gap-6">

            {{-- Left --}}
            <div class="flex-1 space-y-5">

                {{-- Shipping --}}
                <div class="bg-white rounded-3xl shadow-sm p-8"
                     x-data="{
                        fill(addr) {
                            document.querySelector('[name=shipping_address]').value  = addr.address;
                            document.querySelector('[name=shipping_city]').value     = addr.city;
                            document.querySelector('[name=shipping_postal]').value   = addr.postal_code;
                            document.querySelector('[name=shipping_contact]').value  = addr.contact_name ?? '';
                            document.querySelector('[name=shipping_phone]').value    = addr.phone ?? '';
                            let sel = document.querySelector('[name=shipping_country]');
                            if (sel) sel.value = addr.country;
                        }
                     }">
                    <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-4">
                        {{ __('app.shipping_address') }}
                    </p>

                    {{-- Saved addresses picker --}}
                    @if(isset($savedAddresses) && $savedAddresses->isNotEmpty())
                        <div class="mb-5">
                            <p class="font-outfit text-xs text-gray-400 mb-3">{{ __('app.use_saved_address') }}</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($savedAddresses as $saved)
                                    <button type="button"
                                            @click="fill({{ Js::from($saved->toShippingArray() + ['contact_name' => $saved->contact_name, 'phone' => $saved->phone]) }})"
                                            class="font-outfit text-xs px-4 py-2 rounded-xl border-2 transition-all
                                                   {{ $saved->is_default
                                                        ? 'border-primary text-primary bg-primary/5'
                                                        : 'border-gray-200 text-gray-600 hover:border-primary hover:text-primary' }}">
                                        {{ $saved->label }}
                                        @if($saved->is_default)
                                            <span class="text-primary/60">({{ __('app.default') }})</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="md:col-span-2">
                            <label class="block font-outfit text-xs font-semibold tracking-widest
                                           text-primary uppercase mb-2">
                                {{ __('app.address') }} <span class="text-red-400">*</span>
                            </label>
                            <input type="text" name="shipping_address"
                                   value="{{ old('shipping_address', $user->address) }}" required
                                   class="w-full bg-light border-0 rounded-2xl px-5 py-4
                                          font-outfit text-sm text-dark placeholder:text-gray-400
                                          focus:outline-none focus:ring-2 focus:ring-primary/30 transition
                                          @error('shipping_address') ring-2 ring-red-300 @enderror" />
                            @error('shipping_address')
                                <p class="text-red-500 font-outfit text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block font-outfit text-xs font-semibold tracking-widest
                                           text-primary uppercase mb-2">
                                {{ __('app.city') }} <span class="text-red-400">*</span>
                            </label>
                            <input type="text" name="shipping_city"
                                   value="{{ old('shipping_city', $user->city) }}" required
                                   class="w-full bg-light border-0 rounded-2xl px-5 py-4
                                          font-outfit text-sm text-dark placeholder:text-gray-400
                                          focus:outline-none focus:ring-2 focus:ring-primary/30 transition
                                          @error('shipping_city') ring-2 ring-red-300 @enderror" />
                        </div>

                        <div>
                            <label class="block font-outfit text-xs font-semibold tracking-widest
                                           text-primary uppercase mb-2">
                                {{ __('app.postal_code') }} <span class="text-red-400">*</span>
                            </label>
                            <input type="text" name="shipping_postal"
                                   value="{{ old('shipping_postal', $user->postal_code) }}" required
                                   class="w-full bg-light border-0 rounded-2xl px-5 py-4
                                          font-outfit text-sm text-dark placeholder:text-gray-400
                                          focus:outline-none focus:ring-2 focus:ring-primary/30 transition
                                          @error('shipping_postal') ring-2 ring-red-300 @enderror" />
                        </div>

                        <div>
                            <label class="block font-outfit text-xs font-semibold tracking-widest
                                           text-primary uppercase mb-2">
                                {{ __('app.country') }} <span class="text-red-400">*</span>
                            </label>
                            <select name="shipping_country"
                                    class="w-full bg-light border-0 rounded-2xl px-5 py-4
                                           font-outfit text-sm text-dark
                                           focus:outline-none focus:ring-2 focus:ring-primary/30 transition">
                                <option value="ES" {{ old('shipping_country', $user->country) === 'ES' ? 'selected' : '' }}>🇪🇸 España</option>
                                <option value="FR" {{ old('shipping_country', $user->country) === 'FR' ? 'selected' : '' }}>🇫🇷 France</option>
                                <option value="PT" {{ old('shipping_country', $user->country) === 'PT' ? 'selected' : '' }}>🇵🇹 Portugal</option>
                                <option value="DE" {{ old('shipping_country', $user->country) === 'DE' ? 'selected' : '' }}>🇩🇪 Deutschland</option>
                                <option value="GB" {{ old('shipping_country', $user->country) === 'GB' ? 'selected' : '' }}>🇬🇧 United Kingdom</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-outfit text-xs font-semibold tracking-widest
                                           text-primary uppercase mb-2">
                                {{ __('app.contact_name') }}
                            </label>
                            <input type="text" name="shipping_contact"
                                   value="{{ old('shipping_contact') }}"
                                   class="w-full bg-light border-0 rounded-2xl px-5 py-4
                                          font-outfit text-sm text-dark placeholder:text-gray-400
                                          focus:outline-none focus:ring-2 focus:ring-primary/30 transition" />
                        </div>

                        <div>
                            <label class="block font-outfit text-xs font-semibold tracking-widest
                                           text-primary uppercase mb-2">
                                {{ __('app.phone') }}
                            </label>
                            <input type="tel" name="shipping_phone"
                                   value="{{ old('shipping_phone') }}"
                                   class="w-full bg-light border-0 rounded-2xl px-5 py-4
                                          font-outfit text-sm text-dark placeholder:text-gray-400
                                          focus:outline-none focus:ring-2 focus:ring-primary/30 transition" />
                        </div>
                    </div>
                </div>

                {{-- Billing (read-only) --}}
                <div class="bg-white rounded-3xl shadow-sm p-8">
                    <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-5">
                        {{ __('app.billing_address') }}
                    </p>
                    <div class="bg-light rounded-2xl p-5 space-y-1">
                        <p class="font-alumni text-sm-header text-dark">{{ $user->company_name }}</p>
                        <p class="font-outfit text-sm text-gray-500">{{ __('app.cif') }}: {{ $user->cif }}</p>
                        <p class="font-outfit text-sm text-gray-500">{{ $user->address }}, {{ $user->city }}</p>
                        <p class="font-outfit text-sm text-gray-500">{{ $user->postal_code }} · {{ $user->country }}</p>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="bg-white rounded-3xl shadow-sm p-8">
                    <label class="block font-outfit text-xs font-semibold tracking-widest
                                   text-primary uppercase mb-3">
                        {{ __('app.notes') }}
                    </label>
                    <textarea name="notes" rows="3"
                              placeholder="{{ __('app.notes') }}..."
                              class="w-full bg-light border-0 rounded-2xl px-5 py-4
                                     font-outfit text-sm text-dark placeholder:text-gray-400
                                     focus:outline-none focus:ring-2 focus:ring-primary/30
                                     resize-none transition">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- Right: Summary --}}
            <div class="lg:w-80 flex-shrink-0">
                <div class="bg-white rounded-3xl shadow-sm p-6 sticky top-28">
                    <p class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-5">
                        {{ __('app.order_summary') }}
                    </p>

                    <div class="space-y-3 mb-5">
                        @foreach($cartItems as $item)
                            <div class="flex justify-between items-start gap-2">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        @if($item->is_print_job)
                                            <span class="font-outfit text-xs text-primary">🖨️</span>
                                        @endif
                                        <p class="font-outfit text-xs text-dark line-clamp-1">
                                            {{ $item->display_name }}
                                        </p>
                                    </div>
                                    <p class="font-outfit text-xs text-gray-400">
                                        × {{ $item->quantity }}
                                        @if(!$item->is_print_job && $item->product->unit)
                                            {{ $item->product->unit }}
                                        @else
                                            ut.
                                        @endif
                                    </p>
                                </div>
                                <p class="font-outfit text-sm text-dark shrink-0">
                                    {{ number_format($item->line_total, 2, ',', '.') }} €
                                </p>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-2 border-t border-gray-100 pt-4 mb-4">
                        <div class="flex justify-between">
                            <span class="font-outfit text-xs text-gray-400">{{ __('app.subtotal') }}</span>
                            <span class="font-outfit text-sm text-dark">{{ number_format($subtotal, 2, ',', '.') }} €</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-outfit text-xs text-gray-400">{{ __('app.vat') }}</span>
                            <span class="font-outfit text-sm text-dark">{{ number_format($vatAmount, 2, ',', '.') }} €</span>
                        </div>
                    </div>

                    <div class="flex justify-between mb-6 border-t border-gray-100 pt-4">
                        <span class="font-alumni text-h6 text-dark">{{ __('app.total') }}</span>
                        <span class="font-alumni text-h4 text-primary">{{ number_format($total, 2, ',', '.') }} €</span>
                    </div>

                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2
                                   bg-primary text-white font-alumni text-sm-header
                                   py-4 rounded-2xl hover:brightness-110 active:scale-95
                                   transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ __('app.place_order') }}
                    </button>

                    <p class="font-outfit text-xs text-gray-400 text-center mt-4">
                        {{ __('app.invoice_auto_generated') }}
                    </p>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection
