@extends('layouts.app')
@section('title', __('app.contact_title'))

@section('content')

 {{-- ── Hero ─────────────────────────────────────────────────────────────── --}}
 <section class="relative bg-dark overflow-hidden -mt-16 lg:-mt-20">
 <div class="absolute inset-0 pointer-events-none overflow-hidden">
 <div class="absolute -top-32 -left-32 w-[500px] h-[500px] rounded-full
 bg-secondary opacity-10 blur-3xl"></div>
 <div class="absolute -bottom-20 -right-20 w-[400px] h-[400px] rounded-full
 bg-primary opacity-10 blur-3xl"></div>
 </div>

 <div class="section relative py-24 md:py-32">
 <div class="max-w-3xl">
 <div class="inline-flex items-center gap-2 bg-white/10 rounded-full
 px-4 py-1.5 mb-6">
 <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
 <span class="font-outfit text-body-md text-white/70">{{ __('app.contact_badge') }}</span>
 </div>

 <h1 class="font-alumni text-h1 text-white ">
 {{ __('app.contact_hero_title_1') }}<br>
 <em class="italic">{{ __('app.contact_hero_title_2') }}</em>
 </h1>

 <p class="font-outfit text-body-lg text-white/60 mt-6 max-w-xl leading-relaxed">
 {{ __('app.contact_hero_subtitle') }}
 </p>
 </div>
 </div>
 </section>

 {{-- ── Contact info cards ──────────────────────────────────────────────── --}}
 <section class="py-16">
 <div class="section grid md:grid-cols-3 gap-6">

 {{-- Phone --}}
 <div class="bg-white rounded-3xl p-8 border border-gray-100">
 <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center
 justify-center mb-5">
 <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
 d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
 </svg>
 </div>
 <h3 class="font-alumni text-h5 text-dark mb-2">{{ __('app.contact_phone') }}</h3>
 <p class="font-outfit text-body-md text-gray-500 leading-relaxed">
 <a href="tel:+34937409228" class="block hover:text-primary transition-colors">
 +34 937 409 228
 </a>
 <a href="tel:+34695561180" class="block hover:text-primary transition-colors">
 +34 695 561 180
 </a>
 </p>
 </div>

 {{-- Email --}}
 <div class="bg-white rounded-3xl p-8 border border-gray-100">
 <div class="w-12 h-12 rounded-2xl bg-secondary/10 flex items-center
 justify-center mb-5">
 <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
 d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
 </svg>
 </div>
 <h3 class="font-alumni text-h5 text-dark mb-2">{{ __('app.contact_email') }}</h3>
 <p class="font-outfit text-body-md text-gray-500 leading-relaxed">
 <a href="mailto:copyus@copyus.es" class="block hover:text-primary transition-colors">
 copyus@copyus.es
 </a>
 <span class="block text-body-sm text-gray-400 mt-1">
 {{ __('app.contact_email_response') }}
 </span>
 </p>
 </div>

 {{-- Hours --}}
 <div class="bg-white rounded-3xl p-8 border border-gray-100">
 <div class="w-12 h-12 rounded-2xl bg-dark/10 flex items-center
 justify-center mb-5">
 <svg class="w-6 h-6 text-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
 d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
 </svg>
 </div>
 <h3 class="font-alumni text-h5 text-dark mb-2">{{ __('app.contact_hours') }}</h3>
 <ul class="font-outfit text-body-md text-gray-500 space-y-1">
 <li class="flex justify-between">
 <span>{{ __('app.contact_hours_weekdays') }}</span>
 <span class="font-semibold text-dark">9:00 – 19:00</span>
 </li>
 <li class="flex justify-between">
 <span>{{ __('app.contact_hours_saturday') }}</span>
 <span class="font-semibold text-dark">10:00 – 14:00</span>
 </li>
 <li class="flex justify-between">
 <span>{{ __('app.contact_hours_sunday') }}</span>
 <span class="text-gray-400">{{ __('app.contact_hours_closed') }}</span>
 </li>
 </ul>
 </div>
 </div>
 </section>

 {{-- ── Form + Map ──────────────────────────────────────────────────────── --}}
 <section class="py-10 pb-20">
 <div class="section grid lg:grid-cols-2 gap-10">

 {{-- Form --}}
 <div class="bg-white rounded-3xl border border-gray-100 p-8 md:p-10">
 <h2 class="font-alumni text-h4 text-dark mb-1">
 {{ __('app.contact_form_title') }}
 </h2>
 <p class="font-outfit text-body-sm text-gray-400 mb-8">
 {{ __('app.contact_form_subtitle') }}
 </p>

 @if($errors->any())
 <div class="mb-6 bg-red-50 border border-red-200 text-red-600
 font-outfit text-body-sm px-4 py-3 rounded-xl">
 <ul class="list-disc list-inside space-y-0.5">
 @foreach($errors->all() as $error)
 <li>{{ $error }}</li>
 @endforeach
 </ul>
 </div>
 @endif

 <form method="POST" action="{{ route('contact.store') }}" class="space-y-4">
 @csrf

 <div class="grid md:grid-cols-2 gap-4">
 <div>
 <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">
 {{ __('app.contact_form_name') }} *
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
 {{ __('app.contact_form_email') }} *
 </label>
 <input type="email" name="email" required maxlength="160"
 value="{{ old('email', auth()->user()->email ?? '') }}"
 class="w-full border border-gray-200 rounded-xl px-4 py-2.5
 font-outfit text-sm focus:outline-none
 focus:ring-2 focus:ring-primary/40 focus:border-primary
 transition">
 </div>
 </div>

 <div class="grid md:grid-cols-2 gap-4">
 <div>
 <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">
 {{ __('app.contact_form_phone') }}
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
 {{ __('app.contact_form_subject') }} *
 </label>
 <input type="text" name="subject" required maxlength="200"
 value="{{ old('subject') }}"
 class="w-full border border-gray-200 rounded-xl px-4 py-2.5
 font-outfit text-sm focus:outline-none
 focus:ring-2 focus:ring-primary/40 focus:border-primary
 transition">
 </div>
 </div>

 <div>
 <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">
 {{ __('app.contact_form_message') }} *
 </label>
 <textarea name="message" required rows="6" maxlength="5000"
 placeholder="{{ __('app.contact_form_message_placeholder') }}"
 class="w-full border border-gray-200 rounded-xl px-4 py-2.5
 font-outfit text-sm focus:outline-none
 focus:ring-2 focus:ring-primary/40 focus:border-primary
 transition placeholder:text-gray-300">{{ old('message') }}</textarea>
 </div>

 <button type="submit"
 class="bg-primary text-white font-alumni text-sm-header
 px-7 py-3 rounded-xl hover:brightness-110
 active:scale-95 transition-all">
 {{ __('app.contact_form_submit') }}
 </button>
 </form>
 </div>

 {{-- Map + address --}}
 <div class="space-y-6">
 <div class="bg-light rounded-3xl border border-gray-100 overflow-hidden">
 <iframe
 src="https://www.google.com/maps?q=Carrer+d%27Ernest+Lluch+32+Matar%C3%B3&output=embed"
 width="100%" height="380" style="border:0" allowfullscreen=""
 loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
 </div>

 <div class="bg-white rounded-3xl border border-gray-100 p-7">
 <div class="flex items-start gap-4">
 <div class="w-10 h-10 rounded-2xl bg-primary/10 flex items-center
 justify-center shrink-0">
 <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
 d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
 d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
 </svg>
 </div>
 <div>
 <h3 class="font-alumni text-h5 text-dark mb-2">
 {{ __('app.contact_location_title') }}
 </h3>
 <address class="not-italic font-outfit text-body-md text-gray-500 leading-relaxed">
 Parc TecnoCampus Mataró-Maresme<br>
 TCM3, Local 2<br>
 Carrer d'Ernest Lluch, 32<br>
 08302 Mataró, Barcelona
 </address>
 <a href="https://www.google.com/maps/dir/?api=1&destination=Carrer+d%27Ernest+Lluch+32+Matar%C3%B3"
 target="_blank" rel="noopener"
 class="inline-flex items-center gap-1.5 mt-4 font-outfit text-body-sm
 font-semibold text-primary hover:underline">
 {{ __('app.contact_location_directions') }}
 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
 d="M14 5l7 7m0 0l-7 7m7-7H3"/>
 </svg>
 </a>
 </div>
 </div>
 </div>
 </div>
 </div>
 </section>

@endsection
