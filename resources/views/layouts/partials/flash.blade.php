@if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-800
                font-outfit text-body-lg px-6 py-4" role="alert">
        ✅ {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border-l-4 border-primary text-red-800
                font-outfit text-body-lg px-6 py-4" role="alert">
        ❌ {{ session('error') }}
    </div>
@endif

@if(session('warning'))
    <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-800
                font-outfit text-body-lg px-6 py-4" role="alert">
        ⚠️ {{ session('warning') }}
    </div>
@endif
