<x-app-layout>
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-4">
            <div>
                <h1 class="font-gaming text-2xl sm:text-3xl font-bold">Lobby</h1>
                <p class="text-gray-400 mt-1 text-sm sm:text-base">Créez ou rejoignez une partie</p>
            </div>
            <div class="flex items-center">
                <!-- Stats du joueur -->
                <div class="card-glass rounded-xl px-3 sm:px-4 py-2 flex items-center space-x-3 sm:space-x-4">
                    <div class="text-center">
                        <div class="text-xs text-gray-400">ELO</div>
                        <div class="font-gaming text-amber-400 text-sm sm:text-base">{{ $playerStats['elo'] }}</div>
                    </div>
                    <div class="w-px h-8 bg-white/10"></div>
                    <div class="text-center">
                        <div class="text-xs text-gray-400">Victoires</div>
                        <div class="font-gaming text-green-400 text-sm sm:text-base">{{ $playerStats['win_rate'] }}%</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
            <!-- Colonne gauche : Créer une partie -->
            <div class="lg:col-span-1 space-y-4 sm:space-y-6">
                <!-- Créer partie PvP -->
                <div class="card-glass rounded-2xl p-4 sm:p-6">
                    <h2 class="font-gaming text-lg sm:text-xl mb-3 sm:mb-4 flex items-center">
                        <span class="mr-2">👥</span> Partie Multijoueur
                    </h2>

                    @if($myWaitingGame)
                        <div class="bg-amber-500/20 border border-amber-500/30 rounded-xl p-4 mb-4">
                            <p class="text-amber-400 text-sm mb-2">Vous avez une partie en attente</p>
                            <div class="flex space-x-2">
                                <a href="{{ route('game.waiting', $myWaitingGame->uuid) }}" 
                                   class="flex-1 btn-primary py-2 rounded-lg text-center text-sm">
                                    Voir
                                </a>
                                <form action="{{ route('lobby.cancel', $myWaitingGame->uuid) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full py-2 rounded-lg bg-red-500/20 text-red-400 hover:bg-red-500/30 text-sm transition">
                                        Annuler
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <form action="{{ route('lobby.create') }}" method="POST" class="space-y-4" x-data="{ timerEnabled: false }">
                            @csrf
                            
                            <!-- Sélection du thème -->
                            <div>
                                <label class="block text-sm text-gray-400 mb-2">Thème</label>
                                <select name="theme_id" required
                                        class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    @foreach($themes as $theme)
                                        <option value="{{ $theme->id }}">{{ $theme->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Timer -->
                            <div>
                                <label class="flex items-center mb-2">
                                    <input type="checkbox" name="timer_enabled" x-model="timerEnabled"
                                           class="w-4 h-4 rounded bg-white/5 border-white/20 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-400">Activer le timer</span>
                                </label>
                                <div x-show="timerEnabled" x-collapse class="grid grid-cols-2 gap-2">
                                    <select name="timer_minutes" 
                                            class="px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="1">1 min (Bullet)</option>
                                        <option value="3">3 min (Blitz)</option>
                                        <option value="5" selected>5 min (Blitz)</option>
                                        <option value="10">10 min (Rapid)</option>
                                        <option value="15">15 min</option>
                                        <option value="30">30 min</option>
                                    </select>
                                    <select name="timer_increment"
                                            class="px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="0">+0s</option>
                                        <option value="1">+1s</option>
                                        <option value="2">+2s</option>
                                        <option value="3">+3s</option>
                                        <option value="5">+5s</option>
                                        <option value="10">+10s</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="w-full btn-primary py-3 rounded-xl font-gaming">
                                Créer une partie
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Jouer contre l'IA -->
                <div class="card-glass rounded-2xl p-4 sm:p-6">
                    <h2 class="font-gaming text-lg sm:text-xl mb-3 sm:mb-4 flex items-center">
                        <span class="mr-2">🤖</span> Jouer contre l'IA
                    </h2>

                    <form action="{{ route('lobby.create-ai') }}" method="POST" class="space-y-4" x-data="{ timerEnabled: false, sameTheme: true }">
                        @csrf

                        <!-- Votre Thème -->
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Votre thème</label>
                            <select name="theme_id" required
                                    class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                @foreach($themes as $theme)
                                    <option value="{{ $theme->id }}">{{ $theme->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Thème IA différent -->
                        <div>
                            <label class="flex items-center mb-2">
                                <input type="checkbox" x-model="sameTheme" checked
                                       class="w-4 h-4 rounded bg-white/5 border-white/20 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-400">Même thème pour l'IA</span>
                            </label>
                            <div x-show="!sameTheme" x-collapse>
                                <select name="ai_theme_id"
                                        class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    @foreach($themes as $theme)
                                        <option value="{{ $theme->id }}">{{ $theme->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Niveau IA -->
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Difficulté</label>
                            <select name="ai_level" required
                                    class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="1">🌱 Débutant</option>
                                <option value="5">😊 Facile</option>
                                <option value="10" selected>🎯 Intermédiaire</option>
                                <option value="15">💪 Difficile</option>
                                <option value="20">🏆 Expert</option>
                            </select>
                        </div>

                        <!-- Couleur -->
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Jouer avec</label>
                            <div class="grid grid-cols-3 gap-2">
                                <label class="flex items-center justify-center p-3 bg-white/5 border border-white/10 rounded-lg cursor-pointer hover:bg-white/10 transition has-[:checked]:bg-indigo-500/20 has-[:checked]:border-indigo-500">
                                    <input type="radio" name="player_color" value="white" class="sr-only" checked>
                                    <span class="text-2xl">♔</span>
                                </label>
                                <label class="flex items-center justify-center p-3 bg-white/5 border border-white/10 rounded-lg cursor-pointer hover:bg-white/10 transition has-[:checked]:bg-indigo-500/20 has-[:checked]:border-indigo-500">
                                    <input type="radio" name="player_color" value="black" class="sr-only">
                                    <span class="text-2xl">♚</span>
                                </label>
                                <label class="flex items-center justify-center p-3 bg-white/5 border border-white/10 rounded-lg cursor-pointer hover:bg-white/10 transition has-[:checked]:bg-indigo-500/20 has-[:checked]:border-indigo-500">
                                    <input type="radio" name="player_color" value="random" class="sr-only">
                                    <span class="text-xl">🎲</span>
                                </label>
                            </div>
                        </div>

                        <!-- Timer -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="timer_enabled" x-model="timerEnabled"
                                       class="w-4 h-4 rounded bg-white/5 border-white/20 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-400">Activer le timer</span>
                            </label>
                            <div x-show="timerEnabled" x-collapse class="mt-2">
                                <select name="timer_minutes"
                                        class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="5">5 minutes</option>
                                    <option value="10" selected>10 minutes</option>
                                    <option value="15">15 minutes</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="w-full btn-accent py-3 rounded-xl font-gaming text-black">
                            Jouer contre l'IA
                        </button>
                    </form>
                </div>
            </div>

            <!-- Colonne droite : Parties disponibles -->
            <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                <!-- Mes parties en cours -->
                @if($myGames->count() > 0)
                <div class="card-glass rounded-2xl p-4 sm:p-6">
                    <h2 class="font-gaming text-lg sm:text-xl mb-3 sm:mb-4 flex items-center">
                        <span class="mr-2">⚔️</span> Mes parties en cours
                    </h2>
                    <div class="space-y-2 sm:space-y-3">
                        @foreach($myGames as $game)
                            <a href="{{ route('game.play', $game->uuid) }}"
                               class="flex items-center justify-between p-3 sm:p-4 bg-white/5 rounded-xl hover:bg-white/10 transition group">
                                <div class="flex items-center space-x-3 sm:space-x-4 min-w-0">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <span class="text-lg sm:text-xl">{{ $game->isAiGame() ? '🤖' : '👥' }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-medium text-sm sm:text-base truncate">
                                            vs {{ $game->white_player_id === auth()->id()
                                                ? ($game->blackPlayer?->name ?? 'IA')
                                                : $game->whitePlayer->name }}
                                        </div>
                                        <div class="text-xs sm:text-sm text-gray-400 truncate">
                                            {{ $game->whiteTheme?->name ?? 'Sans thème' }} •
                                            {{ $game->current_turn === $game->getPlayerColor(auth()->user()) ? 'À vous !' : 'En attente...' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-indigo-400 group-hover:translate-x-1 transition-transform ml-2">
                                    →
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Parties en attente -->
                <div class="card-glass rounded-2xl p-4 sm:p-6">
                    <h2 class="font-gaming text-lg sm:text-xl mb-3 sm:mb-4 flex items-center">
                        <span class="mr-2">🎮</span> Parties disponibles
                    </h2>

                    @if($waitingGames->count() > 0)
                        <div class="space-y-2 sm:space-y-3">
                            @foreach($waitingGames as $game)
                                <div class="flex items-center justify-between p-3 sm:p-4 bg-white/5 rounded-xl gap-3">
                                    <div class="flex items-center space-x-3 sm:space-x-4 min-w-0">
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center font-bold flex-shrink-0 text-sm sm:text-base">
                                            {{ strtoupper(substr($game->whitePlayer->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-medium text-sm sm:text-base truncate">{{ $game->whitePlayer->name }}</div>
                                            <div class="text-xs sm:text-sm text-gray-400 truncate">
                                                {{ $game->whiteTheme?->name ?? 'Sans thème' }}
                                                @if($game->timer_enabled)
                                                    • {{ $game->timer_minutes }}min
                                                    @if($game->timer_increment > 0)
                                                        +{{ $game->timer_increment }}s
                                                    @endif
                                                @else
                                                    • Sans limite
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('lobby.join.show', $game->uuid) }}" class="btn-primary px-4 sm:px-6 py-2 rounded-lg font-medium text-sm sm:text-base flex-shrink-0 text-center">
                                        Rejoindre
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 sm:py-12">
                            <div class="text-4xl sm:text-5xl mb-4 opacity-50">🎯</div>
                            <p class="text-gray-400 text-sm sm:text-base">Aucune partie en attente</p>
                            <p class="text-gray-500 text-xs sm:text-sm mt-1">Créez une partie ou jouez contre l'IA !</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
