@extends('layouts.app')
@section('title', __('app.services_title'))

@section('content')

 {{-- ── Hero ─────────────────────────────────────────────────────────────── --}}
 <section class="relative bg-dark overflow-hidden -mt-16 lg:-mt-20">

 <div class="absolute inset-0 pointer-events-none overflow-hidden">
 <div class="absolute -top-32 -left-32 w-[500px] h-[500px] rounded-full
 bg-secondary opacity-10 blur-3xl"></div>
 <div class="absolute -bottom-20 -right-20 w-[400px] h-[400px] rounded-full
 bg-primary opacity-10 blur-3xl"></div>
 </div>

 <div class="section relative py-28 md:py-36">
 <div class="max-w-3xl">
 <div class="inline-flex items-center gap-2 bg-white/10 rounded-full
 px-4 py-1.5 mb-6 animate-reveal-up">
 <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
 <span class="font-outfit text-body-md text-white/70">
 {{ __('app.services_badge') }}
 </span>
 </div>

 <h1 class="font-alumni text-h1 text-white
 animate-reveal-up delay-100">
 {{ __('app.services_hero_title_1') }}<br>
 <em class="italic">{{ __('app.services_hero_title_2') }}</em>
 </h1>

 <p class="font-outfit text-body-lg text-white/60 mt-6 max-w-xl leading-relaxed
 animate-reveal-up delay-200">
 {{ __('app.services_hero_subtitle') }}
 </p>

 <div class="flex flex-wrap gap-4 mt-10 animate-reveal-up delay-300">
 <a href="{{ route('print.index') }}" class="btn-primary">
 {{ __('app.services_btn_configure') }}
 </a>
 <a href="{{ route('request-quote') }}" class="btn-outline-white">
 {{ __('app.services_btn_request') }}
 </a>
 </div>
 </div>
 </div>
 </section>

 {{-- ── Serveis principals ───────────────────────────────────────────────── --}}
 <section class="py-20">
 <div class="section">
 <div class="max-w-2xl mb-14">
 <p class="font-outfit text-body-md font-semibold text-primary uppercase
 tracking-widest mb-3">
 {{ __('app.services_eyebrow') }}
 </p>
 <h2 class="font-alumni text-h2 text-dark ">
 {{ __('app.services_section_title') }}
 </h2>
 </div>

 @php
 $services = [
 [
 'title' => __('app.service_print_title'),
 'desc'  => __('app.service_print_desc'),
 'price' => __('app.service_print_price'),
 'cta'   => __('app.service_print_cta'),
 'href'  => 'print.index',
 'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>',
 'color' => 'primary',
 ],
 [
 'title' => __('app.service_largeformat_title'),
 'desc'  => __('app.service_largeformat_desc'),
 'price' => __('app.service_largeformat_price'),
 'cta'   => __('app.service_largeformat_cta'),
 'href'  => 'request-quote',
 'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v10a1 1 0 01-1 1h-5l-5 5v-5H5a1 1 0 01-1-1V5z"/>',
 'color' => 'secondary',
 ],
 [
 'title' => __('app.service_merch_title'),
 'desc'  => __('app.service_merch_desc'),
 'price' => __('app.service_merch_price'),
 'cta'   => __('app.service_merch_cta'),
 'href'  => 'request-quote',
 'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>',
 'color' => 'dark',
 ],
 [
 'title' => __('app.service_stationery_title'),
 'desc'  => __('app.service_stationery_desc'),
 'price' => __('app.service_stationery_price'),
 'cta'   => __('app.service_stationery_cta'),
 'href'  => 'products.index',
 'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
 'color' => 'primary',
 ],
 [
 'title' => __('app.service_binding_title'),
 'desc'  => __('app.service_binding_desc'),
 'price' => __('app.service_binding_price'),
 'cta'   => __('app.service_binding_cta'),
 'href'  => 'request-quote',
 'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
 'color' => 'secondary',
 ],
 [
 'title' => __('app.service_design_title'),
 'desc'  => __('app.service_design_desc'),
 'price' => __('app.service_design_price'),
 'cta'   => __('app.service_design_cta'),
 'href'  => 'contact',
 'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>',
 'color' => 'dark',
 ],
 ];
 @endphp

 <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
 @foreach($services as $service)
 <div class="group bg-white rounded-3xl p-8 border border-gray-100
 hover:border-primary/40 hover:shadow-lg transition-all">
 <div class="w-14 h-14 rounded-2xl
 {{ $service['color'] === 'primary' ? 'bg-primary/10 text-primary' : '' }}
 {{ $service['color'] === 'secondary' ? 'bg-secondary/10 text-secondary' : '' }}
 {{ $service['color'] === 'dark' ? 'bg-dark/10 text-dark' : '' }}
 flex items-center justify-center mb-6">
 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 {!! $service['icon'] !!}
 </svg>
 </div>
 <h3 class="font-alumni text-h5 text-dark mb-2">
 {{ $service['title'] }}
 </h3>
 <p class="font-outfit text-body-md text-gray-500 leading-relaxed mb-5">
 {{ $service['desc'] }}
 </p>
 <div class="flex items-center justify-between pt-5 border-t border-gray-100">
 <span class="font-outfit text-body-sm font-semibold text-dark">
 {{ $service['price'] }}
 </span>
 <a href="{{ route($service['href']) }}"
 class="font-outfit text-body-sm font-semibold text-primary
 group-hover:underline">
 {{ $service['cta'] }} →
 </a>
 </div>
 </div>
 @endforeach
 </div>
 </div>
 </section>

 {{-- ── Procés ───────────────────────────────────────────────────────────── --}}
 <section class="py-20 bg-light">
 <div class="section">
 <div class="max-w-2xl mb-14">
 <p class="font-outfit text-body-md font-semibold text-primary uppercase
 tracking-widest mb-3">
 {{ __('app.services_process_eyebrow') }}
 </p>
 <h2 class="font-alumni text-h2 text-dark ">
 {{ __('app.services_process_title_1') }}<br>
 {{ __('app.services_process_title_2') }}
 </h2>
 </div>

 <div class="grid md:grid-cols-4 gap-6">
 @foreach([
 ['n' => '01', 'title' => __('app.services_step_1_title'), 'desc' => __('app.services_step_1_desc')],
 ['n' => '02', 'title' => __('app.services_step_2_title'), 'desc' => __('app.services_step_2_desc')],
 ['n' => '03', 'title' => __('app.services_step_3_title'), 'desc' => __('app.services_step_3_desc')],
 ['n' => '04', 'title' => __('app.services_step_4_title'), 'desc' => __('app.services_step_4_desc')],
 ] as $step)
 <div class="relative bg-white rounded-3xl p-7 border border-gray-100">
 <p class="font-alumni text-h2 text-primary/20 leading-none">
 {{ $step['n'] }}
 </p>
 <h3 class="font-alumni text-h6 text-dark mt-3">
 {{ $step['title'] }}
 </h3>
 <p class="font-outfit text-body-sm text-gray-500 mt-2 leading-relaxed">
 {{ $step['desc'] }}
 </p>
 </div>
 @endforeach
 </div>
 </div>
 </section>

 {{-- ── CTA ─────────────────────────────────────────────────────────────── --}}
 <section class="py-20">
 <div class="section">
 <div class="bg-dark rounded-3xl p-12 md:p-16 relative overflow-hidden">
 <div class="absolute inset-0 pointer-events-none">
 <div class="absolute -top-20 -right-20 w-[300px] h-[300px] rounded-full
 bg-primary opacity-15 blur-3xl"></div>
 <div class="absolute -bottom-20 -left-20 w-[250px] h-[250px] rounded-full
 bg-secondary opacity-15 blur-3xl"></div>
 </div>
 <div class="relative max-w-xl">
 <h2 class="font-alumni text-h2 text-white ">
 {{ __('app.services_cta_title_1') }}<br>
 <em class="italic">{{ __('app.services_cta_title_2') }}</em>
 </h2>
 <p class="font-outfit text-body-lg text-white/60 mt-4">
 {{ __('app.services_cta_subtitle') }}
 </p>
 <div class="flex flex-wrap gap-4 mt-8">
 <a href="{{ route('request-quote') }}" class="btn-primary">
 {{ __('app.services_cta_btn_quote') }}
 </a>
 <a href="{{ route('contact') }}" class="btn-outline-white">
 {{ __('app.services_cta_btn_contact') }}
 </a>
 </div>
 </div>
 </div>
 </div>
 </section>

@endsection
