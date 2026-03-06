<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin' }} - FanChess</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=orbitron:400,500,600,700|inter:400,500,600,700" rel="stylesheet" />

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="h-full text-white bg-gray-950">

    <div class="flex h-full" x-data="{ sidebarOpen: false }">

        {{-- =====================================================
             Backdrop (mobile uniquement) — ferme la sidebar
             ===================================================== --}}
        <div x-show="sidebarOpen"
             x-cloak
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/60 z-40 lg:hidden">
        </div>

        {{-- =====================================================
             Sidebar
             - Mobile  : fixed, hors du flux, s'ouvre en slide-in
             - Desktop : relative, partie du flex layout
             ===================================================== --}}
        <aside class="fixed top-0 left-0 h-full w-64 z-50 flex flex-col
                       bg-gray-900 border-r border-gray-800
                       transition-transform duration-300 ease-in-out
                       lg:relative lg:translate-x-0 lg:z-auto"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               x-cloak>

            <!-- Logo + bouton fermer (mobile) -->
            <div class="p-4 border-b border-gray-800 flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <span class="text-xl">♔</span>
                    </div>
                    <div>
                        <span class="font-gaming font-bold text-lg">FanChess</span>
                        <span class="block text-xs text-amber-400">Administration</span>
                    </div>
                </a>
                <!-- Bouton fermer (mobile uniquement) -->
                <button @click="sidebarOpen = false"
                        class="lg:hidden p-1.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/10 transition"
                        aria-label="Fermer le menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                   @click="sidebarOpen = false">
                    <span class="mr-3">📊</span> Dashboard
                </a>
                <a href="{{ route('admin.themes.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.themes.*') ? 'active' : '' }}"
                   @click="sidebarOpen = false">
                    <span class="mr-3">🎨</span> Thèmes
                </a>
                <a href="{{ route('admin.cards.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.cards.*') ? 'active' : '' }}"
                   @click="sidebarOpen = false">
                    <span class="mr-3">🎴</span> Cartes
                </a>

                <div class="pt-4 mt-4 border-t border-gray-800">
                    <a href="{{ route('lobby') }}" class="sidebar-link" @click="sidebarOpen = false">
                        <span class="mr-3">🎮</span> Retour au jeu
                    </a>
                </div>
            </nav>

            <!-- Utilisateur -->
            <div class="p-4 border-t border-gray-800">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium truncate">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-gray-500">Administrateur</div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- =====================================================
             Contenu principal
             ===================================================== --}}
        <div class="flex-1 flex flex-col overflow-hidden min-w-0">

            <!-- Header -->
            <header class="bg-gray-900/80 backdrop-blur-md border-b border-gray-800 px-4 sm:px-6 py-4 sticky top-0 z-30">
                <div class="flex items-center justify-between">
                    <h1 class="font-gaming text-lg sm:text-xl">{{ $header ?? 'Administration' }}</h1>

                    <div class="flex items-center space-x-3">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-white transition text-sm">
                                Déconnexion
                            </button>
                        </form>
                        <!-- Burger (mobile uniquement) -->
                        <button @click="sidebarOpen = true"
                                class="lg:hidden p-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/10 transition"
                                aria-label="Ouvrir le menu">
                            <div class="burger" :class="{ 'is-open': sidebarOpen }">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            @if(session('success') || session('error'))
            <div class="px-4 sm:px-6 pt-4 space-y-2">
                @if(session('success'))
                <div class="bg-green-500/20 border border-green-500/30 text-green-400 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
                @endif
                @if(session('error'))
                <div class="bg-red-500/20 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
                @endif
            </div>
            @endif

            <!-- Contenu de la page -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
