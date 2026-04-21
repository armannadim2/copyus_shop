@extends('layouts.app')
@section('title', __('app.pending_approval'))

@section('content')
<div class="min-h-screen bg-light flex items-center justify-center py-12 px-4">
    <div class="max-w-lg w-full bg-white rounded-2xl shadow-lg p-10 text-center">

        {{-- Icon --}}
        <div class="flex items-center justify-center w-20 h-20 bg-yellow-50
                    rounded-full mx-auto mb-6">
            <span class="text-4xl">⏳</span>
        </div>

        <h1 class="font-alumni text-h3 text-dark mb-3">
            {{ __('app.pending_approval_title') }}
        </h1>
        <p class="font-outfit text-body-lg text-gray-500 mb-6">
            {{ __('app.pending_approval_message') }}
        </p>

        {{-- Info Card --}}
        <div class="bg-light rounded-xl p-4 mb-8 text-left space-y-2">
            <p class="font-outfit text-body-lg text-dark">
                🏢 <span class="font-semibold">{{ auth()->user()->company_name }}</span>
            </p>
            <p class="font-outfit text-body-lg text-dark">
                📧 <span class="font-semibold">{{ auth()->user()->email }}</span>
            </p>
            <p class="font-outfit text-body-lg text-dark">
                🪪 <span class="font-semibold">{{ auth()->user()->cif }}</span>
            </p>
        </div>

        {{-- Steps --}}
        <div class="text-left space-y-3 mb-8">
            <div class="flex items-start gap-3">
                <span class="w-6 h-6 bg-primary text-white rounded-full flex items-center
                             justify-center font-alumni text-body-sm flex-shrink-0 mt-0.5">1</span>
                <p class="font-outfit text-body-lg text-gray-600">
                    {{ __('app.pending_step_1') }}
                </p>
            </div>
            <div class="flex items-start gap-3">
                <span class="w-6 h-6 bg-gray-200 text-gray-500 rounded-full flex items-center
                             justify-center font-alumni text-body-sm flex-shrink-0 mt-0.5">2</span>
                <p class="font-outfit text-body-lg text-gray-400">
                    {{ __('app.pending_step_2') }}
                </p>
            </div>
            <div class="flex items-start gap-3">
                <span class="w-6 h-6 bg-gray-200 text-gray-500 rounded-full flex items-center
                             justify-center font-alumni text-body-sm flex-shrink-0 mt-0.5">3</span>
                <p class="font-outfit text-body-lg text-gray-400">
                    {{ __('app.pending_step_3') }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full border-2 border-primary text-primary font-alumni
                           text-sm-header py-3 rounded-lg hover:bg-primary
                           hover:text-white transition-all">
                {{ __('app.logout') }}
            </button>
        </form>

    </div>
</div>
@endsection
