@extends('layouts.app')
@section('title', __('app.rq_title'))

@section('content')

 {{-- ── Hero ─────────────────────────────────────────────────────────────── --}}
 <section class="relative bg-dark overflow-hidden -mt-16 lg:-mt-20">
 <div class="absolute inset-0 pointer-events-none overflow-hidden">
 <div class="absolute -top-32 -right-32 w-[500px] h-[500px] rounded-full
 bg-primary opacity-10 blur-3xl"></div>
 </div>

 <div class="section relative py-24 md:py-32">
 <div class="max-w-3xl">
 <div class="inline-flex items-center gap-2 bg-white/10 rounded-full
 px-4 py-1.5 mb-6">
 <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
 <span class="font-outfit text-body-md text-white/70">
 {{ __('app.rq_badge') }}
 </span>
 </div>

 <h1 class="font-alumni text-h1 text-white ">
 {{ __('app.rq_hero_title_1') }}<br>
 <em class="italic">{{ __('app.rq_hero_title_2') }}</em>
 </h1>

 <p class="font-outfit text-body-lg text-white/60 mt-6 max-w-xl leading-relaxed">
 {{ __('app.rq_hero_subtitle') }}
 </p>
 </div>
 </div>
 </section>

 {{-- ── Form ─────────────────────────────────────────────────────────────── --}}
 <section class="py-16">
 <div class="section grid lg:grid-cols-3 gap-10">

 {{-- Left: form --}}
 <div class="lg:col-span-2">
 <div class="bg-white rounded-3xl border border-gray-100 p-8 md:p-10">

 <h2 class="font-alumni text-h4 text-dark mb-1">
 {{ __('app.rq_section_title') }}
 </h2>
 <p class="font-outfit text-body-sm text-gray-400 mb-8">
 {{ __('app.rq_section_subtitle') }}
 </p>

 @if($errors->any())
 <div class="mb-6 bg-red-50 border border-red-200 text-red-600
 font-outfit text-body-sm px-4 py-3 rounded-xl">
 <p class="font-semibold mb-1">{{ __('app.rq_errors_review') }}</p>
 <ul class="list-disc list-inside space-y-0.5">
 @foreach($errors->all() as $error)
 <li>{{ $error }}</li>
 @endforeach
 </ul>
 </div>
 @endif

 <form method="POST" action="{{ route('request-quote.store') }}"
 enctype="multipart/form-data" class="space-y-6">
 @csrf

 {{-- Section: contact --}}
 <div>
 <p class="font-outfit text-xs font-semibold text-primary uppercase
 tracking-widest mb-4">{{ __('app.rq_section_contact') }}</p>

 <div class="grid md:grid-cols-2 gap-4">
 <div>
 <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">
 {{ __('app.rq_field_full_name') }} *
 </label>
 <input type="text" name="name" required maxlength="120"
 value="{{ old('name', auth()->user()->name ?? '') }}"
 class="w-full border border-gray-200 rounded-xl px-4 py-2.5
 font-outfit text-sm focus:outline-none
 focus:ring-2 focus:ring-primary/40 focus:border-primary
 transition">
 </div>
 <div>
 <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">
 {{ __('app.rq_field_email') }} *
 </label>
 <input type="email" name="email" required maxlength="160"
 value="{{ old('email', auth()->user()->email ?? '') }}"
 class="w-full border border-gray-200 rounded-xl px-4 py-2.5
 font-outfit text-sm focus:outline-none
 focus:ring-2 focus:ring-primary/40 focus:border-primary
 transition">
 </div>
 <div>
 <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">
 {{ __('app.rq_field_phone') }}
 </label>
 <input type="text" name="phone" maxlength="40"
 value="{{ old('phone', auth()->user()->phone ?? '') }}"
 class="w-full border border-gray-200 rounded-xl px-4 py-2.5
 font-outfit text-sm focus:outline-none
 focus:ring-2 focus:ring-primary/40 focus:border-primary
 transition">
 </div>
 <div>
 <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">
 {{ __('app.rq_field_company') }}
 </label>
 <input type="text" name="company_name" maxlength="160"
 value="{{ old('company_name', auth()->user()->company_name ?? '') }}"
 class="w-full border border-gray-200 rounded-xl px-4 py-2.5
 font-outfit text-sm focus:outline-none
 focus:ring-2 focus:ring-primary/40 focus:border-primary
 transition">
 </div>
 <div class="md:col-span-2">
 <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">
 {{ __('app.rq_field_cif') }}
 </label>
 <input type="text" name="cif" maxlength="40"
 value="{{ old('cif', auth()->user()->cif ?? '') }}"
 class="w-full border border-gray-200 rounded-xl px-4 py-2.5
 font-outfit text-sm focus:outline-none
 focus:ring-2 focus:ring-primary/40 focus:border-primary
 transition">
 </div>
 </div>
 </div>

 {{-- Section: project --}}
 <div class="pt-4 border-t border-gray-100">
 <p class="font-outfit text-xs font-semibold text-primary uppercase
 tracking-widest mb-4">{{ __('app.rq_section_project') }}</p>

 <div class="grid md:grid-cols-2 gap-4">
 <div class="md:col-span-2">
 <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">
 {{ __('app.rq_field_service_type') }} *
 </label>
 <select name="service_type" required
 class="w-full border border-gray-200 rounded-xl px-4 py-2.5
 font-outfit text-sm focus:outline-none
 focus:ring-2 focus:ring-primary/40 focus:border-primary
 transition bg-white">
 <option value="">{{ __('app.rq_select_placeholder') }}</option>
 @foreach([
 'print'       => __('app.rq_service_print'),
 'largeformat' => __('app.rq_service_largeformat'),
 'merch'       => __('app.rq_service_merch'),
 'stationery'  => __('app.rq_service_stationery'),
 'binding'     => __('app.rq_service_binding'),
 'design'      => __('app.rq_service_design'),
 'other'       => __('app.rq_service_other'),
 ] as $value => $label)
 <option value="{{ $value }}"
 @selected(old('service_type') === $value)>
 {{ $label }}
 </option>
 @endforeach
 </select>
 </div>

 <div>
 <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">
 {{ __('app.rq_field_quantity') }}
 </label>
 <input type="number" name="quantity" min="1" max="1000000"
 placeholder="{{ __('app.rq_field_quantity_placeholder') }}"
 value="{{ old('quantity') }}"
 class="w-full border border-gray-200 rounded-xl px-4 py-2.5
 font-outfit text-sm focus:outline-none
 focus:ring-2 focus:ring-primary/40 focus:border-primary
 transition placeholder:text-gray-300">
 </div>
 <div>
 <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">
 {{ __('app.rq_field_deadline') }}
 </label>
 <select name="deadline"
 class="w-full border border-gray-200 rounded-xl px-4 py-2.5
 font-outfit text-sm focus:outline-none
 focus:ring-2 focus:ring-primary/40 focus:border-primary
 transition bg-white">
 <option value="">{{ __('app.rq_deadline_none') }}</option>
 <option value="24-48h" @selected(old('deadline') === '24-48h')>{{ __('app.rq_deadline_24_48') }}</option>
 <option value="1week"  @selected(old('deadline') === '1week')>{{ __('app.rq_deadline_1week') }}</option>
 <option value="2weeks" @selected(old('deadline') === '2weeks')>{{ __('app.rq_deadline_2weeks') }}</option>
 <option value="1month" @selected(old('deadline') === '1month')>{{ __('app.rq_deadline_1month') }}</option>
 </select>
 </div>
 <div class="md:col-span-2">
 <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">
 {{ __('app.rq_field_budget') }}
 </label>
 <select name="budget_range"
 class="w-full border border-gray-200 rounded-xl px-4 py-2.5
 font-outfit text-sm focus:outline-none
 focus:ring-2 focus:ring-primary/40 focus:border-primary
 transition bg-white">
 <option value="">{{ __('app.rq_budget_none') }}</option>
 @foreach(['< 100 €', '100 - 500 €', '500 - 1.500 €', '1.500 - 5.000 €', '> 5.000 €'] as $range)
 <option value="{{ $range }}" @selected(old('budget_range') === $range)>
 {{ $range }}
 </option>
 @endforeach
 </select>
 </div>

 <div class="md:col-span-2">
 <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">
 {{ __('app.rq_field_description') }} *
 </label>
 <textarea name="description" required rows="6" maxlength="5000"
 placeholder="{{ __('app.rq_field_description_placeholder') }}"
 class="w-full border border-gray-200 rounded-xl px-4 py-2.5
 font-outfit text-sm focus:outline-none
 focus:ring-2 focus:ring-primary/40 focus:border-primary
 transition placeholder:text-gray-300">{{ old('description') }}</textarea>
 </div>

 <div class="md:col-span-2">
 <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">
 {{ __('app.rq_field_attachment') }}
 </label>
 <input type="file" name="attachment"
 accept=".pdf,.jpg,.jpeg,.png,.ai,.eps,.svg,.zip"
 class="w-full border border-dashed border-gray-200 rounded-xl
 px-4 py-3 font-outfit text-sm text-gray-500
 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg
 file:border-0 file:bg-primary/10 file:text-primary
 file:font-semibold hover:file:bg-primary/20
 transition">
 <p class="font-outfit text-xs text-gray-400 mt-1.5">
 {{ __('app.rq_field_attachment_hint') }}
 </p>
 </div>
 </div>
 </div>

 <div class="pt-4 flex flex-wrap gap-3 items-center">
 <button type="submit"
 class="bg-primary text-white font-alumni text-sm-header
 px-7 py-3 rounded-xl hover:brightness-110
 active:scale-95 transition-all">
 {{ __('app.rq_submit') }}
 </button>
 <p class="font-outfit text-body-sm text-gray-400">
 {{ __('app.rq_response_time') }}
 </p>
 </div>
 </form>
 </div>
 </div>

 {{-- Right: info aside --}}
 <aside class="space-y-6">

 <div class="bg-light rounded-3xl p-6 border border-gray-100">
 <h3 class="font-alumni text-h5 text-dark mb-4">
 {{ __('app.rq_aside_what_title') }}
 </h3>
 <ul class="space-y-3 font-outfit text-body-md text-gray-500">
 @foreach([
 __('app.rq_aside_what_1'),
 __('app.rq_aside_what_2'),
 __('app.rq_aside_what_3'),
 __('app.rq_aside_what_4'),
 __('app.rq_aside_what_5'),
 ] as $item)
 <li class="flex gap-3">
 <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
 </svg>
 <span>{{ $item }}</span>
 </li>
 @endforeach
 </ul>
 </div>

 <div class="bg-dark rounded-3xl p-6 text-white">
 <h3 class="font-alumni text-h5 mb-2">{{ __('app.rq_aside_call_title') }}</h3>
 <p class="font-outfit text-body-md text-white/70 leading-relaxed mb-4">
 {{ __('app.rq_aside_call_subtitle') }}
 </p>
 <ul class="space-y-2 font-outfit text-body-sm">
 <li class="flex items-center gap-3">
 <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
 d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
 </svg>
 <a href="tel:+34937409228" class="hover:text-primary transition-colors">
 +34 937 409 228
 </a>
 </li>
 <li class="flex items-center gap-3">
 <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
 d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
 </svg>
 <a href="mailto:copyus@copyus.es" class="hover:text-primary transition-colors">
 copyus@copyus.es
 </a>
 </li>
 </ul>
 </div>
 </aside>
 </div>
 </section>

@endsection
