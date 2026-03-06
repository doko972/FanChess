<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'FanChess' }} - Échecs Thématiques</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=orbitron:400,500,600,700,800,900|inter:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="h-full text-white antialiased">
    <!-- Fond cosmique -->
    <div class="cosmic-bg"></div>

    <div class="relative z-10 min-h-full flex flex-col">
        <!-- Navigation -->
        <nav class="bg-black/30 backdrop-blur-md border-b border-white/10 sticky top-0 z-50"
             x-data="{ mobileOpen: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">

                    <!-- Logo -->
                    <a href="{{ route('home') }}" class="flex items-center space-x-3 flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center glow">
                            <span class="text-2xl">♔</span>
                        </div>
                        <span class="font-gaming font-bold text-xl bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">
                            FanChess
                        </span>
                    </a>

                    <!-- Liens desktop (masqués sur mobile) -->
                    @auth
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="{{ route('lobby') }}"
                           class="nav-link text-gray-300 hover:text-white {{ request()->routeIs('lobby') ? 'active text-white' : '' }}">
                            Lobby
                        </a>
                        <a href="{{ route('game.history') }}"
                           class="nav-link text-gray-300 hover:text-white {{ request()->routeIs('game.history') ? 'active text-white' : '' }}">
                            Mes Parties
                        </a>
                        @if(auth()->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}"
                           class="nav-link text-amber-400 hover:text-amber-300 {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                            Admin
                        </a>
                        @endif
                    </div>
                    @endauth

                    <!-- Droite : avatar desktop + boutons guest + burger mobile -->
                    <div class="flex items-center space-x-3">
                        @auth
                            <!-- Infos utilisateur (desktop) -->
                            <div class="text-right hidden lg:block">
                                <div class="text-sm font-medium text-white">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-gray-400">ELO: {{ auth()->user()->elo_rating }}</div>
                            </div>
                            <!-- Avatar + dropdown (desktop seulement) -->
                            <div class="relative hidden md:block" x-data="{ open: false }">
                                <button @click="open = !open"
                                        class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center font-bold text-sm hover:ring-2 ring-white/30 transition">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition
                                     class="absolute right-0 mt-2 w-48 bg-gray-900 border border-white/10 rounded-xl shadow-xl overflow-hidden">
                                    <a href="{{ route('profile.edit') }}"
                                       class="block px-4 py-3 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition">
                                        Mon Profil
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                                class="w-full text-left px-4 py-3 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition">
                                            Déconnexion
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <!-- Boutons guest (desktop) -->
                            <a href="{{ route('login') }}" class="hidden md:block text-gray-300 hover:text-white transition text-sm">
                                Connexion
                            </a>
                            <a href="{{ route('register') }}" class="hidden md:block btn-primary px-4 py-2 rounded-lg font-medium text-sm">
                                Rejoindre
                            </a>
                        @endauth

                        <!-- Bouton burger (mobile uniquement) -->
                        <button @click="mobileOpen = !mobileOpen"
                                class="md:hidden p-2 rounded-lg hover:bg-white/10 transition"
                                :aria-expanded="mobileOpen"
                                aria-label="Menu principal">
                            <div class="burger" :class="{ 'is-open': mobileOpen }">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Menu mobile (tiroir flottant) -->
            <div x-show="mobileOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="md:hidden absolute top-full left-0 right-0 z-40 border-t border-white/10 bg-black/80 backdrop-blur-md shadow-2xl">
                <div class="max-w-7xl mx-auto px-4 py-3 space-y-1">
                    @auth
                        <!-- Liens de navigation -->
                        <a href="{{ route('lobby') }}"
                           class="mobile-nav-link {{ request()->routeIs('lobby') ? 'active' : '' }}"
                           @click="mobileOpen = false">
                            <span class="mr-3 text-lg">🎮</span> Lobby
                        </a>
                        <a href="{{ route('game.history') }}"
                           class="mobile-nav-link {{ request()->routeIs('game.history') ? 'active' : '' }}"
                           @click="mobileOpen = false">
                            <span class="mr-3 text-lg">📜</span> Mes Parties
                        </a>
                        @if(auth()->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}"
                           class="mobile-nav-link text-amber-400 {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                           @click="mobileOpen = false">
                            <span class="mr-3 text-lg">⚙️</span> Administration
                        </a>
                        @endif

                        <!-- Séparateur + infos utilisateur -->
                        <div class="border-t border-white/10 mt-3 pt-3 space-y-1">
                            <div class="flex items-center space-x-3 px-3 py-2">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-white">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-gray-400">ELO : {{ auth()->user()->elo_rating }}</div>
                                </div>
                            </div>
                            <a href="{{ route('profile.edit') }}"
                               class="mobile-nav-link"
                               @click="mobileOpen = false">
                                <span class="mr-3 text-lg">👤</span> Mon Profil
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="mobile-nav-link text-red-400 hover:text-red-300">
                                    <span class="mr-3 text-lg">🚪</span> Déconnexion
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Liens guest -->
                        <a href="{{ route('login') }}"
                           class="mobile-nav-link"
                           @click="mobileOpen = false">
                            <span class="mr-3 text-lg">🔑</span> Connexion
                        </a>
                        <a href="{{ route('register') }}"
                           class="mobile-nav-link text-indigo-400"
                           @click="mobileOpen = false">
                            <span class="mr-3 text-lg">✨</span> Créer un compte
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        @if(session('success') || session('error') || session('info'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            @if(session('success'))
            <div class="bg-green-500/20 border border-green-500/30 text-green-400 px-4 py-3 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="bg-red-500/20 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg" role="alert">
                {{ session('error') }}
            </div>
            @endif
            @if(session('info'))
            <div class="bg-blue-500/20 border border-blue-500/30 text-blue-400 px-4 py-3 rounded-lg" role="alert">
                {{ session('info') }}
            </div>
            @endif
        </div>
        @endif

        <!-- Main Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-black/30 border-t border-white/10 py-6 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    <div class="flex items-center space-x-2">
                        <span class="text-2xl">♔</span>
                        <span class="font-gaming text-sm text-gray-400">FanChess</span>
                    </div>
                    <p class="text-gray-500 text-sm">
                        © {{ date('Y') }} FanChess - Développé avec ❤️ par L'Atelier Normand du Web
                    </p>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
