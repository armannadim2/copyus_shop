@extends('layouts.app')
@section('title', 'Demanar pressupost')

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
 Demanar pressupost
 </span>
 </div>

 <h1 class="font-alumni text-h1 text-white ">
 Explica'ns el<br>
 <span class="text-gradient">teu projecte.</span>
 </h1>

 <p class="font-outfit text-body-lg text-white/60 mt-6 max-w-xl leading-relaxed">
 Completa el formulari i et farem arribar una valoració
 personalitzada en menys de 24 hores laborables, sense compromís.
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
 Detalls del projecte
 </h2>
 <p class="font-outfit text-body-sm text-gray-400 mb-8">
 Tots els camps amb * són obligatoris.
 </p>

 @if($errors->any())
 <div class="mb-6 bg-red-50 border border-red-200 text-red-600
 font-outfit text-body-sm px-4 py-3 rounded-xl">
 <p class="font-semibold mb-1">Revisa els camps marcats:</p>
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
 tracking-widest mb-4">Contacte</p>

 <div class="grid md:grid-cols-2 gap-4">
 <div>
 <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">
 Nom complet *
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
 Correu electrònic *
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
 Telèfon
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
 Empresa
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
 CIF / NIF
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
 tracking-widest mb-4">Projecte</p>

 <div class="grid md:grid-cols-2 gap-4">
 <div class="md:col-span-2">
 <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">
 Tipus de servei *
 </label>
 <select name="service_type" required
 class="w-full border border-gray-200 rounded-xl px-4 py-2.5
 font-outfit text-sm focus:outline-none
 focus:ring-2 focus:ring-primary/40 focus:border-primary
 transition bg-white">
 <option value="">Selecciona una opció…</option>
 @foreach([
 'Impressió digital',
 'Gran format',
 'Marxandatge personalitzat',
 'Papereria d\'oficina',
 'Enquadernació i acabats',
 'Disseny gràfic',
 'Altres',
 ] as $option)
 <option value="{{ $option }}"
 @selected(old('service_type') === $option)>
 {{ $option }}
 </option>
 @endforeach
 </select>
 </div>

 <div>
 <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">
 Quantitat aproximada
 </label>
 <input type="number" name="quantity" min="1" max="1000000"
 placeholder="Ex. 500"
 value="{{ old('quantity') }}"
 class="w-full border border-gray-200 rounded-xl px-4 py-2.5
 font-outfit text-sm focus:outline-none
 focus:ring-2 focus:ring-primary/40 focus:border-primary
 transition placeholder:text-gray-300">
 </div>
 <div>
 <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">
 Termini desitjat
 </label>
 <select name="deadline"
 class="w-full border border-gray-200 rounded-xl px-4 py-2.5
 font-outfit text-sm focus:outline-none
 focus:ring-2 focus:ring-primary/40 focus:border-primary
 transition bg-white">
 <option value="">Sense urgència</option>
 <option value="24-48h" @selected(old('deadline') === '24-48h')>24-48 hores</option>
 <option value="1 setmana" @selected(old('deadline') === '1 setmana')>1 setmana</option>
 <option value="2 setmanes" @selected(old('deadline') === '2 setmanes')>2 setmanes</option>
 <option value="1 mes" @selected(old('deadline') === '1 mes')>1 mes</option>
 </select>
 </div>
 <div class="md:col-span-2">
 <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">
 Pressupost orientatiu
 </label>
 <select name="budget_range"
 class="w-full border border-gray-200 rounded-xl px-4 py-2.5
 font-outfit text-sm focus:outline-none
 focus:ring-2 focus:ring-primary/40 focus:border-primary
 transition bg-white">
 <option value="">Prefereixo no indicar</option>
 @foreach(['< 100 €', '100 - 500 €', '500 - 1.500 €', '1.500 - 5.000 €', '> 5.000 €'] as $range)
 <option value="{{ $range }}" @selected(old('budget_range') === $range)>
 {{ $range }}
 </option>
 @endforeach
 </select>
 </div>

 <div class="md:col-span-2">
 <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">
 Descriu el projecte *
 </label>
 <textarea name="description" required rows="6" maxlength="5000"
 placeholder="Mides, materials, colors, acabats, referències, ús previst…"
 class="w-full border border-gray-200 rounded-xl px-4 py-2.5
 font-outfit text-sm focus:outline-none
 focus:ring-2 focus:ring-primary/40 focus:border-primary
 transition placeholder:text-gray-300">{{ old('description') }}</textarea>
 </div>

 <div class="md:col-span-2">
 <label class="block font-outfit text-xs font-medium text-dark/70 mb-1.5">
 Adjunta un fitxer (opcional)
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
 PDF, JPG, PNG, AI, EPS, SVG o ZIP. Màxim 8 MB.
 </p>
 </div>
 </div>
 </div>

 <div class="pt-4 flex flex-wrap gap-3 items-center">
 <button type="submit"
 class="bg-primary text-white font-alumni text-sm-header
 px-7 py-3 rounded-xl hover:brightness-110
 active:scale-95 transition-all">
 Enviar sol·licitud →
 </button>
 <p class="font-outfit text-body-sm text-gray-400">
 Resposta en menys de 24 hores laborables.
 </p>
 </div>
 </form>
 </div>
 </div>

 {{-- Right: info aside --}}
 <aside class="space-y-6">

 <div class="bg-light rounded-3xl p-6 border border-gray-100">
 <h3 class="font-alumni text-h5 text-dark mb-4">
 Què inclou el pressupost?
 </h3>
 <ul class="space-y-3 font-outfit text-body-md text-gray-500">
 @foreach([
 'Estudi del projecte i viabilitat tècnica',
 'Recomanacions de materials i acabats',
 'Preu definitiu sense sorpreses',
 'Termini de producció i lliurament',
 'Factura B2B amb IVA desglossat',
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
 <h3 class="font-alumni text-h5 mb-2">Necessites parlar?</h3>
 <p class="font-outfit text-body-md text-white/70 leading-relaxed mb-4">
 Si prefereixes, contacta'ns directament i t'atendrem
 de seguida.
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
