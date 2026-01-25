<x-app-layout>
    <div class="min-h-[calc(100vh-12rem)] flex items-center justify-center py-8 sm:py-12 px-4">
        <div class="w-full max-w-lg">
            <div class="card-glass rounded-2xl p-5 sm:p-8">
                <!-- Header -->
                <div class="text-center mb-6">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center">
                        <span class="text-4xl">♚</span>
                    </div>
                    <h1 class="font-gaming text-xl sm:text-2xl mb-2">Rejoindre la partie</h1>
                    <p class="text-gray-400 text-sm">Choisissez votre famille de pions</p>
                </div>

                <!-- Info adversaire -->
                <div class="bg-white/5 rounded-xl p-4 mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-gray-600 to-gray-800 flex items-center justify-center font-bold">
                            {{ strtoupper(substr($game->whitePlayer->name, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-400">Vous affrontez</div>
                            <div class="font-medium">{{ $game->whitePlayer->name }}</div>
                            <div class="text-xs text-gray-500">
                                Joue avec : <span class="text-indigo-400">{{ $game->whiteTheme?->name ?? 'Classique' }}</span>
                            </div>
                        </div>
                        <div class="text-3xl">♔</div>
                    </div>
                </div>

                <!-- Formulaire de sélection -->
                <form action="{{ route('lobby.join', $game->uuid) }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Sélection du thème -->
                    <div>
                        <label class="block text-sm text-gray-400 mb-3">Votre thème (pièces noires)</label>

                        <div class="grid grid-cols-1 gap-3 max-h-64 overflow-y-auto pr-2">
                            @foreach($themes as $theme)
                                <label class="relative flex items-center p-4 bg-white/5 border border-white/10 rounded-xl cursor-pointer hover:bg-white/10 transition has-[:checked]:bg-indigo-500/20 has-[:checked]:border-indigo-500">
                                    <input type="radio" name="theme_id" value="{{ $theme->id }}" class="sr-only" {{ $loop->first ? 'checked' : '' }}>
                                    <div class="flex-1">
                                        <div class="font-medium">{{ $theme->name }}</div>
                                        @if($theme->description)
                                            <div class="text-xs text-gray-400 mt-1">{{ Str::limit($theme->description, 60) }}</div>
                                        @endif
                                    </div>
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: {{ $theme->primary_color }}20; border: 1px solid {{ $theme->primary_color }}50;">
                                        <span class="text-lg">♚</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Infos partie -->
                    <div class="bg-white/5 rounded-xl p-3 text-sm">
                        <div class="flex justify-between text-gray-400">
                            <span>Timer</span>
                            <span class="text-white">
                                @if($game->timer_enabled)
                                    {{ $game->timer_minutes }} min
                                    @if($game->timer_increment > 0)
                                        +{{ $game->timer_increment }}s
                                    @endif
                                @else
                                    Sans limite
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('lobby') }}" class="flex-1 py-3 rounded-xl border border-white/20 hover:bg-white/5 transition text-center">
                            Annuler
                        </a>
                        <button type="submit" class="flex-1 btn-primary py-3 rounded-xl font-gaming">
                            Rejoindre la partie
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
