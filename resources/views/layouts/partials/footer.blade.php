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
