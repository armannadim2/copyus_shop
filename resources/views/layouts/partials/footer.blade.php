<footer class="bg-light border-t border-dark/10">
    <div class="section py-16">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-10 lg:gap-12">

            {{-- ── Brand ─────────────────────────────────────────── --}}
            <div class="md:col-span-4">
                <a href="{{ route('home') }}" class="inline-block">
                    <img src="{{ asset('images/logo.svg') }}"
                         alt="Copyus"
                         class="h-9 md:h-10 w-auto">
                </a>
                <p class="font-outfit font-light text-body-md text-dark/65 mt-5 max-w-xs leading-relaxed">
                    {{ __('app.footer_brand_desc') }}
                </p>

                {{-- Working hours --}}
                <div class="mt-7 max-w-xs">
                    <p class="font-outfit text-xs font-semibold uppercase tracking-widest text-primary mb-3">
                        {{ __('app.home_visit_hours_label') }}
                    </p>
                    <ul class="font-outfit text-body-sm space-y-1.5">
                        <li class="flex items-center justify-between">
                            <span class="text-dark/65">{{ __('app.home_visit_days_mon_thu') }}</span>
                            <span class="text-primary font-semibold">9:00 – 17:00</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span class="text-dark/65">{{ __('app.home_visit_days_fri') }}</span>
                            <span class="text-primary font-semibold">9:00 – 14:00</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span class="text-dark/65">{{ __('app.home_visit_days_sat_sun') }}</span>
                            <span class="text-dark/40">{{ __('app.home_visit_closed') }}</span>
                        </li>
                    </ul>
                </div>

                {{-- Social media --}}
                <div class="mt-7 flex items-center gap-3">
                    <a href="https://www.facebook.com/profile.php?id=61580435619609"
                       target="_blank" rel="noopener"
                       aria-label="Facebook"
                       class="w-9 h-9 rounded-full flex items-center justify-center
                              bg-dark/5 text-dark/50 hover:bg-primary hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="https://www.instagram.com/copyus.tcm/"
                       target="_blank" rel="noopener"
                       aria-label="Instagram"
                       class="w-9 h-9 rounded-full flex items-center justify-center
                              bg-dark/5 text-dark/50 hover:bg-primary hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                    <a href="https://www.linkedin.com/company/110057381/"
                       target="_blank" rel="noopener"
                       aria-label="LinkedIn"
                       class="w-9 h-9 rounded-full flex items-center justify-center
                              bg-dark/5 text-dark/50 hover:bg-primary hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- ── Enllaços ràpids ──────────────────────────────── --}}
            <div class="md:col-span-3">
                <h6 class="font-outfit font-bold text-sm-header2 text-dark mb-5">
                    {{ __('app.footer_quick_links') }}
                </h6>
                <ul class="space-y-3">
                    @foreach([
                        ['label' => __('app.footer_link_print'),       'href' => route('print.index')],
                        ['label' => __('app.footer_link_largeformat'), 'href' => route('request-quote')],
                        ['label' => __('app.footer_link_merch'),       'href' => route('request-quote')],
                        ['label' => __('app.footer_link_stationery'),  'href' => route('products.index')],
                    ] as $link)
                        <li>
                            <a href="{{ $link['href'] }}"
                               class="font-outfit font-light text-body-md text-dark/65
                                      hover:text-primary transition-colors">
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- ── Suport ────────────────────────────────────────── --}}
            <div class="md:col-span-2">
                <h6 class="font-outfit font-bold text-sm-header2 text-dark mb-5">
                    {{ __('app.footer_support') }}
                </h6>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('contact') }}"
                           class="font-outfit font-light text-body-md text-dark/65
                                  hover:text-primary transition-colors">
                            {{ __('app.footer_help_center') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}"
                           class="font-outfit font-light text-body-md text-dark/65
                                  hover:text-primary transition-colors">
                            {{ __('app.footer_shipping_policy') }}
                        </a>
                    </li>
                    @auth
                        <li>
                            <a href="{{ route('orders.index') }}"
                               class="font-outfit font-light text-body-md text-dark/65
                                      hover:text-primary transition-colors">
                                {{ __('app.footer_track_order') }}
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('login') }}"
                               class="font-outfit font-light text-body-md text-dark/65
                                      hover:text-primary transition-colors">
                                {{ __('app.footer_track_order') }}
                            </a>
                        </li>
                    @endauth
                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <li>
                            <a href="{{ route('admin.index') }}"
                               class="font-outfit font-bold text-body-md text-secondary
                                      hover:text-primary transition-colors">
                                {{ __('app.footer_admin_access') }}
                            </a>
                        </li>
                    @else
                        <li>
                            <span class="font-outfit font-light text-body-md text-dark/30">
                                {{ __('app.footer_admin_access') }}
                            </span>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- ── Contacte ──────────────────────────────────────── --}}
            <div class="md:col-span-3">
                <h6 class="font-outfit font-bold text-sm-header2 text-dark mb-5">
                    {{ __('app.footer_contact') }}
                </h6>
                <ul class="space-y-3.5">

                    <li class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-primary shrink-0 mt-0.5" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <div class="font-outfit font-light text-body-md text-dark/65 leading-snug">
                            <a href="tel:+34937409228"
                               class="block hover:text-primary transition-colors">
                                +34 937 409 228
                            </a>
                            <a href="tel:+34695561180"
                               class="block hover:text-primary transition-colors">
                                +34 695 561 180
                            </a>
                        </div>
                    </li>

                    <li class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <a href="mailto:copyus@copyus.es"
                           class="font-outfit font-light text-body-md text-dark/65
                                  hover:text-primary transition-colors">
                            copyus@copyus.es
                        </a>
                    </li>

                    <li class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-primary shrink-0 mt-0.5" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <div>
                            <address class="not-italic font-outfit font-light text-body-md text-dark/65 leading-snug">
                                Parc TecnoCampus Mataró-Maresme, TCM3, Local 2<br>
                                Carrer d'Ernest Lluch, 32, 08302<br>
                                Mataró, Barcelona
                            </address>
                            <a href="https://www.google.com/maps/search/?api=1&query=Parc+TecnoCampus+Mataró"
                               target="_blank" rel="noopener"
                               class="inline-flex items-center gap-1 mt-2 font-outfit text-body-sm
                                      font-semibold text-secondary hover:text-primary transition-colors">
                                {{ __('app.home_visit_view_maps') }}
                            </a>
                        </div>
                    </li>
                </ul>
            </div>

        </div>
    </div>

    {{-- ── Bottom bar ─────────────────────────────────────────── --}}
    <div class="border-t border-dark/10">
        <div class="section py-5 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="font-outfit font-light text-body-sm text-dark/45">
                © {{ date('Y') }} {{ __('app.footer_brand_legal') }}. {{ __('app.footer_rights') }}
            </p>
            <div class="flex items-center gap-6">
                <a href="{{ route('privacy') }}" class="font-outfit font-light text-body-sm text-dark/45 hover:text-primary transition-colors">{{ __('app.footer_privacy') }}</a>
                <a href="{{ route('terms') }}" class="font-outfit font-light text-body-sm text-dark/45 hover:text-primary transition-colors">{{ __('app.footer_terms') }}</a>
                <a href="{{ route('cookies') }}" class="font-outfit font-light text-body-sm text-dark/45 hover:text-primary transition-colors">{{ __('app.footer_cookies') }}</a>
            </div>
        </div>
    </div>
</footer>
