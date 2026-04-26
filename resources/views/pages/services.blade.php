@extends('layouts.app')
@section('title', 'Serveis')

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
 Serveis · Impressió i marxandatge
 </span>
 </div>

 <h1 class="font-alumni text-h1 text-white 
 animate-reveal-up delay-100">
 Tot el que necessites,<br>
 <span class="text-gradient">en un sol lloc.</span>
 </h1>

 <p class="font-outfit text-body-lg text-white/60 mt-6 max-w-xl leading-relaxed
 animate-reveal-up delay-200">
 Impressió digital, gran format, papereria d'oficina i
 marxandatge personalitzat. Configura, demana pressupost o
 compra directament des del catàleg.
 </p>

 <div class="flex flex-wrap gap-4 mt-10 animate-reveal-up delay-300">
 <a href="{{ route('print.index') }}" class="btn-primary">
 Configurar impressió
 </a>
 <a href="{{ route('request-quote') }}" class="btn-outline-white">
 Demanar pressupost
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
 Què oferim
 </p>
 <h2 class="font-alumni text-h2 text-dark ">
 Catàleg de serveis.
 </h2>
 </div>

 @php
 $services = [
 [
 'title' => 'Impressió digital',
 'desc' => 'Targetes, flyers, catàlegs, díptics i tríptics impresos en alta resolució a partir d\'una unitat.',
 'price' => 'des de 0,12 €/ut',
 'cta' => 'Configurar',
 'href' => 'print.index',
 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>',
 'color' => 'primary',
 ],
 [
 'title' => 'Gran format',
 'desc' => 'Lones, vinils, banderoles, posters i roll-ups. Producció pròpia per a esdeveniments i punts de venda.',
 'price' => 'des de 12 €/m²',
 'cta' => 'Demanar preu',
 'href' => 'request-quote',
 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v10a1 1 0 01-1 1h-5l-5 5v-5H5a1 1 0 01-1-1V5z"/>',
 'color' => 'secondary',
 ],
 [
 'title' => 'Marxandatge personalitzat',
 'desc' => 'Bosses, samarretes, tasses, llibretes, bolígrafs i regals corporatius amb el teu logo.',
 'price' => 'des de 1,90 €/ut',
 'cta' => 'Demanar preu',
 'href' => 'request-quote',
 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>',
 'color' => 'dark',
 ],
 [
 'title' => 'Papereria d\'oficina',
 'desc' => 'Material d\'oficina, tinta, paper, arxivadors i consumibles amb preus B2B per a empreses registrades.',
 'price' => 'preus B2B',
 'cta' => 'Veure catàleg',
 'href' => 'products.index',
 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
 'color' => 'primary',
 ],
 [
 'title' => 'Enquadernació i acabats',
 'desc' => 'Wire-O, espiral, cosit, plastificat, encartronat i altres acabats per a llibres, manuals i memòries.',
 'price' => 'des de 2,50 €/ut',
 'cta' => 'Demanar preu',
 'href' => 'request-quote',
 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
 'color' => 'secondary',
 ],
 [
 'title' => 'Disseny gràfic',
 'desc' => 'Si necessites maquetar o crear identitat visual, t\'ajudem amb el disseny abans de la impressió.',
 'price' => 'sota petició',
 'cta' => 'Parlar-ne',
 'href' => 'contact',
 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>',
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
 Com treballem
 </p>
 <h2 class="font-alumni text-h2 text-dark ">
 De la idea a la teva oficina,<br>
 en 4 passos.
 </h2>
 </div>

 <div class="grid md:grid-cols-4 gap-6">
 @foreach([
 ['n' => '01', 'title' => 'Demana o configura', 'desc' => 'Configura el treball d\'impressió o demana pressupost per a projectes a mida.'],
 ['n' => '02', 'title' => 'Pressupost en 24h', 'desc' => 'Validem la viabilitat i t\'enviem el preu definitiu sense compromís.'],
 ['n' => '03', 'title' => 'Producció pròpia', 'desc' => 'Imprimim a Mataró amb materials certificats i un control de qualitat per peça.'],
 ['n' => '04', 'title' => 'Lliurament a l\'oficina', 'desc' => 'Et fem arribar la comanda a la teva adreça amb factura B2B inclosa.'],
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
 Tens un projecte<br>
 <span class="text-gradient">a mida?</span>
 </h2>
 <p class="font-outfit text-body-lg text-white/60 mt-4">
 Explica'ns què necessites i et farem arribar una valoració
 personalitzada en menys de 24 hores laborables.
 </p>
 <div class="flex flex-wrap gap-4 mt-8">
 <a href="{{ route('request-quote') }}" class="btn-primary">
 Demanar pressupost
 </a>
 <a href="{{ route('contact') }}" class="btn-outline-white">
 Contactar
 </a>
 </div>
 </div>
 </div>
 </div>
 </section>

@endsection
