<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="font-gaming text-3xl mb-8">Mes Parties</h1>

        @if($games->count() > 0)
            <div class="space-y-4">
                @foreach($games as $game)
                    @php
                        $isWhite = $game->white_player_id === auth()->id();
                        $opponent = $isWhite ? $game->blackPlayer : $game->whitePlayer;
                        $won = $game->winner_id === auth()->id();
                        $draw = $game->status === 'draw';
                    @endphp
                    <div class="card-glass rounded-xl p-4 hover:bg-white/5 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <!-- Résultat -->
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center font-gaming text-lg
                                    {{ $won ? 'bg-green-500/20 text-green-400' : ($draw ? 'bg-gray-500/20 text-gray-400' : 'bg-red-500/20 text-red-400') }}">
                                    {{ $won ? 'V' : ($draw ? '=' : 'D') }}
                                </div>

                                <!-- Info -->
                                <div>
                                    <div class="font-medium flex items-center space-x-2">
                                        <span>vs {{ $game->isAiGame() ? 'IA (Niv. ' . $game->ai_level . ')' : ($opponent?->name ?? 'Joueur inconnu') }}</span>
                                        @if($won)
                                            <span class="text-xs px-2 py-0.5 bg-green-500/20 text-green-400 rounded-full">Victoire</span>
                                        @elseif($draw)
                                            <span class="text-xs px-2 py-0.5 bg-gray-500/20 text-gray-400 rounded-full">Nulle</span>
                                        @else
                                            <span class="text-xs px-2 py-0.5 bg-red-500/20 text-red-400 rounded-full">Défaite</span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-400">
                                        {{ $game->theme?->name ?? 'Sans thème' }} •
                                        {{ $game->move_count }} coups •
                                        {{ $game->ended_at?->diffForHumans() ?? 'Récemment' }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4">
                                <!-- Raison de fin -->
                                <div class="text-right text-sm text-gray-500">
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

                                <a href="{{ route('game.replay', $game->uuid) }}" 
                                   class="px-4 py-2 bg-white/5 border border-white/10 rounded-lg hover:bg-white/10 transition text-sm">
                                    Revoir
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $games->links() }}
            </div>
        @else
            <div class="card-glass rounded-2xl p-12 text-center">
                <div class="text-5xl mb-4 opacity-50">🎮</div>
                <p class="text-gray-400 mb-4">Vous n'avez pas encore terminé de partie</p>
                <a href="{{ route('lobby') }}" class="btn-primary px-6 py-3 rounded-xl inline-block">
                    Jouer maintenant
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
