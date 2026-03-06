<x-admin-layout>
    <x-slot name="header">Dashboard</x-slot>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Utilisateurs -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Utilisateurs</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($stats['users_count']) }}</p>
                    <p class="text-green-400 text-sm mt-1">+{{ $stats['users_today'] }} aujourd'hui</p>
                </div>
                <div class="w-12 h-12 bg-indigo-500/20 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">👥</span>
                </div>
            </div>
        </div>

        <!-- Thèmes -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Thèmes</p>
                    <p class="text-3xl font-bold mt-1">{{ $stats['themes_count'] }}</p>
                    <p class="text-gray-400 text-sm mt-1">{{ $stats['themes_active'] }} actifs</p>
                </div>
                <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">🎨</span>
                </div>
            </div>
        </div>

        <!-- Cartes -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Cartes</p>
                    <p class="text-3xl font-bold mt-1">{{ $stats['cards_count'] }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">🎴</span>
                </div>
            </div>
        </div>

        <!-- Parties -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Parties</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($stats['games_total']) }}</p>
                    <p class="text-green-400 text-sm mt-1">{{ $stats['games_in_progress'] }} en cours</p>
                </div>
                <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">♟️</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Thèmes incomplets -->
        @if($incompleteThemes->count() > 0)
        <div class="bg-gray-800 rounded-xl border border-gray-700">
            <div class="p-4 border-b border-gray-700">
                <h2 class="font-gaming text-lg flex items-center">
                    <span class="mr-2">⚠️</span> Thèmes incomplets
                </h2>
            </div>
            <div class="p-4">
                <div class="space-y-3">
                    @foreach($incompleteThemes as $theme)
                    <div class="flex items-center justify-between p-3 bg-gray-700/50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 rounded" style="background: {{ $theme->primary_color }}"></div>
                            <span>{{ $theme->name }}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="text-amber-400 text-sm">{{ $theme->cards_count }}/12 cartes</span>
                            <a href="{{ route('admin.themes.show', $theme) }}" 
                               class="text-indigo-400 hover:text-indigo-300 text-sm">
                                Compléter →
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Derniers utilisateurs -->
        <div class="bg-gray-800 rounded-xl border border-gray-700">
            <div class="p-4 border-b border-gray-700">
                <h2 class="font-gaming text-lg">Derniers inscrits</h2>
            </div>
            <div class="p-4">
                <div class="space-y-3">
                    @foreach($recentUsers as $user)
                    <div class="flex items-center justify-between p-3 bg-gray-700/50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-sm font-bold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-medium">{{ $user->name }}</div>
                                <div class="text-xs text-gray-400">{{ $user->email }}</div>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $user->created_at->diffForHumans() }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Dernières parties -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 lg:col-span-2">
            <div class="p-4 border-b border-gray-700">
                <h2 class="font-gaming text-lg">Dernières parties</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-gray-400 text-sm">
                            <th class="p-4">Joueurs</th>
                            <th class="p-4">Thème</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Coups</th>
                            <th class="p-4">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentGames as $game)
                        <tr class="border-t border-gray-700">
                            <td class="p-4">
                                <span class="text-white">{{ $game->whitePlayer?->name ?? 'IA' }}</span>
                                <span class="text-gray-500 mx-2">vs</span>
                                <span class="text-white">{{ $game->blackPlayer?->name ?? 'IA' }}</span>
                            </td>
                            <td class="p-4 text-gray-400">{{ $game->theme?->name ?? '-' }}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded-full text-xs
                                    {{ $game->status === 'completed' ? 'bg-green-500/20 text-green-400' : '' }}
                                    {{ $game->status === 'in_progress' ? 'bg-blue-500/20 text-blue-400' : '' }}
                                    {{ $game->status === 'waiting' ? 'bg-amber-500/20 text-amber-400' : '' }}
                                ">
                                    {{ $game->status_name }}
                                </span>
                            </td>
                            <td class="p-4 text-gray-400">{{ $game->move_count }}</td>
                            <td class="p-4 text-gray-500 text-sm">{{ $game->created_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
