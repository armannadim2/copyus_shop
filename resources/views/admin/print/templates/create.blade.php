@extends('layouts.admin')
@section('title', 'Nova plantilla d\'impressió')

@section('content')
<div class="p-8 max-w-5xl space-y-6">

    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('admin.print.templates.index') }}"
           class="font-outfit text-xs text-gray-400 hover:text-primary transition-colors">
            ← Plantilles
        </a>
        <span class="text-gray-300">/</span>
        <span class="font-outfit text-xs text-dark">Nova plantilla</span>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-4">
            <ul class="space-y-1">
                @foreach($errors->all() as $error)
                    <li class="font-outfit text-xs text-red-600">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('admin.print.templates.store') }}"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf
        @include('admin.print.templates._form')
        <div class="flex gap-3">
            <button type="submit"
                    class="bg-primary text-white font-alumni text-sm-header
                           px-8 py-3 rounded-2xl hover:brightness-110 transition-all">
                Crear plantilla
            </button>
            <a href="{{ route('admin.print.templates.index') }}"
               class="border-2 border-gray-200 text-gray-500 font-alumni text-sm-header
                      px-6 py-3 rounded-2xl hover:border-gray-400 transition-all">
                Cancel·lar
            </a>
        </div>
    </form>

</div>
@endsection
