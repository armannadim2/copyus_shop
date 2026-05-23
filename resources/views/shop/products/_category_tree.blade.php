{{-- Recursive category tree node --}}
{{-- Required vars: $nodes (Collection), $openIds (int[]), $activeSlug (?string) --}}
@foreach($nodes as $cat)
    @php
        $isActive    = $activeSlug === $cat->slug;
        $isAncestor  = in_array($cat->id, $openIds);
        $hasChildren = $cat->subtree->isNotEmpty();
    @endphp
    <li x-data="{ open: {{ ($isActive || $isAncestor) ? 'true' : 'false' }} }">

        <div class="flex items-center gap-0.5">
            <a href="{{ route('products.index', ['category' => $cat->slug]) }}"
               class="flex-1 flex items-center justify-between font-outfit text-sm
                      rounded-lg px-3 py-2 transition-colors leading-snug
                      {{ $isActive
                         ? 'bg-primary text-white font-medium'
                         : ($isAncestor
                            ? 'text-primary font-medium hover:bg-light'
                            : 'text-dark hover:bg-light hover:text-primary') }}">
                <span>{{ $cat->getTranslation('name', app()->getLocale()) }}</span>
                <span class="text-xs opacity-50 shrink-0 ml-1">({{ $cat->total_count }})</span>
            </a>

            @if($hasChildren)
                <button type="button" @click="open = !open"
                        class="flex items-center justify-center w-7 h-7 rounded-lg flex-shrink-0
                               transition-colors text-gray-400 hover:text-primary hover:bg-light">
                    <svg class="w-3 h-3 transition-transform duration-200"
                         :class="open ? 'rotate-180' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            @endif
        </div>

        @if($hasChildren)
            <ul x-show="open"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1"
                class="mt-0.5 ml-3 pl-2 border-l-2 border-gray-100 space-y-0.5 pb-1"
                style="display:none">
                @include('shop.products._category_tree', [
                    'nodes'      => $cat->subtree,
                    'openIds'    => $openIds,
                    'activeSlug' => $activeSlug,
                ])
            </ul>
        @endif
    </li>
@endforeach
