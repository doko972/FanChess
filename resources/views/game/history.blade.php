<x-app-layout>
    <div class="max-w-5xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8">
        <h1 class="font-gaming text-2xl sm:text-3xl mb-6 sm:mb-8">Mes Parties</h1>

        @if($games->count() > 0)
            <div class="space-y-3 sm:space-y-4">
                @foreach($games as $game)
                    @php
                        $isWhite = $game->white_player_id === auth()->id();
                        $opponent = $isWhite ? $game->blackPlayer : $game->whitePlayer;
                        $won = $game->winner_id === auth()->id();
                        $draw = $game->status === 'draw';
                    @endphp
                    <div class="card-glass rounded-xl p-3 sm:p-4 hover:bg-white/5 transition">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <!-- Partie gauche : Résultat + Info -->
                            <div class="flex items-center space-x-3 sm:space-x-4">
                                <!-- Résultat -->
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center font-gaming text-base sm:text-lg flex-shrink-0
                                    {{ $won ? 'bg-green-500/20 text-green-400' : ($draw ? 'bg-gray-500/20 text-gray-400' : 'bg-red-500/20 text-red-400') }}">
                                    {{ $won ? 'V' : ($draw ? '=' : 'D') }}
                                </div>

                                <!-- Info -->
                                <div class="min-w-0">
                                    <div class="font-medium text-sm sm:text-base flex flex-wrap items-center gap-1 sm:gap-2">
                                        <span class="truncate">vs {{ $game->isAiGame() ? 'IA (Niv. ' . $game->ai_level . ')' : ($opponent?->name ?? 'Joueur inconnu') }}</span>
                                        @if($won)
                                            <span class="text-xs px-2 py-0.5 bg-green-500/20 text-green-400 rounded-full">Victoire</span>
                                        @elseif($draw)
                                            <span class="text-xs px-2 py-0.5 bg-gray-500/20 text-gray-400 rounded-full">Nulle</span>
                                        @else
                                            <span class="text-xs px-2 py-0.5 bg-red-500/20 text-red-400 rounded-full">Défaite</span>
                                        @endif
                                    </div>
                                    <div class="text-xs sm:text-sm text-gray-400 truncate">
                                        {{ $game->theme?->name ?? 'Sans thème' }} •
                                        {{ $game->move_count }} coups •
                                        {{ $game->ended_at?->diffForHumans() ?? 'Récemment' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Partie droite : Raison + Bouton -->
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 ml-13 sm:ml-0">
                                <a href="{{ route('game.replay', $game->uuid) }}"
                                   class="px-4 py-2 bg-indigo-500/20 border border-indigo-500/30 text-indigo-400 rounded-lg hover:bg-indigo-500/30 transition text-sm text-center">
                                    Revoir la partie
                                </a>
                                <!-- Raison de fin -->
                                <div class="text-xs sm:text-sm text-gray-500 text-center sm:text-right">
                                    @switch($game->end_reason)
                                        @case('checkmate')
                                            Échec et mat
                                            @break
                                        @case('resignation')
                                            Abandon
                                            @break
                                        @case('timeout')
                                            Temps écoulé
                                            @break
                                        @case('stalemate')
                                            Pat
                                            @break
                                        @case('draw_agreement')
                                            Accord mutuel
                                            @break
                                        @default
                                            -
                                    @endswitch
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6 sm:mt-8">
                {{ $games->links() }}
            </div>
        @else
            <div class="card-glass rounded-2xl p-8 sm:p-12 text-center">
                <div class="text-4xl sm:text-5xl mb-4 opacity-50">🎮</div>
                <p class="text-gray-400 mb-4 text-sm sm:text-base">Vous n'avez pas encore terminé de partie</p>
                <a href="{{ route('lobby') }}" class="btn-primary px-5 sm:px-6 py-2.5 sm:py-3 rounded-xl inline-block text-sm sm:text-base">
                    Jouer maintenant
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
