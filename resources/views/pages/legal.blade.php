@extends('layouts.app')
@section('title', $pageTitle)

@section('content')

    {{-- ── Hero ─────────────────────────────────────────────────────────────── --}}
    <section class="relative bg-dark text-white -mt-16 lg:-mt-20 pt-32 lg:pt-40 pb-20
                    overflow-hidden">

        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-32 -right-32 w-[500px] h-[500px] rounded-full
                        bg-primary opacity-10 blur-3xl"></div>
            <div class="absolute -bottom-20 -left-32 w-[420px] h-[420px] rounded-full
                        bg-secondary opacity-10 blur-3xl"></div>
        </div>

        <div class="section relative text-center max-w-3xl mx-auto">
            <span class="inline-flex items-center gap-2 bg-white/10 rounded-full
                         px-4 py-1.5 mb-6 font-outfit text-body-md text-white/70">
                <span class="w-2 h-2 rounded-full bg-primary"></span>
                Legal
            </span>

            <h1 class="font-alumni italic text-h1 text-white"
                style="line-height: 1.05; letter-spacing: 0.01em;">
                {{ $pageTitle }}
            </h1>

            <p class="font-outfit font-light text-body-lg text-white/75 mt-5">
                {{ $pageLead }}
            </p>

            <p class="font-outfit text-body-sm text-white/45 mt-6">
                {{ $updatedLabel }}: <span class="text-white/65">{{ $updatedDate }}</span>
            </p>
        </div>
    </section>

    {{-- ── Body ─────────────────────────────────────────────────────────────── --}}
    <section class="bg-light py-16 md:py-20">
        <div class="section max-w-3xl mx-auto">

            <div class="bg-white rounded-3xl border border-dark/5 p-8 md:p-12
                        shadow-[0_4px_24px_rgba(36,48,46,0.04)]">

                @foreach($sections as $section)
                    <div class="@if(!$loop->first) mt-10 pt-10 border-t border-dark/5 @endif">

                        <h2 class="font-alumni text-h4 text-dark mb-4">
                            {{ $section['title'] }}
                        </h2>

                        @if(isset($section['body']))
                            @if(is_array($section['body']))
                                @foreach($section['body'] as $para)
                                    <p class="font-outfit font-light text-body-lg text-dark/75
                                              leading-relaxed mb-3 last:mb-0">
                                        {{ $para }}
                                    </p>
                                @endforeach
                            @else
                                <p class="font-outfit font-light text-body-lg text-dark/75
                                          leading-relaxed">
                                    {{ $section['body'] }}
                                </p>
                            @endif
                        @endif

                        @if(isset($section['list']))
                            <ul class="mt-3 space-y-2 font-outfit font-light text-body-lg text-dark/75
                                       leading-relaxed">
                                @foreach($section['list'] as $item)
                                    <li class="flex gap-3">
                                        <span class="mt-2 w-1.5 h-1.5 rounded-full bg-primary shrink-0"></span>
                                        <span>{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach

            </div>

            {{-- Cross-links --}}
            <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
                @php
                    $legalLinks = [
                        'privacy' => __('legal.privacy.page_title'),
                        'terms'   => __('legal.terms.page_title'),
                        'cookies' => __('legal.cookies.page_title'),
                    ];
                @endphp
                @foreach($legalLinks as $key => $label)
                    <a href="{{ route($key) }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full
                              border border-dark/15 font-outfit text-body-md
                              transition-colors
                              {{ $type === $key
                                  ? 'bg-dark text-white border-dark'
                                  : 'text-dark/70 hover:text-primary hover:border-primary/40' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

@endsection
