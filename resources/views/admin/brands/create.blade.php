@extends('layouts.admin')
@section('title', 'Nova Marca')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('admin.brands.index') }}" class="text-gray-400 hover:text-primary transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="font-alumni text-h4 text-dark">Nova Marca</h1>
    </div>

    <form method="POST" action="{{ route('admin.brands.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="bg-white rounded-2xl shadow-sm p-6 sm:p-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-outfit text-sm font-medium text-gray-700 mb-2">Nom *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2 font-outfit text-sm
                                  focus:outline-none focus:ring-2 focus:ring-primary">
                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-outfit text-sm font-medium text-gray-700 mb-2">Slug *</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2 font-outfit text-sm
                                  focus:outline-none focus:ring-2 focus:ring-primary">
                    @error('slug') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block font-outfit text-sm font-medium text-gray-700 mb-2">Descripció (Català)</label>
                <textarea name="description[ca]" rows="3"
                          class="w-full border border-gray-200 rounded-xl px-4 py-2 font-outfit text-sm
                                 focus:outline-none focus:ring-2 focus:ring-primary">{{ old('description.ca') }}</textarea>
            </div>

            <div>
                <label class="block font-outfit text-sm font-medium text-gray-700 mb-2">Descripció (Castellà)</label>
                <textarea name="description[es]" rows="3"
                          class="w-full border border-gray-200 rounded-xl px-4 py-2 font-outfit text-sm
                                 focus:outline-none focus:ring-2 focus:ring-primary">{{ old('description.es') }}</textarea>
            </div>

            <div>
                <label class="block font-outfit text-sm font-medium text-gray-700 mb-2">Descripció (Anglès)</label>
                <textarea name="description[en]" rows="3"
                          class="w-full border border-gray-200 rounded-xl px-4 py-2 font-outfit text-sm
                                 focus:outline-none focus:ring-2 focus:ring-primary">{{ old('description.en') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-outfit text-sm font-medium text-gray-700 mb-2">Ordre *</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" required min="0"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2 font-outfit text-sm
                                  focus:outline-none focus:ring-2 focus:ring-primary">
                    @error('sort_order') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-outfit text-sm font-medium text-gray-700 mb-2">Imatge</label>
                    <input type="file" name="image" accept="image/*"
                           class="block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-primary file:text-white
                                  hover:file:bg-primary/90">
                    @error('image') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', true))
                       class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary">
                <label for="is_active" class="font-outfit text-sm text-gray-700">Marca Activa</label>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-primary text-white font-alumni text-lg px-8 py-3 rounded-2xl hover:brightness-110 active:scale-95 transition-all">
                Guardar Marca
            </button>
        </div>
    </form>
</div>
@endsection
