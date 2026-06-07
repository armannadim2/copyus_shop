<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@hasSection('full_title')@yield('full_title')@else{{ config('app.name', 'Copyus') }} — @yield('title', __('app.welcome'))@endif</title>
    @if(trim($__env->yieldContent('meta_description')))
    <meta name="description" content="@yield('meta_description')">
    @endif

    {{-- hreflang --}}
    @php $appUrl = rtrim(config('app.url'), '/'); @endphp
    <link rel="alternate" hreflang="ca" href="{{ $appUrl }}" />
    <link rel="alternate" hreflang="es" href="{{ $appUrl }}/locale/es" />
    <link rel="alternate" hreflang="en" href="{{ $appUrl }}/locale/en" />
    <link rel="alternate" hreflang="x-default" href="{{ $appUrl }}" />

    {{-- LocalBusiness schema --}}
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "LocalBusiness",
      "name": "Copyus",
      "image": "{{ $appUrl }}/assets/images/logo/FULL%20LOGO%20(Red%20Sun).svg",
      "url": "{{ $appUrl }}",
      "telephone": "+34937409228",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Parc TecnoCampus Mataró",
        "addressLocality": "Mataró",
        "addressRegion": "Barcelona",
        "postalCode": "08302",
        "addressCountry": "ES"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": 41.5432,
        "longitude": 2.4459
      },
      "priceRange": "€€",
      "description": "Copisteria i impressió digital a Mataró. Flyers, targetes, banners, enquadernació i marxandatge personalitzat.",
      "areaServed": ["Mataró", "Maresme", "Barcelona"]
    }
    </script>

    @stack('meta')

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Alumni+Sans:ital,wght@0,100..900;1,100..900&family=Outfit:wght@100..900&display=swap" rel="stylesheet" />

    <link rel="icon" href="{{ asset('images/icon.png') }}" type="image/png">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icons/favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/icons/favicon-16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/icons/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-light font-outfit text-dark antialiased min-h-screen">

    {{-- Navigation --}}
    @include('layouts.partials.navbar')

    {{-- Flash Messages --}}
    @include('layouts.partials.flash')

    {{-- Page Content --}}
    {{-- pt-16/pt-20 compensates for the fixed navbar on non-hero pages.
         Pages with a full-bleed hero section (home) should use -mt-16/-mt-20
         on their first section to cancel this offset. --}}
    <main class="min-h-screen pt-16 lg:pt-20">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('layouts.partials.footer')

    @stack('scripts')
    <script>
    function navSearch() {
        return {
            q: '',
            results: [],
            activeIdx: -1,
            async search() {
                if (this.q.length < 2) { this.results = []; return; }
                const res = await fetch(`{{ route('search.autocomplete') }}?q=${encodeURIComponent(this.q)}`);
                this.results   = await res.json();
                this.activeIdx = -1;
            },
            moveDown() { if (this.activeIdx < this.results.length - 1) this.activeIdx++; },
            moveUp()   { if (this.activeIdx > 0) this.activeIdx--; },
            submitSearch() {
                if (this.activeIdx >= 0 && this.results[this.activeIdx]) {
                    window.location.href = this.results[this.activeIdx].url;
                } else if (this.q.length >= 2) {
                    window.location.href = `{{ route('search') }}?q=${encodeURIComponent(this.q)}`;
                }
            },
        };
    }
    </script>
</body>
</html>
