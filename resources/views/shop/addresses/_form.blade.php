<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <label class="block font-outfit text-xs font-medium text-gray-600 mb-1">
            {{ __('app.address_label') }} <span class="text-red-400">*</span>
        </label>
        <input type="text" name="label"
               value="{{ old('label', $address->label ?? '') }}"
               placeholder="{{ __('app.address_label_placeholder') }}"
               required
               class="w-full font-outfit text-sm border border-gray-200 rounded-2xl px-4 py-3
                      focus:outline-none focus:border-primary transition-colors">
    </div>

    <div>
        <label class="block font-outfit text-xs font-medium text-gray-600 mb-1">
            {{ __('app.contact_name') }}
        </label>
        <input type="text" name="contact_name"
               value="{{ old('contact_name', $address->contact_name ?? '') }}"
               class="w-full font-outfit text-sm border border-gray-200 rounded-2xl px-4 py-3
                      focus:outline-none focus:border-primary transition-colors">
    </div>

    <div>
        <label class="block font-outfit text-xs font-medium text-gray-600 mb-1">
            {{ __('app.phone') }}
        </label>
        <input type="tel" name="phone"
               value="{{ old('phone', $address->phone ?? '') }}"
               class="w-full font-outfit text-sm border border-gray-200 rounded-2xl px-4 py-3
                      focus:outline-none focus:border-primary transition-colors">
    </div>

    <div class="sm:col-span-2">
        <label class="block font-outfit text-xs font-medium text-gray-600 mb-1">
            {{ __('app.address') }} <span class="text-red-400">*</span>
        </label>
        <input type="text" name="address"
               value="{{ old('address', $address->address ?? '') }}"
               required
               class="w-full font-outfit text-sm border border-gray-200 rounded-2xl px-4 py-3
                      focus:outline-none focus:border-primary transition-colors">
    </div>

    <div>
        <label class="block font-outfit text-xs font-medium text-gray-600 mb-1">
            {{ __('app.city') }} <span class="text-red-400">*</span>
        </label>
        <input type="text" name="city"
               value="{{ old('city', $address->city ?? '') }}"
               required
               class="w-full font-outfit text-sm border border-gray-200 rounded-2xl px-4 py-3
                      focus:outline-none focus:border-primary transition-colors">
    </div>

    <div>
        <label class="block font-outfit text-xs font-medium text-gray-600 mb-1">
            {{ __('app.postal_code') }} <span class="text-red-400">*</span>
        </label>
        <input type="text" name="postal_code"
               value="{{ old('postal_code', $address->postal_code ?? '') }}"
               required
               class="w-full font-outfit text-sm border border-gray-200 rounded-2xl px-4 py-3
                      focus:outline-none focus:border-primary transition-colors">
    </div>

    <div>
        <label class="block font-outfit text-xs font-medium text-gray-600 mb-1">
            {{ __('app.country') }} <span class="text-red-400">*</span>
        </label>
        <select name="country"
                class="w-full font-outfit text-sm border border-gray-200 rounded-2xl px-4 py-3
                       focus:outline-none focus:border-primary transition-colors">
            @php $selectedCountry = old('country', $address->country ?? 'ES'); @endphp
            <option value="ES" @selected($selectedCountry === 'ES')>🇪🇸 España</option>
            <option value="FR" @selected($selectedCountry === 'FR')>🇫🇷 France</option>
            <option value="PT" @selected($selectedCountry === 'PT')>🇵🇹 Portugal</option>
            <option value="DE" @selected($selectedCountry === 'DE')>🇩🇪 Deutschland</option>
            <option value="GB" @selected($selectedCountry === 'GB')>🇬🇧 United Kingdom</option>
        </select>
    </div>

    <div class="flex items-center gap-3">
        <input type="hidden" name="is_default" value="0" />
        <input type="checkbox" name="is_default" id="is_default_{{ $address->id ?? 'new' }}"
               value="1" class="w-4 h-4 accent-primary"
               {{ old('is_default', $address->is_default ?? false) ? 'checked' : '' }}>
        <label for="is_default_{{ $address->id ?? 'new' }}"
               class="font-outfit text-sm text-gray-600">
            {{ __('app.set_as_default') }}
        </label>
    </div>
</div>
