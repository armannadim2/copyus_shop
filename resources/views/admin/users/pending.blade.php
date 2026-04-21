@extends('layouts.admin')
@section('title', 'Usuaris Pendents')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <h1 class="font-alumni text-h1 text-dark mb-8">Usuaris Pendents d'Aprovació</h1>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left font-outfit text-body-sm text-gray-500 px-6 py-3">Nom</th>
                    <th class="text-left font-outfit text-body-sm text-gray-500 px-6 py-3">Empresa</th>
                    <th class="text-left font-outfit text-body-sm text-gray-500 px-6 py-3">CIF</th>
                    <th class="text-left font-outfit text-body-sm text-gray-500 px-6 py-3">Registrat</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-outfit text-body-sm text-dark">{{ $user->name }}</td>
                        <td class="px-6 py-4 font-outfit text-body-sm text-gray-600">{{ $user->company_name }}</td>
                        <td class="px-6 py-4 font-outfit text-body-sm text-gray-600">{{ $user->cif }}</td>
                        <td class="px-6 py-4 font-outfit text-body-sm text-gray-500">
                            {{ $user->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.users.show', $user->id) }}"
                               class="font-outfit text-body-sm text-secondary hover:text-primary transition-colors">
                                Gestionar →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center font-outfit text-body-lg text-gray-400">
                            No hi ha usuaris pendents d'aprovació.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>

</div>
@endsection
