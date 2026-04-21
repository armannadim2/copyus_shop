<footer class="bg-gray-50 border-t border-gray-200 mt-20">
    <div class="section py-14">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">

            {{-- Brand --}}
            <div>
                <a href="{{ route('home') }}" class="shrink-0 inline-block">
                    <img src="{{ asset('assets/images/logo/' . rawurlencode('FULL LOGO (Red Sun + Shiny Blue).svg')) }}"
                         alt="Copyus"
                         class="w-auto transition-all duration-300"
                         style="height:56px; margin-left:-8px">
                </a>
                <p class="font-outfit text-body-lg text-gray-500 max-w-xs leading-relaxed mt-4">
                    Solucions premium d'impressió digital i marxandatge per a startups i grans empreses.
                    El teu soci en producció creativa.
                </p>
            </div>

            {{-- Enllaços ràpids --}}
            <div>
                <h4 class="font-outfit font-bold text-gray-900 mb-5">
                    Enllaços ràpids
                </h4>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('products.index') }}"
                           class="font-outfit text-body-lg text-gray-500 hover:text-primary transition-colors">
                            Digital Printing
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('products.index') }}"
                           class="font-outfit text-body-lg text-gray-500 hover:text-primary transition-colors">
                            Large Formats
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('products.index') }}"
                           class="font-outfit text-body-lg text-gray-500 hover:text-primary transition-colors">
                            Custom Merch
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('products.index') }}"
                           class="font-outfit text-body-lg text-gray-500 hover:text-primary transition-colors">
                            Office Supplies
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Suport --}}
            <div>
                <h4 class="font-outfit font-bold text-gray-900 mb-5">
                    Suport
                </h4>
                <ul class="space-y-3">
                    <li>
                        <a href="#"
                           class="font-outfit text-body-lg text-gray-500 hover:text-primary transition-colors">
                            Help Center
                        </a>
                    </li>
                    <li>
                        <a href="#"
                           class="font-outfit text-body-lg text-gray-500 hover:text-primary transition-colors">
                            Shipping Policy
                        </a>
                    </li>
                    <li>
                        <a href="#"
                           class="font-outfit text-body-lg text-gray-500 hover:text-primary transition-colors">
                            Track Order
                        </a>
                    </li>
                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <li>
                            <a href="{{ route('admin.index') }}"
                               class="font-outfit text-body-lg text-gray-500 hover:text-primary transition-colors">
                                Admin Access
                            </a>
                        </li>
                    @else
                        <li>
                            <span class="font-outfit text-body-lg text-gray-300">
                                Admin Access
                            </span>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- Contacte --}}
            <div>
                <h4 class="font-outfit font-bold text-gray-900 mb-5">
                    Contacte
                </h4>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div class="font-outfit text-body-lg text-gray-500 leading-snug">
                            <a href="tel:+34937409228" class="block hover:text-primary transition-colors">+34 937 409 228</a>
                            <a href="tel:+34695561180" class="block hover:text-primary transition-colors">+34 695 561 180</a>
                        </div>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <a href="mailto:copyus@copyus.es"
                           class="font-outfit text-body-lg text-gray-500 hover:text-primary transition-colors">
                            copyus@copyus.es
                        </a>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <address class="not-italic font-outfit text-body-lg text-gray-500 leading-snug">
                            Parc TecnoCampus Mataró-Maresme,<br>
                            TCM3, Local 2<br>
                            Carrer d'Ernest Lluch, 32, 08302<br>
                            Mataró, Barcelona
                        </address>
                    </li>
                </ul>
            </div>

        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-gray-200">
        <div class="section py-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="font-outfit text-body-sm text-gray-400">
                © {{ date('Y') }} COPYUS Impressió Digital. Tots els drets reservats.
            </p>
            <div class="flex items-center gap-6">
                <a href="#" class="font-outfit text-body-sm text-gray-400 hover:text-primary transition-colors">Privadesa</a>
                <a href="#" class="font-outfit text-body-sm text-gray-400 hover:text-primary transition-colors">Termes</a>
                <a href="#" class="font-outfit text-body-sm text-gray-400 hover:text-primary transition-colors">Cookies</a>
            </div>
        </div>
    </div>
</footer>
