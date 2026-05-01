@extends('layouts.app')
@section('title', __('app.about_title'))

@section('content')

 {{-- ── Hero ─────────────────────────────────────────────────────────────── --}}
 <section class="relative bg-dark overflow-hidden -mt-16 lg:-mt-20">

 <div class="absolute inset-0 pointer-events-none overflow-hidden">
 <div class="absolute -top-32 -right-32 w-[500px] h-[500px] rounded-full
 bg-primary opacity-10 blur-3xl"></div>
 <div class="absolute -bottom-20 -left-20 w-[400px] h-[400px] rounded-full
 bg-secondary opacity-10 blur-3xl"></div>
 </div>

 <div class="section relative py-28 md:py-36">
 <div class="max-w-3xl">
 <div class="inline-flex items-center gap-2 bg-white/10 rounded-full
 px-4 py-1.5 mb-6 animate-reveal-up">
 <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
 <span class="font-outfit text-body-md text-white/70">
 {{ __('app.about_badge') }}
 </span>
 </div>

 <h1 class="font-alumni text-h1 text-white
 animate-reveal-up delay-100">
 {{ __('app.about_hero_title_1') }}<br>
 <em class="italic">{{ __('app.about_hero_title_2') }}</em>
 </h1>

 <p class="font-outfit text-body-lg text-white/60 mt-6 max-w-xl leading-relaxed
 animate-reveal-up delay-200">
 {{ __('app.about_hero_intro') }}
 </p>
 </div>
 </div>
 </section>

 {{-- ── Història ─────────────────────────────────────────────────────────── --}}
 <section class="py-20">
 <div class="section grid md:grid-cols-2 gap-14 items-center">
 <div>
 <p class="font-outfit text-body-md font-semibold text-primary uppercase
 tracking-widest mb-3">
 {{ __('app.about_history_eyebrow') }}
 </p>
 <h2 class="font-alumni text-h2 text-dark mb-6 ">
 {{ __('app.about_history_title_1') }}<br>
 {{ __('app.about_history_title_2') }}
 </h2>
 <div class="space-y-4 font-outfit text-body-lg text-gray-600 leading-relaxed">
 <p>{{ __('app.about_history_p1') }}</p>
 <p>{{ __('app.about_history_p2') }}</p>
 <p>{{ __('app.about_history_p3') }}</p>
 </div>
 </div>

 {{-- Card stack --}}
 <div class="relative">
 <div class="bg-light rounded-3xl p-10 border border-gray-100">
 <div class="grid grid-cols-2 gap-6">
 <div class="bg-white rounded-2xl p-5 border border-gray-100">
 <p class="font-alumni text-h3 text-primary">2018</p>
 <p class="font-outfit text-body-sm text-gray-500 mt-1">
 {{ __('app.about_stat_year') }}
 </p>
 </div>
 <div class="bg-white rounded-2xl p-5 border border-gray-100">
 <p class="font-alumni text-h3 text-secondary">+500</p>
 <p class="font-outfit text-body-sm text-gray-500 mt-1">
 {{ __('app.about_stat_clients') }}
 </p>
 </div>
 <div class="bg-white rounded-2xl p-5 border border-gray-100">
 <p class="font-alumni text-h3 text-dark">24h</p>
 <p class="font-outfit text-body-sm text-gray-500 mt-1">
 {{ __('app.about_stat_response') }}
 </p>
 </div>
 <div class="bg-white rounded-2xl p-5 border border-gray-100">
 <p class="font-alumni text-h3 text-primary">100%</p>
 <p class="font-outfit text-body-sm text-gray-500 mt-1">
 {{ __('app.about_stat_local') }}
 </p>
 </div>
 </div>
 </div>
 <div class="absolute -top-4 -right-4 w-20 h-20 rounded-full
 bg-primary/10 blur-xl"></div>
 </div>
 </div>
 </section>

 {{-- ── Valors ───────────────────────────────────────────────────────────── --}}
 <section class="py-20 bg-light">
 <div class="section">
 <div class="max-w-2xl mb-14">
 <p class="font-outfit text-body-md font-semibold text-primary uppercase
 tracking-widest mb-3">
 {{ __('app.about_values_eyebrow') }}
 </p>
 <h2 class="font-alumni text-h2 text-dark ">
 {{ __('app.about_values_title') }}
 </h2>
 </div>

 <div class="grid md:grid-cols-3 gap-6">
 {{-- Proximitat --}}
 <div class="bg-white rounded-3xl p-8 border border-gray-100
 hover:border-primary/30 transition-colors">
 <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center
 justify-center mb-5">
 <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
 d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
 d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
 </svg>
 </div>
 <h3 class="font-alumni text-h5 text-dark mb-2">{{ __('app.about_value_1_title') }}</h3>
 <p class="font-outfit text-body-md text-gray-500 leading-relaxed">
 {{ __('app.about_value_1_desc') }}
 </p>
 </div>

 {{-- Qualitat --}}
 <div class="bg-white rounded-3xl p-8 border border-gray-100
 hover:border-primary/30 transition-colors">
 <div class="w-12 h-12 rounded-2xl bg-secondary/10 flex items-center
 justify-center mb-5">
 <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
 d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
 </svg>
 </div>
 <h3 class="font-alumni text-h5 text-dark mb-2">{{ __('app.about_value_2_title') }}</h3>
 <p class="font-outfit text-body-md text-gray-500 leading-relaxed">
 {{ __('app.about_value_2_desc') }}
 </p>
 </div>

 {{-- Rapidesa --}}
 <div class="bg-white rounded-3xl p-8 border border-gray-100
 hover:border-primary/30 transition-colors">
 <div class="w-12 h-12 rounded-2xl bg-dark/10 flex items-center
 justify-center mb-5">
 <svg class="w-6 h-6 text-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
 d="M13 10V3L4 14h7v7l9-11h-7z"/>
 </svg>
 </div>
 <h3 class="font-alumni text-h5 text-dark mb-2">{{ __('app.about_value_3_title') }}</h3>
 <p class="font-outfit text-body-md text-gray-500 leading-relaxed">
 {{ __('app.about_value_3_desc') }}
 </p>
 </div>
 </div>
 </div>
 </section>

 {{-- ── Equip ────────────────────────────────────────────────────────────── --}}
 <section class="py-20">
 <div class="section">
 <div class="max-w-2xl mb-14">
 <p class="font-outfit text-body-md font-semibold text-primary uppercase
 tracking-widest mb-3">
 {{ __('app.about_team_eyebrow') }}
 </p>
 <h2 class="font-alumni text-h2 text-dark ">
 {{ __('app.about_team_title') }}
 </h2>
 <p class="font-outfit text-body-lg text-gray-500 mt-4 leading-relaxed">
 {{ __('app.about_team_subtitle') }}
 </p>
 </div>

 <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
 @foreach([
 ['name' => 'Marc Vila', 'role' => __('app.about_team_role_1'), 'init' => 'MV'],
 ['name' => 'Laia Roig', 'role' => __('app.about_team_role_2'), 'init' => 'LR'],
 ['name' => 'Jordi Puig', 'role' => __('app.about_team_role_3'), 'init' => 'JP'],
 ['name' => 'Núria Bosch', 'role' => __('app.about_team_role_4'), 'init' => 'NB'],
 ] as $member)
 <div class="bg-white rounded-3xl p-6 border border-gray-100 text-center">
 <div class="w-20 h-20 rounded-full bg-gradient-to-br from-primary
 to-secondary mx-auto mb-4 flex items-center justify-center
 text-white font-alumni text-h4">
 {{ $member['init'] }}
 </div>
 <p class="font-alumni text-h6 text-dark">{{ $member['name'] }}</p>
 <p class="font-outfit text-body-sm text-gray-400 mt-1">{{ $member['role'] }}</p>
 </div>
 @endforeach
 </div>
 </div>
 </section>

 {{-- ── CTA ─────────────────────────────────────────────────────────────── --}}
 <section class="py-20 bg-dark relative overflow-hidden">
 <div class="absolute inset-0 pointer-events-none">
 <div class="absolute top-0 right-0 w-[400px] h-[400px] rounded-full
 bg-primary opacity-10 blur-3xl"></div>
 </div>
 <div class="section relative text-center max-w-2xl mx-auto">
 <h2 class="font-alumni text-h2 text-white ">
 {{ __('app.about_cta_title_1') }}<br>
 <em class="italic">{{ __('app.about_cta_title_2') }}</em>.
 </h2>
 <p class="font-outfit text-body-lg text-white/60 mt-5">
 {{ __('app.about_cta_subtitle') }}
 </p>
 <div class="flex flex-wrap gap-4 justify-center mt-10">
 <a href="{{ route('products.index') }}" class="btn-primary">
 {{ __('app.about_cta_btn_products') }}
 </a>
 <a href="{{ route('contact') }}" class="btn-outline-white">
 {{ __('app.about_cta_btn_contact') }}
 </a>
 </div>
 </div>
 </section>

@endsection
