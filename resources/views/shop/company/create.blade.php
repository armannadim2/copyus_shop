@extends('layouts.app')
@section('title', 'Crear empresa')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-16">
    <div class="text-center mb-10">
        <h1 class="font-alumni text-h2 text-dark">Crea la teva empresa</h1>
        <p class="font-outfit text-sm text-gray-400 mt-2">
            Centralitza les compres del teu equip sota un mateix compte empresarial.
        </p>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-2xl px-5 py-4 mb-6">
            @foreach($errors->all() as $error)
                <p class="font-outfit text-xs text-red-600">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm p-8">
        <form method="POST" action="{{ route('company.store') }}" class="space-y-5">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">Nom de l'empresa *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 font-outfit text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">CIF / NIF</label>
                    <input type="text" name="cif_vat" value="{{ old('cif_vat') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 font-outfit text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">Email empresa</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 font-outfit text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">Telèfon</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 font-outfit text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">Adreça</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 font-outfit text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">Ciutat</label>
                    <input type="text" name="city" value="{{ old('city') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 font-outfit text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="font-outfit text-xs font-semibold tracking-widest text-primary uppercase mb-1 block">Codi postal</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 font-outfit text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-primary text-white font-alumni text-sm-header py-3.5 rounded-2xl
                           hover:brightness-110 active:scale-95 transition-all">
                Crear empresa
            </button>
        </form>
    </div>
</div>
@endsection
