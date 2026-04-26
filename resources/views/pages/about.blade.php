@extends('layouts.app')
@section('title', 'Qui som')

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
 Qui som · Mataró
 </span>
 </div>

 <h1 class="font-alumni text-h1 text-white 
 animate-reveal-up delay-100">
 Som la papereria<br>
 <span class="text-gradient">del Tecnocampus.</span>
 </h1>

 <p class="font-outfit text-body-lg text-white/60 mt-6 max-w-xl leading-relaxed
 animate-reveal-up delay-200">
 A Copyus combinem la proximitat d'una papereria de barri amb la
 tecnologia d'una impremta digital. Servim startups, despatxos i
 centres educatius del Maresme amb material d'oficina, impressió
 professional i marxandatge personalitzat.
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
 La nostra història
 </p>
 <h2 class="font-alumni text-h2 text-dark mb-6 ">
 Una idea simple:<br>
 fer-ho fàcil.
 </h2>
 <div class="space-y-4 font-outfit text-body-lg text-gray-600 leading-relaxed">
 <p>
 Vam néixer al cor del Parc TecnoCampus de Mataró amb
 una pregunta concreta: per què la papereria i la
 impressió per a empreses ha de ser un mal de cap?
 </p>
 <p>
 La resposta va ser construir un servei que entengués els
 ritmes de qui treballa amb pressa: catàleg ampli, preus
 clars per a empreses registrades, factura amb IVA
 desglossat, i lliurament directe a l'oficina.
 </p>
 <p>
 Avui imprimim, encartonem, envasem i lliurem cada
 setmana milers d'unitats per a equips que volen menys
 gestió i més temps per al que importa.
 </p>
 </div>
 </div>

 {{-- Card stack --}}
 <div class="relative">
 <div class="bg-light rounded-3xl p-10 border border-gray-100">
 <div class="grid grid-cols-2 gap-6">
 <div class="bg-white rounded-2xl p-5 border border-gray-100">
 <p class="font-alumni text-h3 text-primary">2018</p>
 <p class="font-outfit text-body-sm text-gray-500 mt-1">
 Obrim al TecnoCampus
 </p>
 </div>
 <div class="bg-white rounded-2xl p-5 border border-gray-100">
 <p class="font-alumni text-h3 text-secondary">+500</p>
 <p class="font-outfit text-body-sm text-gray-500 mt-1">
 Empreses confien en nosaltres
 </p>
 </div>
 <div class="bg-white rounded-2xl p-5 border border-gray-100">
 <p class="font-alumni text-h3 text-dark">24h</p>
 <p class="font-outfit text-body-sm text-gray-500 mt-1">
 Resposta a sol·licituds
 </p>
 </div>
 <div class="bg-white rounded-2xl p-5 border border-gray-100">
 <p class="font-alumni text-h3 text-primary">100%</p>
 <p class="font-outfit text-body-sm text-gray-500 mt-1">
 Producció pròpia a Mataró
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
 Els nostres valors
 </p>
 <h2 class="font-alumni text-h2 text-dark ">
 Què ens fa diferents.
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
 <h3 class="font-alumni text-h5 text-dark mb-2">Proximitat</h3>
 <p class="font-outfit text-body-md text-gray-500 leading-relaxed">
 Som a 5 minuts del centre de Mataró i parlem amb cada client
 per entendre què necessita realment.
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
 <h3 class="font-alumni text-h5 text-dark mb-2">Qualitat garantida</h3>
 <p class="font-outfit text-body-md text-gray-500 leading-relaxed">
 Treballem amb materials certificats i revisem cada producció
 abans que arribi al client.
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
 <h3 class="font-alumni text-h5 text-dark mb-2">Rapidesa</h3>
 <p class="font-outfit text-body-md text-gray-500 leading-relaxed">
 Tirades exprés en 24-48 hores i lliurament directe a la
 teva oficina de tot el Maresme.
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
 L'equip
 </p>
 <h2 class="font-alumni text-h2 text-dark ">
 Persones, no màquines.
 </h2>
 <p class="font-outfit text-body-lg text-gray-500 mt-4 leading-relaxed">
 Darrere de cada comanda hi ha un equip petit que coneix el teu
 negoci. Sempre la mateixa cara, sempre la mateixa qualitat.
 </p>
 </div>

 <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
 @foreach([
 ['name' => 'Marc Vila', 'role' => 'Fundador & Producció', 'init' => 'MV'],
 ['name' => 'Laia Roig', 'role' => 'Atenció al client', 'init' => 'LR'],
 ['name' => 'Jordi Puig', 'role' => 'Disseny gràfic', 'init' => 'JP'],
 ['name' => 'Núria Bosch', 'role' => 'Logística', 'init' => 'NB'],
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
 Comença a treballar amb<br>
 <span class="text-gradient">Copyus</span>.
 </h2>
 <p class="font-outfit text-body-lg text-white/60 mt-5">
 Registra't i tindràs accés al catàleg complet, preus B2B i
 pressupostos personalitzats en menys de 24 hores.
 </p>
 <div class="flex flex-wrap gap-4 justify-center mt-10">
 <a href="{{ route('products.index') }}" class="btn-primary">
 Veure productes
 </a>
 <a href="{{ route('contact') }}" class="btn-outline-white">
 Parlar amb nosaltres
 </a>
 </div>
 </div>
 </section>

@endsection
