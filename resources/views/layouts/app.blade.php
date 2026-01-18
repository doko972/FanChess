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
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #8b5cf6;
            --accent: #f59e0b;
            --dark: #0f0f23;
            --darker: #0a0a1a;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--darker) 0%, var(--dark) 50%, #1a1a3e 100%);
            min-height: 100vh;
        }

        .font-gaming {
            font-family: 'Orbitron', sans-serif;
        }

        /* Effet de glow */
        .glow {
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.3), 0 0 40px rgba(99, 102, 241, 0.1);
        }

        .glow-accent {
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.3), 0 0 40px rgba(245, 158, 11, 0.1);
        }

        /* Animation de fond cosmique */
        .cosmic-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
            opacity: 0.5;
            background-image: 
                radial-gradient(2px 2px at 20px 30px, #ffffff, transparent),
                radial-gradient(2px 2px at 40px 70px, rgba(255,255,255,0.8), transparent),
                radial-gradient(1px 1px at 90px 40px, #ffffff, transparent),
                radial-gradient(2px 2px at 160px 120px, rgba(255,255,255,0.9), transparent),
                radial-gradient(1px 1px at 230px 80px, #ffffff, transparent),
                radial-gradient(2px 2px at 300px 150px, rgba(255,255,255,0.7), transparent);
            background-size: 350px 200px;
            animation: cosmic 60s linear infinite;
        }

        @keyframes cosmic {
            from { background-position: 0 0; }
            to { background-position: 350px 200px; }
        }

        /* Cards */
        .card-glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.4);
        }

        .btn-accent {
            background: linear-gradient(135deg, var(--accent) 0%, #f97316 100%);
            transition: all 0.3s ease;
        }

        .btn-accent:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(245, 158, 11, 0.4);
        }

        /* Navigation */
        .nav-link {
            position: relative;
            transition: color 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            transition: width 0.3s ease;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }

        /* Scrollbar custom */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--darker);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary);
        }
    </style>

    @stack('styles')
</head>
<body class="h-full text-white antialiased">
    <!-- Fond cosmique -->
    <div class="cosmic-bg"></div>

    <div class="relative z-10 min-h-full flex flex-col">
        <!-- Navigation -->
        <nav class="bg-black/30 backdrop-blur-md border-b border-white/10 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center glow">
                                <span class="text-2xl">♔</span>
                            </div>
                            <span class="font-gaming font-bold text-xl bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">
                                FanChess
                            </span>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    @auth
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="{{ route('lobby') }}" class="nav-link text-gray-300 hover:text-white {{ request()->routeIs('lobby') ? 'active text-white' : '' }}">
                            Lobby
                        </a>
                        <a href="{{ route('game.history') }}" class="nav-link text-gray-300 hover:text-white {{ request()->routeIs('game.history') ? 'active text-white' : '' }}">
                            Mes Parties
                        </a>
                        @if(auth()->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}" class="nav-link text-amber-400 hover:text-amber-300 {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                            Admin
                        </a>
                        @endif
                    </div>
                    @endauth

                    <!-- User Menu -->
                    <div class="flex items-center space-x-4">
                        @auth
                            <div class="flex items-center space-x-3">
                                <div class="text-right hidden sm:block">
                                    <div class="text-sm font-medium text-white">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-gray-400">ELO: {{ auth()->user()->elo_rating }}</div>
                                </div>
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center font-bold text-sm hover:ring-2 ring-white/30 transition">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </button>
                                    <div x-show="open" @click.away="open = false" x-transition
                                         class="absolute right-0 mt-2 w-48 bg-gray-900 border border-white/10 rounded-xl shadow-xl overflow-hidden">
                                        <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition">
                                            Mon Profil
                                        </a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="w-full text-left px-4 py-3 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition">
                                                Déconnexion
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-300 hover:text-white transition">Connexion</a>
                            <a href="{{ route('register') }}" class="btn-primary px-4 py-2 rounded-lg font-medium">
                                Rejoindre
                            </a>
                        @endauth
                    </div>
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
