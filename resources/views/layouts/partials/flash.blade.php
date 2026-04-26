@php
    $flashes = collect([
        ['type' => 'success', 'message' => session('success'), 'icon' => '✓', 'classes' => 'bg-white border-green-500/30 text-green-700',  'iconBg' => 'bg-green-100 text-green-600'],
        ['type' => 'error',   'message' => session('error'),   'icon' => '✕', 'classes' => 'bg-white border-red-500/30 text-red-700',      'iconBg' => 'bg-red-100 text-red-600'],
        ['type' => 'warning', 'message' => session('warning'), 'icon' => '!', 'classes' => 'bg-white border-yellow-500/30 text-yellow-800', 'iconBg' => 'bg-yellow-100 text-yellow-700'],
    ])->filter(fn($f) => !empty($f['message']));
@endphp

@if($flashes->isNotEmpty())
    <div class="fixed top-24 right-4 sm:right-6 z-[60] flex flex-col gap-3 max-w-sm w-[calc(100%-2rem)]">
        @foreach($flashes as $flash)
            <div x-data="{ show: true }"
                 x-init="setTimeout(() => show = false, 6000)"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-6 scale-95"
                 x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-6"
                 role="alert"
                 class="flex items-start gap-3 {{ $flash['classes'] }}
                        border rounded-2xl shadow-[0_8px_24px_rgba(36,48,46,0.12)]
                        px-4 py-3 backdrop-blur-sm">

                <div class="w-8 h-8 rounded-full {{ $flash['iconBg'] }} flex items-center
                            justify-center font-alumni text-sm-header shrink-0">
                    {{ $flash['icon'] }}
                </div>

                <p class="font-outfit text-body-md leading-snug flex-1 pt-1">
                    {{ $flash['message'] }}
                </p>

                <button type="button"
                        @click="show = false"
                        class="text-dark/30 hover:text-dark/70 transition-colors shrink-0
                               leading-none text-lg pt-0.5"
                        aria-label="Tancar">
                    ×
                </button>
            </div>
        @endforeach
    </div>
@endif
