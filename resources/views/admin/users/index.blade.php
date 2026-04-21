@extends('layouts.admin')
@section('title', 'Gestió d\'Usuaris')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center justify-between mb-8">
        <h1 class="font-alumni text-h1 text-dark">Usuaris</h1>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.users.index') }}"
          class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cerca nom, email, empresa, CIF..."
               class="border border-gray-200 rounded-xl px-4 py-2 font-outfit text-body-sm
                      focus:outline-none focus:ring-2 focus:ring-primary w-72">
        <select name="role"
                class="border border-gray-200 rounded-xl px-4 py-2 font-outfit text-body-sm
                       focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">Tots els rols</option>
            <option value="approved" @selected(request('role') === 'approved')>Aprovats</option>
            <option value="pending"  @selected(request('role') === 'pending')>Pendents</option>
            <option value="rejected"     @selected(request('role') === 'rejected')>Rebutjats</option>
        </select>
        <button type="submit"
                class="bg-primary text-white font-outfit text-body-sm px-5 py-2
                       rounded-xl hover:bg-primary/90 transition-colors">
            Filtrar
        </button>
        @if(request('search') || request('role'))
            <a href="{{ route('admin.users.index') }}"
               class="font-outfit text-body-sm text-gray-500 px-4 py-2 rounded-xl
                      border border-gray-200 hover:bg-gray-50 transition-colors">
                Netejar
            </a>
        @endif
    </form>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left font-outfit text-body-sm text-gray-500 px-6 py-3">Nom</th>
                    <th class="text-left font-outfit text-body-sm text-gray-500 px-6 py-3">Empresa</th>
                    <th class="text-left font-outfit text-body-sm text-gray-500 px-6 py-3">Email</th>
                    <th class="text-left font-outfit text-body-sm text-gray-500 px-6 py-3">Rol</th>
                    <th class="text-left font-outfit text-body-sm text-gray-500 px-6 py-3">Alta</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                    @php
                        $roleColors = [
                            'approved' => 'bg-green-50 text-green-700',
                            'pending'  => 'bg-yellow-50 text-yellow-700',
                            'rejected'     => 'bg-red-50 text-red-600',
                        ];
                        $roleLabels = [
                            'approved' => 'Aprovat',
                            'pending'  => 'Pendent',
                            'rejected'     => 'Rebutjat',
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-outfit text-body-sm text-dark">{{ $user->name }}</td>
                        <td class="px-6 py-4 font-outfit text-body-sm text-gray-600">{{ $user->company_name }}</td>
                        <td class="px-6 py-4 font-outfit text-body-sm text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-block font-outfit text-body-sm px-2 py-0.5 rounded-full
                                         {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-500' }}">
                                {{ $roleLabels[$user->role] ?? $user->role }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-outfit text-body-sm text-gray-500">
                            {{ $user->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.users.show', $user->id) }}"
                               class="font-outfit text-body-sm text-secondary hover:text-primary transition-colors">
                                Veure →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center font-outfit text-body-lg text-gray-400">
                            No s'han trobat usuaris.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>

</div>
@endsection
