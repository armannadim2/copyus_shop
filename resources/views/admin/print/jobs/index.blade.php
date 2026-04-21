@extends('layouts.admin')
@section('title', 'Treballs d\'impressió')

@section('content')
<div class="p-8 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-alumni text-h4 text-dark">Treballs d'impressió</h1>
            <p class="font-outfit text-xs text-gray-400 mt-0.5">Gestiona les comandes d'impressió dels clients</p>
        </div>
    </div>

    {{-- Status tabs --}}
    @php
        $statuses = [
            'all'           => ['label' => 'Tots',          'color' => 'gray'],
            'ordered'       => ['label' => 'Encarregats',   'color' => 'yellow'],
            'in_production' => ['label' => 'En producció',  'color' => 'orange'],
            'completed'     => ['label' => 'Completats',    'color' => 'green'],
            'cancelled'     => ['label' => 'Cancel·lats',   'color' => 'red'],
        ];
        $activeStatus = request('status', 'all');
    @endphp
    <div class="flex flex-wrap gap-2">
        @foreach($statuses as $key => $meta)
            <a href="{{ route('admin.print.jobs.index', array_merge(request()->except('status', 'page'), $key !== 'all' ? ['status' => $key] : [])) }}"
               class="inline-flex items-center gap-2 font-outfit text-xs font-semibold px-4 py-2 rounded-xl border-2 transition-all
                      {{ $activeStatus === $key || ($key === 'all' && !request('status'))
                          ? 'bg-dark text-white border-dark'
                          : 'bg-white text-gray-500 border-gray-200 hover:border-gray-400' }}">
                {{ $meta['label'] }}
                @if($key !== 'all' && isset($counts[$key]))
                    <span class="bg-white/20 text-inherit px-1.5 py-0.5 rounded-full text-xs">{{ $counts[$key] }}</span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- Search --}}
    <form method="GET" class="flex gap-2">
        @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cercar per client o empresa…"
               class="border border-gray-200 rounded-xl px-4 py-2 font-outfit text-sm
                      focus:outline-none focus:ring-2 focus:ring-primary w-72">
        <button type="submit"
                class="bg-dark text-white font-outfit text-sm px-4 py-2 rounded-xl hover:bg-primary transition-colors">
            Cercar
        </button>
        @if(request('search'))
            <a href="{{ route('admin.print.jobs.index', request()->except('search', 'page')) }}"
               class="font-outfit text-sm text-gray-400 hover:text-primary px-4 py-2 rounded-xl border border-gray-200 transition-colors">
                Netejar
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div x-data="{ selected: [], allChecked: false }" class="space-y-3">

        {{-- Bulk action bar (visible when rows selected) --}}
        <div x-show="selected.length > 0"
             x-transition
             class="flex items-center gap-3 bg-primary/10 border border-primary/20 rounded-2xl px-5 py-3"
             style="display:none">
            <span class="font-outfit text-xs text-primary font-semibold"
                  x-text="selected.length + ' treball(s) seleccionat(s)'"></span>
            <form method="POST" action="{{ route('admin.print.jobs.bulk-status') }}"
                  class="flex items-center gap-2 ml-auto"
                  @submit.prevent="
                      $el.querySelectorAll('input[name=\'job_ids[]\']').forEach(e => e.remove());
                      selected.forEach(id => {
                          const i = document.createElement('input');
                          i.type = 'hidden'; i.name = 'job_ids[]'; i.value = id;
                          $el.appendChild(i);
                      });
                      $el.submit();
                  ">
                @csrf
                <select name="status"
                        class="border border-gray-200 rounded-xl px-3 py-1.5 font-outfit text-xs
                               focus:outline-none focus:ring-2 focus:ring-primary/40">
                    <option value="in_production">→ En producció</option>
                    <option value="completed">→ Completat</option>
                    <option value="cancelled">→ Cancel·lat</option>
                </select>
                <button type="submit"
                        class="bg-primary text-white font-outfit text-xs px-4 py-1.5 rounded-xl
                               hover:brightness-110 transition-all">
                    Aplicar
                </button>
                <button type="button" @click="selected = []; allChecked = false"
                        class="font-outfit text-xs text-gray-400 hover:text-dark transition-colors px-2 py-1.5">
                    Netejar
                </button>
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            @if($jobs->isEmpty())
                <div class="py-16 text-center">
                    <p class="text-4xl mb-3">📋</p>
                    <p class="font-outfit text-sm text-gray-400">Cap treball d'impressió trobat.</p>
                </div>
            @else
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 w-8">
                                <input type="checkbox"
                                       x-model="allChecked"
                                       @change="selected = allChecked ? [{{ $jobs->pluck('id')->join(',') }}] : []"
                                       class="rounded accent-primary">
                            </th>
                            <th class="font-outfit text-xs font-semibold tracking-widest text-gray-400 uppercase text-left px-4 py-3">Treball</th>
                            <th class="font-outfit text-xs font-semibold tracking-widest text-gray-400 uppercase text-left px-4 py-3">Client</th>
                            <th class="font-outfit text-xs font-semibold tracking-widest text-gray-400 uppercase text-center px-4 py-3">Quantitat</th>
                            <th class="font-outfit text-xs font-semibold tracking-widest text-gray-400 uppercase text-right px-4 py-3">Total</th>
                            <th class="font-outfit text-xs font-semibold tracking-widest text-gray-400 uppercase text-center px-4 py-3">Estat</th>
                            <th class="font-outfit text-xs font-semibold tracking-widest text-gray-400 uppercase text-center px-4 py-3">Arxiu</th>
                            <th class="font-outfit text-xs font-semibold tracking-widest text-gray-400 uppercase text-left px-4 py-3">Data</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($jobs as $job)
                        @php
                            $statusBadge = match($job->status) {
                                'ordered'       => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                'in_production' => 'bg-orange-50 text-orange-700 border-orange-200',
                                'completed'     => 'bg-green-50 text-green-700 border-green-200',
                                'cancelled'     => 'bg-red-50 text-red-600 border-red-200',
                                default         => 'bg-gray-100 text-gray-500 border-gray-200',
                            };
                            $statusLabel = match($job->status) {
                                'ordered'       => 'Encarregat',
                                'in_production' => 'En producció',
                                'completed'     => 'Completat',
                                'cancelled'     => 'Cancel·lat',
                                default         => $job->status,
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors"
                            :class="selected.includes({{ $job->id }}) ? 'bg-primary/5' : ''">
                            <td class="px-4 py-4">
                                <input type="checkbox"
                                       value="{{ $job->id }}"
                                       x-model="selected"
                                       :value="{{ $job->id }}"
                                       class="rounded accent-primary">
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-xl">{{ $job->template->icon ?? '🖨️' }}</span>
                                    <div>
                                        <p class="font-outfit text-sm font-semibold text-dark">
                                            {{ $job->template->getTranslation('name', 'ca') }}
                                        </p>
                                        <p class="font-outfit text-xs text-gray-400">#{{ $job->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-outfit text-sm text-dark">{{ $job->user?->name }}</p>
                                <p class="font-outfit text-xs text-gray-400">{{ $job->user?->company_name }}</p>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="font-outfit text-sm text-dark">{{ number_format($job->quantity, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <span class="font-alumni text-sm-header text-primary">
                                    {{ number_format($job->total_price * (1 + ($job->template->vat_rate ?? 21) / 100), 2, ',', '.') }} €
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="font-outfit text-xs px-2.5 py-1 rounded-full border {{ $statusBadge }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                @if($job->artwork_path)
                                    <span class="text-green-500 text-lg" title="Arxiu carregat">✓</span>
                                @else
                                    <span class="text-gray-300 text-lg" title="Sense arxiu">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-outfit text-xs text-gray-500">{{ $job->created_at->format('d/m/Y') }}</p>
                                @if($job->expected_delivery_at)
                                    <p class="font-outfit text-xs text-primary">→ {{ $job->expected_delivery_at->format('d/m/Y') }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-right">
                                <a href="{{ route('admin.print.jobs.show', $job) }}"
                                   class="font-outfit text-xs text-primary hover:underline">
                                    Gestionar
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($jobs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $jobs->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

</div>
@endsection
