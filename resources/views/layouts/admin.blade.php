<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin' }} - FanChess</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=orbitron:400,500,600,700|inter:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary: #6366f1;
            --secondary: #8b5cf6;
            --accent: #f59e0b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #111827;
        }

        .font-gaming {
            font-family: 'Orbitron', sans-serif;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s;
            color: #9ca3af;
        }

        .sidebar-link:hover {
            background: rgba(255,255,255,0.05);
            color: white;
        }

        .sidebar-link.active {
            background: rgba(99, 102, 241, 0.2);
            color: white;
            border-left: 3px solid var(--primary);
        }
    </style>

    @stack('styles')
</head>
<body class="h-full text-white">
    <div class="flex h-full">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 border-r border-gray-800 flex flex-col">
            <!-- Logo -->
            <div class="p-4 border-b border-gray-800">
                <a href="{{ route('home') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <span class="text-xl">♔</span>
                    </div>
                    <div>
                        <span class="font-gaming font-bold text-lg">FanChess</span>
                        <span class="block text-xs text-amber-400">Administration</span>
                    </div>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" 
                   class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="mr-3">📊</span> Dashboard
                </a>
                <a href="{{ route('admin.themes.index') }}" 
                   class="sidebar-link {{ request()->routeIs('admin.themes.*') ? 'active' : '' }}">
                    <span class="mr-3">🎨</span> Thèmes
                </a>
                <a href="{{ route('admin.cards.index') }}" 
                   class="sidebar-link {{ request()->routeIs('admin.cards.*') ? 'active' : '' }}">
                    <span class="mr-3">🎴</span> Cartes
                </a>

                <div class="pt-4 mt-4 border-t border-gray-800">
                    <a href="{{ route('lobby') }}" class="sidebar-link">
                        <span class="mr-3">🎮</span> Retour au jeu
                    </a>
                </div>
            </nav>

            <!-- User -->
            <div class="p-4 border-t border-gray-800">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium truncate">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-gray-500">Administrateur</div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-gray-900/50 border-b border-gray-800 px-6 py-4">
                <div class="flex justify-between items-center">
                    <h1 class="font-gaming text-xl">{{ $header ?? 'Administration' }}</h1>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-white transition text-sm">
                            Déconnexion
                        </button>
                    </form>
                </div>
            </header>

            <!-- Flash Messages -->
            @if(session('success') || session('error'))
            <div class="px-6 pt-4">
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

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
