<x-app-layout>
    <div class="min-h-[calc(100vh-12rem)] flex items-center justify-center py-8 sm:py-12 px-4">
        <div class="w-full max-w-lg">
            <div class="card-glass rounded-2xl p-5 sm:p-8 text-center">
                <!-- Animation d'attente -->
                <div class="mb-6 sm:mb-8">
                    <div class="relative w-24 h-24 sm:w-32 sm:h-32 mx-auto">
                        <div class="absolute inset-0 border-4 border-indigo-500/30 rounded-full"></div>
                        <div class="absolute inset-0 border-4 border-transparent border-t-indigo-500 rounded-full animate-spin"></div>
                        <div class="absolute inset-4 border-4 border-transparent border-t-purple-500 rounded-full animate-spin" style="animation-direction: reverse; animation-duration: 1.5s;"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-3xl sm:text-4xl">♔</span>
                        </div>
                    </div>
                </div>

                <h1 class="font-gaming text-xl sm:text-2xl mb-2">En attente d'un adversaire</h1>
                <p class="text-gray-400 text-sm sm:text-base mb-4 sm:mb-6">Partagez le lien pour inviter un ami !</p>

                <!-- Infos de la partie -->
                <div class="bg-white/5 rounded-xl p-3 sm:p-4 mb-4 sm:mb-6 text-left">
                    <div class="grid grid-cols-2 gap-3 sm:gap-4 text-sm">
                        <div>
                            <span class="text-gray-400 text-xs sm:text-sm">Thème</span>
                            <p class="font-medium text-sm sm:text-base">{{ $game->whiteTheme?->name ?? 'Classique' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-400 text-xs sm:text-sm">Timer</span>
                            <p class="font-medium text-sm sm:text-base">
                                @if($game->timer_enabled)
                                    {{ $game->timer_minutes }} min
                                    @if($game->timer_increment > 0)
                                        +{{ $game->timer_increment }}s
                                    @endif
                                @else
                                    Sans limite
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Lien de partage -->
                <div class="mb-4 sm:mb-6">
                    <label class="block text-sm text-gray-400 mb-2">Lien d'invitation</label>
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-0">
                        <input type="text"
                               id="share-link"
                               value="{{ route('lobby.join', $game->uuid) }}"
                               readonly
                               class="flex-1 px-3 sm:px-4 py-3 bg-white/5 border border-white/10 rounded-xl sm:rounded-l-xl sm:rounded-r-none text-white text-xs sm:text-sm focus:outline-none overflow-hidden text-ellipsis">
                        <button onclick="copyLink()"
                                class="px-4 py-3 bg-indigo-500 hover:bg-indigo-600 rounded-xl sm:rounded-l-none sm:rounded-r-xl transition font-medium">
                            <span id="copy-text">Copier le lien</span>
                        </button>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                    <a href="{{ route('lobby') }}" class="flex-1 py-3 rounded-xl border border-white/20 hover:bg-white/5 transition text-center text-sm sm:text-base">
                        Retour au lobby
                    </a>
                    <form action="{{ route('lobby.cancel', $game->uuid) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-3 rounded-xl bg-red-500/20 text-red-400 hover:bg-red-500/30 transition text-sm sm:text-base">
                            Annuler la partie
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function copyLink() {
            const input = document.getElementById('share-link');
            input.select();
            document.execCommand('copy');

            const btn = document.getElementById('copy-text');
            btn.textContent = 'Copié !';
            setTimeout(() => btn.textContent = 'Copier', 2000);
        }

        const gameUuid = '{{ $game->uuid }}';

        // Écouter via WebSocket quand un joueur rejoint
        if (window.Echo) {
            console.log('Setting up WebSocket for waiting room:', gameUuid);

            window.Echo.private(`game.${gameUuid}`)
                .listen('.PlayerJoinedGame', (e) => {
                    console.log('Player joined:', e);
                    window.location.href = '{{ route('game.play', $game->uuid) }}';
                });
        }

        // Fallback: Polling pour vérifier si un adversaire a rejoint (si WebSocket ne fonctionne pas)
        setInterval(async () => {
            try {
                const response = await fetch('{{ route('game.state', $game->uuid) }}');
                const data = await response.json();

                if (data.status === 'in_progress') {
                    window.location.href = '{{ route('game.play', $game->uuid) }}';
                }
            } catch (e) {
                console.error('Erreur polling:', e);
            }
        }, 5000);
    </script>
    @endpush
</x-app-layout>
