@extends('layouts.admin')
@section('title', 'Empreses')

@section('content')
<div class="p-8 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="font-alumni text-h4 text-dark">Empreses</h1>
    </div>

    <form method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cercar per nom o CIF…"
               class="border border-gray-200 rounded-xl px-4 py-2 font-outfit text-sm focus:outline-none focus:ring-2 focus:ring-primary w-72">
        <button type="submit"
                class="bg-dark text-white font-outfit text-sm px-4 py-2 rounded-xl hover:bg-primary transition-colors">
            Cercar
        </button>
    </form>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        @if($companies->isEmpty())
            <div class="py-16 text-center">
                <p class="font-outfit text-sm text-gray-400">Cap empresa registrada.</p>
            </div>
        @else
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="font-outfit text-xs font-semibold tracking-widest text-gray-400 uppercase text-left px-6 py-3">Empresa</th>
                        <th class="font-outfit text-xs font-semibold tracking-widest text-gray-400 uppercase text-center px-4 py-3">Membres</th>
                        <th class="font-outfit text-xs font-semibold tracking-widest text-gray-400 uppercase text-left px-4 py-3">Terminis pagament</th>
                        <th class="font-outfit text-xs font-semibold tracking-widest text-gray-400 uppercase text-right px-4 py-3">Límit crèdit</th>
                        <th class="font-outfit text-xs font-semibold tracking-widest text-gray-400 uppercase text-center px-4 py-3">Estat</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($companies as $company)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-outfit text-sm font-semibold text-dark">{{ $company->name }}</p>
                            <p class="font-outfit text-xs text-gray-400">{{ $company->cif_vat }}</p>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="font-outfit text-sm text-dark">{{ $company->members_count }}</span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="font-outfit text-sm text-dark">{{ $company->payment_terms_label }}</span>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <span class="font-outfit text-sm text-dark">
                                {{ $company->credit_limit > 0 ? number_format($company->credit_limit, 2, ',', '.') . ' €' : '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="font-outfit text-xs px-2.5 py-1 rounded-full {{ $company->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                                {{ $company->is_active ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <a href="{{ route('admin.companies.show', $company) }}"
                               class="font-outfit text-xs text-primary hover:underline">Gestionar</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($companies->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">{{ $companies->links() }}</div>
            @endif
        @endif
    </div>
</div>
@endsection
