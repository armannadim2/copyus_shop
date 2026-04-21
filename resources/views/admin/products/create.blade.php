@extends('layouts.admin')
@section('title', 'Nou Producte')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.products.index') }}"
           class="font-outfit text-sm text-gray-500 hover:text-primary transition-colors">
            ← Productes
        </a>
        <h1 class="font-alumni text-h1 text-dark">Nou Producte</h1>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-600 font-outfit text-sm px-4 py-3 rounded-2xl">
            <ul class="space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.products.store') }}"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf

        @php $product = null; @endphp
        @include('admin.products._form')

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-primary text-white font-alumni text-sm-header px-8 py-3
                           rounded-2xl hover:brightness-110 active:scale-95 transition-all">
                Crear producte
            </button>
            <a href="{{ route('admin.products.index') }}"
               class="font-outfit text-sm text-gray-500 px-6 py-3 rounded-2xl
                      border border-gray-200 hover:bg-gray-50 transition-colors">
                Cancel·lar
            </a>
        </div>
    </form>
</div>
@endsection
