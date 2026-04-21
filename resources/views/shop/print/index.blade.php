@extends('layouts.app')
@section('title', __('app.print_page_title'))

@section('content')

{{-- Hero --}}
<div class="text-center pt-16 pb-12 px-4 bg-gradient-to-b from-white to-light/50">
    <div class="inline-flex items-center gap-2 bg-primary/10 rounded-full px-4 py-1.5 mb-5">
        <span class="text-primary text-sm">🖨️</span>
        <span class="font-outfit text-xs font-semibold text-primary uppercase tracking-widest">{{ __('app.print_badge') }}</span>
    </div>
    <h1 class="font-alumni text-h1 text-dark leading-tight mb-4">
        {{ __('app.print_hero_title') }}<br>
        <span class="text-primary">{{ __('app.print_hero_highlight') }}</span>
    </h1>
    <p class="font-outfit text-body-lg text-gray-500 max-w-xl mx-auto">
        {{ __('app.print_hero_subtitle') }}
    </p>
    @auth
    <div class="mt-6">
        <a href="{{ route('print.my-jobs') }}"
           class="inline-flex items-center gap-2 font-outfit text-sm text-primary hover:underline transition-colors">
            {{ __('app.print_my_jobs') }}
        </a>
    </div>
    @endauth
</div>

<div class="max-w-6xl mx-auto px-4 sm:px-6 pb-24">

    @if($templates->isEmpty())
        <div class="text-center py-20">
            <p class="text-4xl mb-4">🖨️</p>
            <p class="font-outfit text-sm text-gray-400">
                {{ __('app.print_empty') }}
            </p>
        </div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
            @foreach($templates as $template)
                @php
                    $locale      = app()->getLocale();
                    $fallbacks   = [$locale, 'ca', 'es', 'en'];
                    $name        = collect($fallbacks)
                                    ->map(fn($l) => $template->getTranslation('name', $l, false))
                                    ->first(fn($v) => !empty(trim((string) $v)))
                                    ?? $template->slug;
                    $description = collect($fallbacks)
                                    ->map(fn($l) => $template->getTranslation('description', $l, false))
                                    ->first(fn($v) => !empty(trim((string) $v)));
                @endphp
                <a href="{{ route('print.builder', $template->slug) }}"
                   class="group bg-white rounded-3xl border border-gray-100 p-6
                          hover:border-primary/30 hover:shadow-lg
                          transition-all duration-300 flex flex-col"
                   style="min-height: 220px;">

                    {{-- Icon + name --}}
                    <div class="flex items-start gap-3 mb-3">
                        <div class="rounded-2xl flex items-center justify-center shrink-0"
                             style="width:48px; height:48px; background:rgba(242,96,82,0.08);">
                            <svg class="text-primary" style="width:24px; height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                        </div>
                        <div style="min-width:0; flex:1;">
                            <h2 class="font-alumni text-dark group-hover:text-primary transition-colors"
                                style="font-size:1.5rem; line-height:1.3; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                {{ $name }}
                            </h2>
                            <p class="font-outfit text-gray-400" style="font-size:0.75rem; margin-top:2px;">
                                {{ $template->options->count() }} {{ __('app.print_options') }} · {{ $template->base_production_days }}d {{ __('app.print_production') }}
                            </p>
                        </div>
                    </div>

                    {{-- Description — flex-1 pushes price section to the bottom --}}
                    <p class="font-outfit text-gray-500" style="font-size:0.875rem; line-height:1.6; flex:1; margin-bottom:1rem;">
                        {{ $description ?? '' }}
                    </p>

                    {{-- Price hint --}}
                    <div class="flex items-center justify-between" style="padding-top:1rem; border-top:1px solid #f3f4f6;">
                        <div>
                            @auth
                                <p class="font-outfit text-gray-400" style="font-size:0.75rem;">{{ __('app.print_from') }}</p>
                                <p class="font-alumni text-primary" style="font-size:1.2rem; line-height:1;">
                                    {{ number_format($template->base_price, 4, ',', '.') }} €
                                    <span class="font-outfit text-gray-400" style="font-size:0.7rem; font-weight:400;">{{ __('app.print_per_unit_vat') }}</span>
                                </p>
                            @else
                                <p class="font-outfit text-gray-400" style="font-size:0.75rem;">
                                    <span class="text-primary" style="font-weight:600;">{{ __('app.login') }}</span>
                                    {{ __('app.print_login_to_see') }}
                                </p>
                            @endauth
                        </div>
                        <span class="inline-flex items-center gap-1 font-outfit font-semibold bg-primary text-white
                                     group-hover:brightness-110 transition-all"
                              style="font-size:0.75rem; padding:0.4rem 1rem; border-radius:0.75rem; flex-shrink:0;">
                            {{ __('app.print_configure') }}
                            <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Benefits strip --}}
        <div class="mt-16 grid sm:grid-cols-3 gap-6">
            @foreach([
                ['🚀', __('app.print_benefit_1_title'), __('app.print_benefit_1_desc')],
                ['📦', __('app.print_benefit_2_title'), __('app.print_benefit_2_desc')],
                ['✅', __('app.print_benefit_3_title'), __('app.print_benefit_3_desc')],
            ] as [$icon, $title, $desc])
                <div class="flex items-start gap-4 bg-white rounded-2xl p-6 border border-gray-100">
                    <span class="text-3xl">{{ $icon }}</span>
                    <div>
                        <p class="font-alumni text-h6 text-dark">{{ $title }}</p>
                        <p class="font-outfit text-xs text-gray-400 mt-1 leading-relaxed">{{ $desc }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

@endsection
