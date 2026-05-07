{{--
  Sortable table header cell.
  Required vars (pass via @include):
    $thCol   - DB column name (or alias like 'name_ca')
    $thLabel - Display text
  Optional:
    $thAlign - 'left' | 'right' | 'center' (default: 'left')
    $thClass - extra CSS classes on the <th>
  View-scope vars expected (passed from controller):
    $sort    - current sort column
    $dir     - current sort direction ('asc'|'desc')
--}}
@php
    $thAlign  = $thAlign ?? 'left';
    $thNext   = ($sort === $thCol && $dir === 'asc') ? 'desc' : 'asc';
    $isActive = $sort === $thCol;
@endphp
<th class="text-{{ $thAlign }} font-outfit text-xs font-semibold tracking-widest text-gray-400 uppercase px-6 py-3 {{ $thClass ?? '' }}">
    <a href="{{ request()->fullUrlWithQuery(['sort' => $thCol, 'direction' => $thNext, 'page' => null]) }}"
       class="inline-flex items-center gap-1 whitespace-nowrap hover:text-gray-600 transition-colors {{ $isActive ? 'text-primary' : '' }}">
        {{ $thLabel }}
        @if($isActive)
            <span>{{ $dir === 'asc' ? '↑' : '↓' }}</span>
        @else
            <span class="text-gray-300">↕</span>
        @endif
    </a>
</th>
