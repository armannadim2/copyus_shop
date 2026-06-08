<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@php
    $base = rtrim(config('app.url'), '/');
    $now  = now()->toAtomString();
@endphp

    {{-- ── Static pages ────────────────────────────────────────────────────── --}}

    <url>
        <loc>{{ $base }}/</loc>
        <lastmod>{{ $now }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>

    <url>
        <loc>{{ $base }}/serveis</loc>
        <lastmod>{{ $now }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>

    <url>
        <loc>{{ $base }}/papereria</loc>
        <lastmod>{{ $now }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>

    <url>
        <loc>{{ $base }}/impressio</loc>
        <lastmod>{{ $now }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>

    <url>
        <loc>{{ $base }}/demanar-pressupost</loc>
        <lastmod>{{ $now }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>

    <url>
        <loc>{{ $base }}/qui-som</loc>
        <lastmod>{{ $now }}</lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.6</priority>
    </url>

    <url>
        <loc>{{ $base }}/contacte</loc>
        <lastmod>{{ $now }}</lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.6</priority>
    </url>

    <url>
        <loc>{{ $base }}/products</loc>
        <lastmod>{{ $now }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>

    <url>
        <loc>{{ $base }}/privadesa</loc>
        <changefreq>yearly</changefreq>
        <priority>0.2</priority>
    </url>

    <url>
        <loc>{{ $base }}/termes</loc>
        <changefreq>yearly</changefreq>
        <priority>0.2</priority>
    </url>

    <url>
        <loc>{{ $base }}/cookies</loc>
        <changefreq>yearly</changefreq>
        <priority>0.2</priority>
    </url>

    {{-- ── Print template builder pages ───────────────────────────────────── --}}

    @foreach ($printTemplates as $template)
    <url>
        <loc>{{ $base }}/impressio/{{ $template->slug }}</loc>
        <lastmod>{{ $template->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach

    {{-- ── Product category pages ─────────────────────────────────────────── --}}

    @foreach ($categories as $category)
    <url>
        <loc>{{ $base }}/categories/{{ $category->slug }}</loc>
        <lastmod>{{ $category->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach

    {{-- ── Individual product pages ────────────────────────────────────────── --}}

    @foreach ($products as $product)
    <url>
        <loc>{{ $base }}/products/{{ $product->slug }}</loc>
        <lastmod>{{ $product->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
    </url>
    @endforeach

</urlset>
