<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Admin · @yield('title', 'Copyus') · COPYUS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-light font-outfit antialiased">

    <div class="flex h-full">

        {{-- ── Sidebar ── --}}
        <aside class="w-64 bg-dark flex-shrink-0 flex flex-col min-h-screen
                       fixed top-0 left-0 bottom-0 z-40">

            {{-- Brand --}}
            <div class="px-6 py-6 border-b border-white border-opacity-10">
                <a href="{{ route('admin.index') }}"
                   class="font-alumni text-h3 text-white tracking-tight">
                    <a href="{{ route('home') }}" class="shrink-0">
                        <img src="{{ asset('assets/images/logo/' . rawurlencode('FULL LOGO (Red Sun).svg')) }}"
                            alt="Copyus"
                            class="w-auto transition-all duration-300"
                            :style="lightMode ? 'height:64px;filter:none' : 'height:64px;filter:brightness(0) invert(1)'">
                    </a>
                    <span class="block text-primary text-body-sm font-outfit
                                 font-normal tracking-wide mt-0.5">
                        Admin Panel
                    </span>
                </a>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">

                <a href="{{ route('admin.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl
                          font-outfit text-body-lg transition-colors
                          {{ request()->routeIs('admin.index')
                             ? 'bg-primary text-white'
                             : 'text-gray-400 hover:bg-white hover:bg-opacity-10 hover:text-white' }}">
                    <span>🎛️</span> Dashboard
                </a>

                <div class="pt-4 pb-1">
                    <p class="font-outfit text-body-sm text-gray-600 px-3 uppercase
                               tracking-widest text-xs">
                        Gestió
                    </p>
                </div>

                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl
                          font-outfit text-body-lg transition-colors
                          {{ request()->routeIs('admin.users.*')
                             ? 'bg-primary text-white'
                             : 'text-gray-400 hover:bg-white hover:bg-opacity-10 hover:text-white' }}">
                    <span>👥</span> Usuaris
                    @if($pendingCount = \App\Models\User::where('role','pending')->count())
                        <span class="ml-auto bg-primary text-white text-xs font-bold
                                     px-2 py-0.5 rounded-full
                                     {{ request()->routeIs('admin.users.*') ? 'bg-white text-primary' : '' }}">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.products.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl
                          font-outfit text-body-lg transition-colors
                          {{ request()->routeIs('admin.products.*')
                             ? 'bg-primary text-white'
                             : 'text-gray-400 hover:bg-white hover:bg-opacity-10 hover:text-white' }}">
                    <span>📦</span> Productes
                </a>

                <a href="{{ route('admin.orders.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl
                          font-outfit text-body-lg transition-colors
                          {{ request()->routeIs('admin.orders.*')
                             ? 'bg-primary text-white'
                             : 'text-gray-400 hover:bg-white hover:bg-opacity-10 hover:text-white' }}">
                    <span>🛒</span> Comandes
                </a>

                <a href="{{ route('admin.quotations.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl
                          font-outfit text-body-lg transition-colors
                          {{ request()->routeIs('admin.quotations.*')
                             ? 'bg-primary text-white'
                             : 'text-gray-400 hover:bg-white hover:bg-opacity-10 hover:text-white' }}">
                    <span>📋</span> Pressupostos
                </a>
                
                <a href="{{ route('admin.promo-codes.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl
                          font-outfit text-body-lg transition-colors
                          {{ request()->routeIs('admin.promo-codes.*')
                             ? 'bg-primary text-white'
                             : 'text-gray-400 hover:bg-white hover:bg-opacity-10 hover:text-white' }}">
                    <span>🏷️</span> Codis de descompte
                </a>

                <a href="{{ route('admin.reviews.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl
                          font-outfit text-body-lg transition-colors
                          {{ request()->routeIs('admin.reviews.*')
                             ? 'bg-primary text-white'
                             : 'text-gray-400 hover:bg-white hover:bg-opacity-10 hover:text-white' }}">
                    <span>⭐</span> Ressenyes
                    @php $pendingReviews = \App\Models\ProductReview::where('is_approved', false)->count(); @endphp
                    @if($pendingReviews)
                        <span class="ml-auto bg-orange-500 text-white text-xs font-bold
                                     px-2 py-0.5 rounded-full">
                            {{ $pendingReviews }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.print.templates.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl
                          font-outfit text-body-lg transition-colors
                          {{ request()->routeIs('admin.print.templates.*')
                             ? 'bg-primary text-white'
                             : 'text-gray-400 hover:bg-white hover:bg-opacity-10 hover:text-white' }}">
                    <span>🖨️</span> Plantilles
                </a>
                <a href="{{ route('admin.print.jobs.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl
                          font-outfit text-body-lg transition-colors
                          {{ request()->routeIs('admin.print.jobs.*')
                             ? 'bg-primary text-white'
                             : 'text-gray-400 hover:bg-white hover:bg-opacity-10 hover:text-white' }}">
                    <span>📋</span> Treballs impressió
                    @php $pendingJobs = \App\Models\PrintJob::whereIn('status', ['ordered', 'in_production'])->count(); @endphp
                    @if($pendingJobs)
                        <span class="ml-auto bg-orange-500 text-white text-xs font-bold
                                     px-2 py-0.5 rounded-full">{{ $pendingJobs }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.tickets.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl
                          font-outfit text-body-lg transition-colors
                          {{ request()->routeIs('admin.tickets.*')
                             ? 'bg-primary text-white'
                             : 'text-gray-400 hover:bg-white hover:bg-opacity-10 hover:text-white' }}">
                    <span>🎫</span> Tiquets de suport
                    @php $openTickets = \App\Models\Ticket::whereIn('status', ['open'])->count(); @endphp
                    @if($openTickets)
                        <span class="ml-auto bg-blue-500 text-white text-xs font-bold
                                     px-2 py-0.5 rounded-full">{{ $openTickets }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.reports.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    📊 Reports
                </a>

                <div class="pt-4 pb-1">
                    <p class="font-outfit text-body-sm text-gray-600 px-3 uppercase
                               tracking-widest text-xs">
                        Compte
                    </p>
                </div>

                <a href="{{ route('home') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl
                          font-outfit text-body-lg text-gray-400
                          hover:bg-white hover:bg-opacity-10 hover:text-white transition-colors">
                    <span>🌐</span> Veure botiga
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl
                                   font-outfit text-body-lg text-gray-400
                                   hover:bg-primary hover:text-white transition-colors text-left">
                        <span>🚪</span> Tancar sessió
                    </button>
                </form>

            </nav>

            {{-- Admin User Info --}}
            <div class="px-6 py-4 border-t border-white border-opacity-10">
                <p class="font-outfit text-body-sm text-gray-400">
                    {{ auth()->user()->name }}
                </p>
                <p class="font-outfit text-body-sm text-gray-600 text-xs">
                    Administrador
                </p>
            </div>

        </aside>

        {{-- ── Main Content ── --}}
        <main class="flex-1 ml-64 min-h-screen overflow-y-auto">

            {{-- Top Bar --}}
            <div class="bg-white border-b border-gray-100 px-8 py-4
                        flex items-center justify-between sticky top-0 z-30">
                <h2 class="font-alumni text-h5 text-dark">
                    @yield('title', 'Dashboard')
                </h2>
                <div class="flex items-center gap-4">
                    <span class="font-outfit text-body-sm text-gray-400">
                        {{ now()->format('d/m/Y H:i') }}
                    </span>

                    {{-- Notification Bell --}}
                    @php
                        $adminNotifications = auth()->user()->notifications()->latest()->take(10)->get();
                        $unreadCount        = auth()->user()->unreadNotifications()->count();
                    @endphp
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open"
                                class="relative p-2 rounded-xl hover:bg-light transition-colors text-gray-400 hover:text-dark">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if($unreadCount > 0)
                                <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white
                                             text-xs font-bold rounded-full flex items-center justify-center
                                             leading-none">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                            @endif
                        </button>

                        {{-- Dropdown --}}
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl shadow-lg
                                    border border-gray-100 overflow-hidden z-50"
                             style="display:none">

                            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                                <p class="font-alumni text-sm-header text-dark">Notificacions</p>
                                @if($unreadCount > 0)
                                    <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="font-outfit text-xs text-secondary hover:text-primary transition-colors">
                                            Marcar totes
                                        </button>
                                    </form>
                                @endif
                            </div>

                            @if($adminNotifications->isEmpty())
                                <div class="px-4 py-8 text-center">
                                    <p class="font-outfit text-xs text-gray-400">Cap notificació</p>
                                </div>
                            @else
                                <div class="divide-y divide-gray-50 max-h-80 overflow-y-auto">
                                    @foreach($adminNotifications as $notif)
                                        @php
                                            $data   = $notif->data;
                                            $isRead = $notif->read_at !== null;
                                            $icon   = match($data['type'] ?? '') {
                                                'artwork_uploaded'        => '🖼️',
                                                'print_job_status_updated'=> '🖨️',
                                                default                   => '🔔',
                                            };
                                        @endphp
                                        <div class="flex items-start gap-3 px-4 py-3
                                                    {{ $isRead ? '' : 'bg-blue-50/40' }}
                                                    hover:bg-light transition-colors">
                                            <span class="text-lg shrink-0 mt-0.5">{{ $icon }}</span>
                                            <div class="flex-1 min-w-0">
                                                @if(($data['type'] ?? '') === 'artwork_uploaded')
                                                    <p class="font-outfit text-xs font-semibold text-dark leading-tight">
                                                        Arxiu de disseny rebut
                                                    </p>
                                                    <p class="font-outfit text-xs text-gray-500 mt-0.5 truncate">
                                                        {{ $data['client_name'] ?? '—' }}
                                                        · {{ $data['template_name'] ?? '—' }}
                                                    </p>
                                                @else
                                                    <p class="font-outfit text-xs text-dark leading-tight">
                                                        {{ $data['type'] ?? 'Notificació' }}
                                                    </p>
                                                @endif
                                                <p class="font-outfit text-xs text-gray-400 mt-0.5">
                                                    {{ $notif->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                @if(!$isRead)
                                                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                                @endif
                                                @if(isset($data['url']))
                                                    <a href="{{ $data['url'] }}"
                                                       class="font-outfit text-xs text-secondary hover:text-primary">
                                                        →
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mx-8 mt-6 bg-green-50 border border-green-200 text-green-700
                            font-outfit text-body-lg px-5 py-4 rounded-xl">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mx-8 mt-6 bg-red-50 border border-red-200 text-red-600
                            font-outfit text-body-lg px-5 py-4 rounded-xl">
                    ❌ {{ session('error') }}
                </div>
            @endif

            @yield('content')

        </main>

    </div>

    @stack('scripts')
</body>
</html>