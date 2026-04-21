@extends('layouts.app')
@section('title', __('app.account_rejected'))

@section('content')
<div class="min-h-screen bg-light flex items-center justify-center py-12 px-4">
    <div class="max-w-lg w-full bg-white rounded-2xl shadow-lg p-10 text-center">

        <div class="flex items-center justify-center w-20 h-20 bg-red-50
                    rounded-full mx-auto mb-6">
            <span class="text-4xl">❌</span>
        </div>

        <h1 class="font-alumni text-h3 text-dark mb-3">
            {{ __('app.account_rejected_title') }}
        </h1>
        <p class="font-outfit text-body-lg text-gray-500 mb-4">
            {{ __('app.account_rejected_message') }}
        </p>

        @if(auth()->user()->rejection_reason)
            <div class="bg-red-50 border-l-4 border-primary rounded-lg p-4 mb-6 text-left">
                <p class="font-alumni text-sm-header text-dark mb-1">
                    {{ __('app.rejection_reason') }}:
                </p>
                <p class="font-outfit text-body-lg text-gray-600">
                    {{ auth()->user()->rejection_reason }}
                </p>
            </div>
        @endif

        <a href="mailto:info@copyus.es"
           class="inline-block w-full bg-primary text-white font-alumni
                  text-sm-header py-3 rounded-lg hover:bg-opacity-90
                  transition-all mb-3">
            📧 {{ __('app.contact_us') }}
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full border-2 border-gray-300 text-gray-500 font-alumni
                           text-sm-header py-3 rounded-lg hover:border-primary
                           hover:text-primary transition-all">
                {{ __('app.logout') }}
            </button>
        </form>

    </div>
</div>
@endsection
