<x-app-layout>
    @php
        $themeStyles = $themeStyles ?? [];
    @endphp

    @push('styles')
    <style>
        /* Variables CSS du thème */
        :root {
            --theme-primary: {{ $themeStyles['primaryColor'] ?? '#6366f1' }};
            --theme-secondary: {{ $themeStyles['secondaryColor'] ?? '#8b5cf6' }};
            --theme-accent: {{ $themeStyles['accentColor'] ?? '#f59e0b' }};
        }

        @if(!empty($themeStyles['squareDark1']))
        /* Cases avec couleurs du thème */
        .chess-square.light {
            background: linear-gradient(135deg, {{ $themeStyles['squareLight1'] }} 0%, {{ $themeStyles['squareLight2'] }} 100%) !important;
        }

        .chess-square.dark {
            background: linear-gradient(135deg, {{ $themeStyles['squareDark1'] }} 0%, {{ $themeStyles['squareDark2'] }} 100%) !important;
        }
        @endif

        /* Background du thème */
        @if(!empty($themeStyles['backgroundImage']))
        .game-page-container {
            position: relative;
            min-height: 100vh;
        }
        .game-page-container::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('{{ $themeStyles['backgroundImage'] }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: -2;
            pointer-events: none;
        }
        /* Overlay sombre pour lisibilité */
        .game-page-container::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(10, 10, 26, 0.7) 0%, rgba(30, 20, 50, 0.75) 100%);
            z-index: -1;
            pointer-events: none;
        }
        @endif

        /* Échiquier */
        .chess-board-container {
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease;
            width: 100%;
        }

        .chess-board-container.fullscreen-board {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(10, 10, 26, 0.98);
            z-index: 1000;
            padding: 20px;
        }

        .chess-board {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            border-radius: 12px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transition: width 0.3s ease, height 0.3s ease;
            max-width: 100%;
            touch-action: manipulation;
        }

        .chess-square {
            position: relative;
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            height: 100%;
            width: 100%;
            -webkit-tap-highlight-color: transparent;
        }

        .chess-square.light {
            background: linear-gradient(135deg, var(--square-light-1, #e8d5b7) 0%, var(--square-light-2, #d4c4a8) 100%);
        }

        .chess-square.dark {
            background: linear-gradient(135deg, var(--square-dark-1, #8b7355) 0%, var(--square-dark-2, #6d5a45) 100%);
        }

        @media (hover: hover) {
            .chess-square:hover {
                filter: brightness(1.1);
            }
        }

        .chess-square.selected {
            box-shadow: inset 0 0 0 4px var(--theme-accent) !important;
        }

        .chess-square.legal-move::after {
            content: '';
            position: absolute;
            width: 30%;
            height: 30%;
            background: var(--theme-primary);
            opacity: 0.6;
            border-radius: 50%;
            z-index: 5;
        }

        .chess-square.legal-capture {
            box-shadow: inset 0 0 0 4px rgba(239, 68, 68, 0.8) !important;
        }

        .chess-square.last-move {
            /* La couleur est maintenant gérée via ::before avec var(--theme-accent) */
        }

        .chess-square.check {
            background: rgba(239, 68, 68, 0.6) !important;
        }

        /* Pièces (cartes miniatures) */
        .chess-piece {
            width: 88%;
            height: 88%;
            border-radius: 6px;
            background: linear-gradient(145deg, #ffffff 0%, #e8e8e8 100%);
            box-shadow: 0 3px 10px rgba(0,0,0,0.3);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: grab;
            transition: transform 0.15s;
            user-select: none;
            z-index: 10;
            color: #1a1a1a;
            touch-action: manipulation;
        }

        .chess-piece.black-piece {
            background: linear-gradient(145deg, #4a4a4a 0%, #2a2a2a 100%);
            color: #f0f0f0;
        }

        @media (hover: hover) {
            .chess-piece:hover {
                transform: scale(1.08);
                z-index: 20;
            }
        }

        .chess-piece.dragging {
            opacity: 0.5;
            cursor: grabbing;
        }

        .piece-icon {
            font-size: 1.6rem;
            line-height: 1;
        }

        /* Responsive piece icon */
        @media (max-width: 480px) {
            .piece-icon {
                font-size: 1.2rem;
            }
            .piece-name {
                display: none;
            }
        }

        .card-image {
            width: 90%;
            height: 75%;
            object-fit: contain;
            border-radius: 4px;
        }

        .piece-name {
            font-size: 0.4rem;
            font-weight: 600;
            text-align: center;
            line-height: 1.1;
            max-width: 95%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin-top: 1px;
            opacity: 0.9;
        }

        /* Carte de détail */
        .card-detail {
            background: linear-gradient(135deg, rgba(99,102,241,0.2) 0%, rgba(139,92,246,0.2) 100%);
            border: 2px solid rgba(99,102,241,0.3);
        }

        .card-detail.enemy {
            background: linear-gradient(135deg, rgba(239,68,68,0.2) 0%, rgba(185,28,28,0.2) 100%);
            border-color: rgba(239,68,68,0.3);
        }

        /* Stats bars */
        .stat-bar {
            height: 6px;
            background: rgba(255,255,255,0.1);
            border-radius: 3px;
            overflow: hidden;
        }

        .stat-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.5s ease;
        }

        /* ========================================
           ANIMATIONS DU JEU
           ======================================== */

        /* Animation de sélection de pièce */
        @keyframes piece-select {
            0% { transform: scale(1); }
            50% { transform: scale(1.15); }
            100% { transform: scale(1.08); }
        }

        .chess-piece.selected-piece {
            animation: piece-select 0.3s ease-out forwards;
            box-shadow: 0 0 20px var(--theme-accent), 0 5px 15px rgba(0,0,0,0.4);
            z-index: 30 !important;
        }

        /* Animation de déplacement fluide */
        @keyframes piece-move {
            0% { transform: scale(1.1); opacity: 0.8; }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); opacity: 1; }
        }

        .chess-piece.just-moved {
            animation: piece-move 0.4s ease-out;
        }

        /* Animation de capture / destruction */
        @keyframes capture-explosion {
            0% {
                transform: scale(1) rotate(0deg);
                opacity: 1;
                filter: brightness(1);
            }
            20% {
                transform: scale(1.3) rotate(5deg);
                filter: brightness(2);
            }
            40% {
                transform: scale(0.8) rotate(-10deg);
                filter: brightness(1.5) hue-rotate(30deg);
            }
            100% {
                transform: scale(0) rotate(180deg);
                opacity: 0;
                filter: brightness(3) blur(10px);
            }
        }

        .chess-piece.captured {
            animation: capture-explosion 0.5s ease-out forwards;
            pointer-events: none;
        }

        /* Particules d'explosion */
        @keyframes particle-fly {
            0% {
                transform: translate(0, 0) scale(1);
                opacity: 1;
            }
            100% {
                transform: translate(var(--tx), var(--ty)) scale(0);
                opacity: 0;
            }
        }

        .capture-particle {
            position: absolute;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            pointer-events: none;
            z-index: 100;
            animation: particle-fly 0.6s ease-out forwards;
        }

        /* Animation échec (roi en danger) */
        @keyframes king-check {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.8);
            }
            50% {
                box-shadow: 0 0 30px 10px rgba(239, 68, 68, 0.6);
            }
        }

        .chess-square.check {
            animation: king-check 1s ease-in-out infinite;
            background: rgba(239, 68, 68, 0.5) !important;
        }

        .chess-piece.in-check {
            animation: king-check 1s ease-in-out infinite;
            border: 2px solid #ef4444 !important;
        }

        /* Animation échec et mat */
        @keyframes checkmate-shake {
            0%, 100% { transform: translateX(0) rotate(0deg); }
            10% { transform: translateX(-5px) rotate(-2deg); }
            20% { transform: translateX(5px) rotate(2deg); }
            30% { transform: translateX(-5px) rotate(-2deg); }
            40% { transform: translateX(5px) rotate(2deg); }
            50% { transform: translateX(-3px) rotate(-1deg); }
            60% { transform: translateX(3px) rotate(1deg); }
            70% { transform: translateX(-2px) rotate(0deg); }
            80% { transform: translateX(2px) rotate(0deg); }
            90% { transform: translateX(-1px) rotate(0deg); }
        }

        .chess-piece.checkmated {
            animation: checkmate-shake 0.8s ease-out;
            filter: grayscale(50%) brightness(0.7);
        }

        /* Animation victoire */
        @keyframes victory-glow {
            0%, 100% {
                box-shadow: 0 0 20px rgba(34, 197, 94, 0.5);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 0 40px rgba(34, 197, 94, 0.8);
                transform: scale(1.05);
            }
        }

        .chess-piece.victory {
            animation: victory-glow 1s ease-in-out infinite;
        }

        /* Animation dernière case jouée */
        .chess-square.last-move {
            position: relative;
        }

        .chess-square.last-move::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--theme-accent);
            opacity: 0.35;
            animation: last-move-pulse 2s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes last-move-pulse {
            0%, 100% { opacity: 0.25; }
            50% { opacity: 0.45; }
        }

        /* Animation coups légaux */
        @keyframes legal-dot-pulse {
            0%, 100% { transform: scale(1); opacity: 0.6; }
            50% { transform: scale(1.2); opacity: 0.9; }
        }

        .chess-square.legal-move::after {
            animation: legal-dot-pulse 1s ease-in-out infinite;
        }

        /* Animation capture possible */
        @keyframes capture-ring-pulse {
            0%, 100% {
                box-shadow: inset 0 0 0 4px rgba(239, 68, 68, 0.6);
            }
            50% {
                box-shadow: inset 0 0 0 6px rgba(239, 68, 68, 0.9);
            }
        }

        .chess-square.legal-capture {
            animation: capture-ring-pulse 1s ease-in-out infinite;
        }

        /* Animation indicateur de tour */
        @keyframes turn-indicator {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.05); }
        }

        .turn-indicator-active {
            animation: turn-indicator 1.5s ease-in-out infinite;
        }

        /* Animation d'entrée du plateau */
        @keyframes board-enter {
            0% {
                opacity: 0;
                transform: scale(0.9) rotateX(10deg);
            }
            100% {
                opacity: 1;
                transform: scale(1) rotateX(0deg);
            }
        }

        .chess-board {
            animation: board-enter 0.6s ease-out;
        }

        /* Overlay de fin de partie */
        @keyframes overlay-appear {
            0% { opacity: 0; transform: scale(0.8); }
            100% { opacity: 1; transform: scale(1); }
        }

        .game-end-overlay {
            animation: overlay-appear 0.5s ease-out;
        }

        /* Confetti pour victoire */
        @keyframes confetti-fall {
            0% {
                transform: translateY(-100vh) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }

        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            top: 0;
            pointer-events: none;
            z-index: 1000;
            animation: confetti-fall 3s linear forwards;
        }

        /* Historique des coups */
        .move-item {
            display: flex;
            padding: 0.25rem 0.5rem;
            font-family: monospace;
            font-size: 0.85rem;
        }

        .move-item:nth-child(odd) {
            background: rgba(255,255,255,0.03);
        }

        .move-number {
            width: 2rem;
            color: #9ca3af;
        }

        .move-white, .move-black {
            width: 4rem;
        }

        /* Mobile game layout */
        @media (max-width: 1024px) {
            .game-sidebar {
                order: 2;
            }
            .game-board-area {
                order: 1;
            }
        }

        /* Mobile header */
        .mobile-game-header {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        @media (max-width: 640px) {
            .mobile-game-header {
                flex-direction: column;
            }
            .mobile-game-header > div {
                width: 100%;
                justify-content: space-between;
            }
        }

        /* Bottom panel mobile pour les infos de jeu */
        .mobile-bottom-panel {
            display: none;
        }

        @media (max-width: 1024px) {
            .mobile-bottom-panel {
                display: block;
            }
            .desktop-sidebar {
                display: none;
            }
        }

        @media (min-width: 1025px) {
            .desktop-sidebar {
                display: block;
            }
        }

        /* Sliders de volume */
        input[type="range"].volume-slider {
            -webkit-appearance: none;
            appearance: none;
            height: 6px;
            background: #374151;
            border-radius: 3px;
            cursor: pointer;
        }

        input[type="range"].volume-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 16px;
            height: 16px;
            background: white;
            border-radius: 50%;
            box-shadow: 0 2px 6px rgba(0,0,0,0.4);
            transition: transform 0.15s, box-shadow 0.15s;
        }

        input[type="range"].volume-slider::-moz-range-thumb {
            width: 16px;
            height: 16px;
            background: white;
            border-radius: 50%;
            border: none;
            box-shadow: 0 2px 6px rgba(0,0,0,0.4);
        }

        input[type="range"].volume-slider::-webkit-slider-thumb:hover {
            transform: scale(1.15);
            box-shadow: 0 2px 8px rgba(0,0,0,0.5);
        }

        input[type="range"].volume-slider.music-slider {
            background: linear-gradient(to right, #8b5cf6 0%, #8b5cf6 var(--val, 30%), #374151 var(--val, 30%), #374151 100%);
        }

        input[type="range"].volume-slider.sfx-slider {
            background: linear-gradient(to right, #22c55e 0%, #22c55e var(--val, 70%), #374151 var(--val, 70%), #374151 100%);
        }
    </style>
    @endpush

    <div class="game-page-container mx-auto px-2 sm:px-4 py-4 sm:py-6" x-data="chessGame()" x-init="init()">
        <!-- Header de la partie -->
        <div class="mobile-game-header mb-4 sm:mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <a href="{{ route('lobby') }}" class="text-gray-400 hover:text-white transition text-sm sm:text-base">
                        ← <span class="hidden sm:inline">Retour</span>
                    </a>
                    <div class="h-6 w-px bg-white/20 hidden sm:block"></div>
                    <span class="font-gaming text-sm sm:text-lg truncate max-w-[150px] sm:max-w-none">{{ $game->theme?->name ?? 'Partie Classique' }}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <!-- Contrôles audio -->
                    <div class="relative" x-data="{ showAudioPanel: false }">
                        <button @click="showAudioPanel = !showAudioPanel"
                                class="flex items-center space-x-1 bg-white/5 rounded-lg px-3 py-1.5 border border-white/10 hover:bg-white/10 transition">
                            <span x-text="musicPlaying || soundEnabled ? '🔊' : '🔇'"></span>
                            <span class="hidden sm:inline text-xs text-gray-300">Audio</span>
                            <span class="text-xs text-gray-500" x-text="showAudioPanel ? '▲' : '▼'"></span>
                        </button>

                        <!-- Panneau de contrôle audio -->
                        <div x-show="showAudioPanel"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             @click.outside="showAudioPanel = false"
                             class="absolute right-0 top-full mt-2 w-64 bg-gray-900/95 backdrop-blur-sm border border-white/10 rounded-xl p-4 shadow-xl z-50">

                            <h4 class="text-sm font-gaming text-gray-300 mb-4">Paramètres Audio</h4>

                            <!-- Musique de fond -->
                            <div class="mb-4" x-show="themeSounds?.music">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-sm text-gray-400 flex items-center space-x-2">
                                        <span>🎵</span>
                                        <span>Musique</span>
                                    </label>
                                    <button @click="toggleMusic()"
                                            class="px-2 py-0.5 rounded text-xs transition"
                                            :class="musicPlaying ? 'bg-purple-500/30 text-purple-300' : 'bg-gray-700 text-gray-400'">
                                        <span x-text="musicPlaying ? 'ON' : 'OFF'"></span>
                                    </button>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="text-xs text-gray-500">🔈</span>
                                    <input type="range"
                                           min="0" max="100"
                                           :value="musicVolume * 100"
                                           @input="setMusicVolume($event.target.value / 100)"
                                           :style="'--val: ' + (musicVolume * 100) + '%'"
                                           class="flex-1 volume-slider music-slider">
                                    <span class="text-xs text-gray-500">🔊</span>
                                    <span class="text-xs text-gray-400 w-8 text-right" x-text="Math.round(musicVolume * 100) + '%'"></span>
                                </div>
                            </div>

                            <!-- Effets sonores -->
                            <div class="mb-2">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-sm text-gray-400 flex items-center space-x-2">
                                        <span>🔔</span>
                                        <span>Effets sonores</span>
                                    </label>
                                    <button @click="toggleSound()"
                                            class="px-2 py-0.5 rounded text-xs transition"
                                            :class="soundEnabled ? 'bg-green-500/30 text-green-300' : 'bg-gray-700 text-gray-400'">
                                        <span x-text="soundEnabled ? 'ON' : 'OFF'"></span>
                                    </button>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="text-xs text-gray-500">🔈</span>
                                    <input type="range"
                                           min="0" max="100"
                                           :value="soundVolume * 100"
                                           @input="setSoundVolume($event.target.value / 100)"
                                           :style="'--val: ' + (soundVolume * 100) + '%'"
                                           class="flex-1 volume-slider sfx-slider">
                                    <span class="text-xs text-gray-500">🔊</span>
                                    <span class="text-xs text-gray-400 w-8 text-right" x-text="Math.round(soundVolume * 100) + '%'"></span>
                                </div>
                            </div>

                            <!-- Bouton tout couper -->
                            <div class="border-t border-white/10 pt-3 mt-3">
                                <button @click="muteAll()"
                                        class="w-full px-3 py-2 bg-red-500/20 hover:bg-red-500/30 border border-red-500/30 text-red-400 rounded-lg text-xs transition">
                                    🔇 Tout couper
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Contrôles de zoom (masqués sur très petit écran) -->
                    <div class="hidden sm:flex items-center space-x-2 bg-white/5 rounded-lg px-3 py-1">
                        <button @click="zoomOut()" class="text-gray-400 hover:text-white transition text-lg px-1" title="Réduire">
                            −
                        </button>
                        <span class="text-sm text-gray-300 w-12 text-center" x-text="zoomLevel + '%'"></span>
                        <button @click="zoomIn()" class="text-gray-400 hover:text-white transition text-lg px-1" title="Agrandir">
                            +
                        </button>
                        <button @click="resetZoom()" class="text-gray-400 hover:text-white transition text-xs ml-1" title="Réinitialiser">
                            ↺
                        </button>
                    </div>
                    <!-- Plein écran -->
                    <button @click="toggleFullscreen()" class="p-2 bg-white/5 border border-white/10 rounded-lg hover:bg-white/10 transition" title="Plein écran">
                        <span x-show="!isFullscreen">⛶</span>
                        <span x-show="isFullscreen">⛷</span>
                    </button>
                </div>
            </div>

            @if($game->isInProgress())
            <div class="flex items-center justify-end space-x-2 mt-2 sm:mt-0">
                <button @click="offerDraw()" class="px-3 sm:px-4 py-2 bg-white/5 border border-white/10 rounded-lg hover:bg-white/10 transition text-xs sm:text-sm">
                    ½ Nulle
                </button>
                <form action="{{ route('game.resign', $game->uuid) }}" method="POST"
                      onsubmit="return confirm('Voulez-vous vraiment abandonner ?')">
                    @csrf
                    <button type="submit" class="px-3 sm:px-4 py-2 bg-red-500/20 border border-red-500/30 text-red-400 rounded-lg hover:bg-red-500/30 transition text-xs sm:text-sm">
                        Abandonner
                    </button>
                </form>
            </div>
            @endif
        </div>

        <!-- Infos joueurs compactes pour mobile -->
        <div class="lg:hidden flex justify-between items-center mb-4 gap-2">
            <!-- Adversaire compact -->
            <div class="card-glass rounded-xl px-3 py-2 flex items-center space-x-2 flex-1">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-gray-600 to-gray-800 flex items-center justify-center font-bold text-sm">
                    @if($game->isAiGame())
                        🤖
                    @else
                        {{ $playerColor === 'white' ? strtoupper(substr($game->blackPlayer?->name ?? '?', 0, 1)) : strtoupper(substr($game->whitePlayer->name, 0, 1)) }}
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-sm truncate">
                        @if($game->isAiGame())
                            IA (Niv. {{ $game->ai_level }})
                        @else
                            {{ $playerColor === 'white' ? ($game->blackPlayer?->name ?? 'En attente...') : $game->whitePlayer->name }}
                        @endif
                    </div>
                </div>
            </div>

            <!-- VS -->
            <div class="text-gray-500 text-sm font-gaming">VS</div>

            <!-- Joueur compact -->
            <div class="card-glass rounded-xl px-3 py-2 flex items-center space-x-2 flex-1 border border-indigo-500/30">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center font-bold text-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-sm truncate">{{ auth()->user()->name }}</div>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-4 gap-4 lg:gap-6">
            <!-- Colonne gauche : Info adversaire + historique (desktop only) -->
            <div class="hidden lg:block lg:col-span-1 space-y-4 desktop-sidebar">
                <!-- Adversaire -->
                <div class="card-glass rounded-xl p-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-gray-600 to-gray-800 flex items-center justify-center font-bold">
                            @if($game->isAiGame())
                                🤖
                            @else
                                {{ $playerColor === 'white' ? strtoupper(substr($game->blackPlayer?->name ?? '?', 0, 1)) : strtoupper(substr($game->whitePlayer->name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="font-medium">
                                @if($game->isAiGame())
                                    Stockfish (Niv. {{ $game->ai_level }})
                                @else
                                    {{ $playerColor === 'white' ? ($game->blackPlayer?->name ?? 'En attente...') : $game->whitePlayer->name }}
                                @endif
                            </div>
                            <div class="text-xs text-gray-400">
                                Joue les {{ $playerColor === 'white' ? 'Noirs' : 'Blancs' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte sélectionnée -->
                <div class="card-glass rounded-xl p-4" x-show="selectedCard" x-transition>
                    <div class="card-detail rounded-xl p-4" :class="{ 'enemy': selectedCard?.isEnemy }">
                        <div class="text-center mb-3">
                            <div class="text-4xl mb-2" x-text="selectedCard?.icon"></div>
                            <div class="font-gaming text-lg" x-text="selectedCard?.name"></div>
                            <div class="text-xs text-gray-400" x-text="selectedCard?.type"></div>
                        </div>
                        <p class="text-sm text-gray-300 italic mb-4" x-show="selectedCard?.quote">
                            « <span x-text="selectedCard?.quote"></span> »
                        </p>
                        <div class="space-y-2">
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span>Attaque</span>
                                    <span x-text="selectedCard?.attack"></span>
                                </div>
                                <div class="stat-bar">
                                    <div class="stat-fill bg-red-500" :style="'width:' + selectedCard?.attack + '%'"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span>Défense</span>
                                    <span x-text="selectedCard?.defense"></span>
                                </div>
                                <div class="stat-bar">
                                    <div class="stat-fill bg-blue-500" :style="'width:' + selectedCard?.defense + '%'"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span>Vitesse</span>
                                    <span x-text="selectedCard?.speed"></span>
                                </div>
                                <div class="stat-bar">
                                    <div class="stat-fill bg-green-500" :style="'width:' + selectedCard?.speed + '%'"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historique des coups -->
                <div class="card-glass rounded-xl p-4">
                    <h3 class="font-gaming text-sm mb-3">Historique</h3>
                    <div class="max-h-48 overflow-y-auto" id="moves-history">
                        <template x-for="(movePair, index) in movesHistory" :key="index">
                            <div class="move-item">
                                <span class="move-number" x-text="(index + 1) + '.'"></span>
                                <span class="move-white text-white" x-text="movePair[0] || ''"></span>
                                <span class="move-black text-gray-400" x-text="movePair[1] || ''"></span>
                            </div>
                        </template>
                        <div x-show="movesHistory.length === 0" class="text-gray-500 text-sm text-center py-2">
                            Aucun coup joué
                        </div>
                    </div>
                </div>
            </div>

            <!-- Échiquier -->
            <div class="lg:col-span-2 flex flex-col items-center game-board-area">
                <div class="chess-board-container" :class="{ 'fullscreen-board': isFullscreen }">
                    <!-- Bouton fermer en plein écran -->
                    <button x-show="isFullscreen" 
                            @click="toggleFullscreen()" 
                            class="absolute top-4 right-4 z-50 p-3 bg-white/10 hover:bg-white/20 rounded-full transition text-2xl"
                            title="Quitter le plein écran (Echap)">
                        ✕
                    </button>
                    <!-- Contrôles zoom en plein écran -->
                    <div x-show="isFullscreen" class="absolute top-4 left-4 z-50 flex items-center space-x-2 bg-black/50 rounded-lg px-3 py-2">
                        <button @click="zoomOut()" class="text-gray-300 hover:text-white transition text-xl px-2">−</button>
                        <span class="text-sm text-gray-300 w-12 text-center" x-text="zoomLevel + '%'"></span>
                        <button @click="zoomIn()" class="text-gray-300 hover:text-white transition text-xl px-2">+</button>
                    </div>
                    <div class="chess-board" :style="'width: ' + boardSize + 'px; height: ' + boardSize + 'px;'">
                    <template x-for="(row, rowIndex) in displayBoard" :key="rowIndex">
                        <template x-for="(square, colIndex) in row" :key="rowIndex + '-' + colIndex">
                            <div class="chess-square"
                                 :class="[
                                     getSquareColor(rowIndex, colIndex),
                                     { 'selected': isSelected(rowIndex, colIndex) },
                                     { 'legal-move': isLegalMove(rowIndex, colIndex) && !square },
                                     { 'legal-capture': isLegalMove(rowIndex, colIndex) && square },
                                     { 'last-move': isLastMove(rowIndex, colIndex) },
                                     { 'check': isKingInCheck(rowIndex, colIndex) }
                                 ]"
                                 @click="handleSquareClick(rowIndex, colIndex)"
                                 @dragover.prevent
                                 @drop="handleDrop($event, rowIndex, colIndex)">
                                
                                <div x-show="square"
                                     class="chess-piece"
                                     :class="{
                                         'black-piece': square?.color === 'b',
                                         'selected-piece': isSelectedPiece(rowIndex, colIndex),
                                         'just-moved': isJustMoved(rowIndex, colIndex),
                                         'in-check': isPieceInCheck(rowIndex, colIndex),
                                         'checkmated': isCheckmated(rowIndex, colIndex),
                                         'victory': isVictoryPiece(rowIndex, colIndex)
                                     }"
                                     draggable="true"
                                     @dragstart="handleDragStart($event, rowIndex, colIndex)"
                                     @dragend="handleDragEnd($event)"
                                     :data-square="getSquareNotation(rowIndex, colIndex)"
                                    <!-- Si la carte a une image -->
                                    <template x-if="getCardImage(square)">
                                        <img :src="getCardImage(square)" :alt="getCardName(square)" class="card-image">
                                    </template>
                                    <!-- Sinon, afficher l'icône unicode -->
                                    <template x-if="!getCardImage(square)">
                                        <span class="piece-icon" x-text="getPieceIcon(square)"></span>
                                    </template>
                                    <span class="piece-name" x-text="getCardName(square)"></span>
                                </div>
                            </div>
                        </template>
                    </template>
                    </div>
                </div>

                <!-- Statut de la partie -->
                <div class="mt-4 w-full flex justify-center">
                    <div class="text-center">
                        <div x-show="gameStatus === 'checkmate'"
                             class="game-end-overlay px-6 py-3 rounded-xl"
                             :class="winner === playerColor ? 'bg-green-500/20 border border-green-500/50' : 'bg-red-500/20 border border-red-500/50'">
                            <div class="text-2xl font-gaming" :class="winner === playerColor ? 'text-green-400' : 'text-red-400'">
                                Échec et Mat !
                            </div>
                            <div class="text-3xl mt-2" x-text="winner === playerColor ? '🎉 Victoire !' : '😔 Défaite'"></div>
                        </div>
                        <div x-show="gameStatus === 'stalemate'" class="game-end-overlay px-6 py-3 rounded-xl bg-gray-500/20 border border-gray-500/50">
                            <div class="text-2xl font-gaming text-gray-400">Pat</div>
                            <div class="text-xl mt-1 text-gray-300">Match Nul 🤝</div>
                        </div>
                        <div x-show="gameStatus === 'draw'" class="game-end-overlay px-6 py-3 rounded-xl bg-gray-500/20 border border-gray-500/50">
                            <div class="text-2xl font-gaming text-gray-400">Match Nul 🤝</div>
                        </div>
                        <div x-show="gameStatus === 'playing' && isMyTurn"
                             class="text-lg text-indigo-400 turn-indicator-active px-4 py-2 rounded-lg bg-indigo-500/10 border border-indigo-500/30">
                            ⚔️ À vous de jouer !
                        </div>
                        <div x-show="gameStatus === 'playing' && !isMyTurn" class="text-lg text-gray-400">
                            <span x-show="isAiGame" class="inline-flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                L'IA réfléchit...
                            </span>
                            <span x-show="!isAiGame">⏳ En attente de l'adversaire...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne droite : Info joueur (desktop only) -->
            <div class="hidden lg:block lg:col-span-1 space-y-4 desktop-sidebar">
                <!-- Joueur -->
                <div class="card-glass rounded-xl p-4 border border-indigo-500/30">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <div class="font-medium">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-gray-400">
                                Joue les <span class="{{ $playerColor === 'white' ? 'text-gray-200' : 'text-gray-400' }}">{{ $playerColor === 'white' ? 'Blancs' : 'Noirs' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pièces capturées -->
                <div class="card-glass rounded-xl p-4">
                    <h3 class="font-gaming text-sm mb-3">Pièces capturées</h3>
                    <div class="mb-2">
                        <span class="text-xs text-gray-400">Par vous :</span>
                        <div class="flex flex-wrap gap-1 mt-1 min-h-[2rem]">
                            <template x-for="(piece, idx) in capturedByMe" :key="'me-'+idx">
                                <span class="text-xl" x-text="getCapturedIcon(piece)"></span>
                            </template>
                            <span x-show="capturedByMe.length === 0" class="text-gray-600 text-sm">-</span>
                        </div>
                    </div>
                    <div class="border-t border-white/10 pt-2">
                        <span class="text-xs text-gray-400">Par l'adversaire :</span>
                        <div class="flex flex-wrap gap-1 mt-1 min-h-[2rem]">
                            <template x-for="(piece, idx) in capturedByOpponent" :key="'opp-'+idx">
                                <span class="text-xl" x-text="getCapturedIcon(piece)"></span>
                            </template>
                            <span x-show="capturedByOpponent.length === 0" class="text-gray-600 text-sm">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel mobile pour les infos supplémentaires -->
        <div class="lg:hidden mt-4 space-y-3" x-data="{ showHistory: false, showCaptures: false }">
            <!-- Pièces capturées (compact) -->
            <div class="card-glass rounded-xl p-3">
                <button @click="showCaptures = !showCaptures" class="w-full flex items-center justify-between">
                    <h3 class="font-gaming text-sm">Pièces capturées</h3>
                    <span class="text-gray-400 text-xs" x-text="showCaptures ? '▲' : '▼'"></span>
                </button>
                <div x-show="showCaptures" x-collapse class="mt-3">
                    <div class="flex justify-between gap-4">
                        <div class="flex-1">
                            <span class="text-xs text-gray-400">Vous :</span>
                            <div class="flex flex-wrap gap-1 mt-1">
                                <template x-for="(piece, idx) in capturedByMe" :key="'me-m-'+idx">
                                    <span class="text-lg" x-text="getCapturedIcon(piece)"></span>
                                </template>
                                <span x-show="capturedByMe.length === 0" class="text-gray-600 text-sm">-</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <span class="text-xs text-gray-400">Adversaire :</span>
                            <div class="flex flex-wrap gap-1 mt-1">
                                <template x-for="(piece, idx) in capturedByOpponent" :key="'opp-m-'+idx">
                                    <span class="text-lg" x-text="getCapturedIcon(piece)"></span>
                                </template>
                                <span x-show="capturedByOpponent.length === 0" class="text-gray-600 text-sm">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historique (accordéon) -->
            <div class="card-glass rounded-xl p-3">
                <button @click="showHistory = !showHistory" class="w-full flex items-center justify-between">
                    <h3 class="font-gaming text-sm">Historique des coups</h3>
                    <span class="text-gray-400 text-xs" x-text="showHistory ? '▲' : '▼'"></span>
                </button>
                <div x-show="showHistory" x-collapse class="mt-3 max-h-32 overflow-y-auto">
                    <template x-for="(movePair, index) in movesHistory" :key="'m-'+index">
                        <div class="move-item text-sm">
                            <span class="move-number" x-text="(index + 1) + '.'"></span>
                            <span class="move-white text-white" x-text="movePair[0] || ''"></span>
                            <span class="move-black text-gray-400" x-text="movePair[1] || ''"></span>
                        </div>
                    </template>
                    <div x-show="movesHistory.length === 0" class="text-gray-500 text-sm text-center py-2">
                        Aucun coup joué
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function chessGame() {
            return {
                // Config
                gameUuid: '{{ $game->uuid }}',
                playerColor: '{{ $playerColor ?? "white" }}',
                isAiGame: {{ $game->isAiGame() ? 'true' : 'false' }},
                
                // État du jeu
                chess: null,
                board: [],
                displayBoard: [],
                selectedSquare: null,
                legalMoves: [],
                lastMove: null,
                movesHistory: [],
                gameStatus: 'playing',
                winner: null,
                currentTurn: 'white',
                isMyTurn: false,
                
                // Cartes
                cards: @json($cards ?? []),
                selectedCard: null,
                
                // Pièces capturées
                capturedByMe: [],
                capturedByOpponent: [],

                // Animations
                animatingSquare: null,
                lastMovedSquare: null,

                // Sons et musique
                themeSounds: @json($themeSounds ?? null),
                musicPlayer: null,
                musicPlaying: false,
                soundEnabled: true,
                musicVolume: 0.3,
                soundVolume: 0.7,
                activeSounds: {}, // Pour éviter les chevauchements

                // Zoom
                zoomLevel: 100,
                baseSize: 560,
                isFullscreen: false,
                isMobile: false,

                get boardSize() {
                    return Math.round(this.baseSize * this.zoomLevel / 100);
                },

                calculateBaseSize() {
                    const screenWidth = window.innerWidth;
                    const screenHeight = window.innerHeight;

                    if (this.isFullscreen) {
                        const screenMin = Math.min(screenWidth, screenHeight) - 40;
                        return Math.min(screenMin, 800);
                    }

                    // Mobile : utiliser la largeur disponible moins les marges
                    if (screenWidth < 640) {
                        return Math.min(screenWidth - 16, 400);
                    }
                    // Tablet
                    if (screenWidth < 1024) {
                        return Math.min(screenWidth - 32, 480);
                    }
                    // Desktop
                    return 560;
                },

                init() {
                    console.log('Init FanChess - Player:', this.playerColor);

                    // Calculer la taille initiale de l'échiquier
                    this.isMobile = window.innerWidth < 1024;
                    this.baseSize = this.calculateBaseSize();

                    this.chess = new Chess('{{ $game->current_fen }}');
                    this.updateBoard();
                    this.loadMovesHistory();
                    this.updateTurn();

                    // Si c'est une partie IA et que l'IA joue en premier (joueur = noir)
                    if (this.isAiGame && this.playerColor === 'black' && this.currentTurn === 'white') {
                        setTimeout(() => this.makeAiMove(), 1000);
                    }

                    // Touche Escape pour quitter le plein écran
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && this.isFullscreen) {
                            this.isFullscreen = false;
                            this.baseSize = this.calculateBaseSize();
                            this.zoomLevel = 100;
                        }
                    });

                    // Redimensionner l'échiquier quand la fenêtre change de taille
                    window.addEventListener('resize', () => {
                        this.isMobile = window.innerWidth < 1024;
                        if (!this.isFullscreen) {
                            this.baseSize = this.calculateBaseSize();
                        }
                    });

                    // Écouter les événements WebSocket (uniquement pour les parties PvP)
                    if (!this.isAiGame && window.Echo) {
                        this.setupWebSocket();
                    }

                    // Initialiser la musique
                    this.initMusic();
                },

                setupWebSocket() {
                    console.log('Setting up WebSocket for game:', this.gameUuid);

                    window.Echo.private(`game.${this.gameUuid}`)
                        .listen('.GameMoveMade', (e) => {
                            console.log('Move received:', e);
                            this.handleOpponentMove(e);
                        })
                        .listen('.GameEnded', (e) => {
                            console.log('Game ended:', e);
                            this.handleGameEnded(e);
                        });
                },

                handleOpponentMove(data) {
                    // Appliquer le coup reçu
                    const move = this.chess.move({
                        from: data.from,
                        to: data.to,
                        promotion: data.promotion || undefined
                    });

                    if (move) {
                        // Animation et son de capture
                        if (data.captured) {
                            this.playCaptureAnimation(data.to);
                            this.playSound('capture');
                            const capturedPiece = (this.playerColor === 'white' ? 'w' : 'b') + data.captured;
                            this.capturedByOpponent.push(capturedPiece);
                        } else {
                            this.playSound('move');
                        }

                        this.lastMove = { from: data.from, to: data.to };
                        this.lastMovedSquare = data.to;

                        // Reset l'animation après un délai
                        setTimeout(() => {
                            this.lastMovedSquare = null;
                        }, 400);

                        this.updateBoard();
                        this.updateMovesHistory(move);
                        this.updateTurn();

                        // Vérifier fin de partie
                        if (data.isCheckmate) {
                            this.gameStatus = 'checkmate';
                            this.winner = data.winner;
                            this.playSound('checkmate');
                            // Animation de défaite ou victoire
                            if (this.winner === this.playerColor) {
                                setTimeout(() => {
                                    this.playVictoryAnimation();
                                    this.playSound('victory');
                                }, 500);
                            } else {
                                setTimeout(() => this.playSound('defeat'), 500);
                            }
                        } else {
                            this.checkGameEnd();
                            // Son d'échec
                            if (this.chess.in_check()) {
                                this.playSound('check');
                            }
                        }

                        // Scroll l'historique
                        this.$nextTick(() => {
                            const history = document.getElementById('moves-history');
                            if (history) history.scrollTop = history.scrollHeight;
                        });
                    }
                },

                handleGameEnded(data) {
                    this.gameStatus = data.reason === 'resignation' ? 'resignation' : 'ended';
                    this.winner = data.winnerColor;

                    // Afficher un message
                    if (data.reason === 'resignation') {
                        alert('Votre adversaire a abandonné. Vous avez gagné !');
                        window.location.reload();
                    }
                },

                updateBoard() {
                    // chess.board() retourne [rang8...rang1] donc noirs en haut, blancs en bas
                    this.board = this.chess.board();
                    
                    // Si le joueur joue les noirs, on inverse l'affichage pour avoir ses pièces en bas
                    if (this.playerColor === 'black') {
                        this.displayBoard = this.board.slice().reverse().map(row => row.slice().reverse());
                    } else {
                        this.displayBoard = this.board;
                    }
                },

                displayToReal(displayRow, displayCol) {
                    if (this.playerColor === 'black') {
                        return { row: 7 - displayRow, col: 7 - displayCol };
                    }
                    return { row: displayRow, col: displayCol };
                },

                getSquareColor(displayRow, displayCol) {
                    const { row, col } = this.displayToReal(displayRow, displayCol);
                    return (row + col) % 2 === 0 ? 'light' : 'dark';
                },

                updateTurn() {
                    this.currentTurn = this.chess.turn() === 'w' ? 'white' : 'black';
                    this.isMyTurn = this.currentTurn === this.playerColor;
                },

                getPieceIcon(piece) {
                    if (!piece) return '';
                    const icons = {
                        'wp': '♙', 'wn': '♘', 'wb': '♗', 'wr': '♖', 'wq': '♕', 'wk': '♔',
                        'bp': '♟', 'bn': '♞', 'bb': '♝', 'br': '♜', 'bq': '♛', 'bk': '♚'
                    };
                    return icons[piece.color + piece.type] || '?';
                },

                getCapturedIcon(piece) {
                    const icons = {
                        'wp': '♙', 'wn': '♘', 'wb': '♗', 'wr': '♖', 'wq': '♕',
                        'bp': '♟', 'bn': '♞', 'bb': '♝', 'br': '♜', 'bq': '♛'
                    };
                    return icons[piece] || '?';
                },

                getCardName(piece) {
                    if (!piece) return '';
                    const color = piece.color === 'w' ? 'white' : 'black';
                    const typeMap = { p: 'pawn', n: 'knight', b: 'bishop', r: 'rook', q: 'queen', k: 'king' };
                    const pieceType = typeMap[piece.type];
                    
                    if (this.cards && this.cards[color] && this.cards[color][pieceType]) {
                        return this.cards[color][pieceType].name || '';
                    }
                    return '';
                },

                getCardImage(piece) {
                    if (!piece) return null;
                    const color = piece.color === 'w' ? 'white' : 'black';
                    const typeMap = { p: 'pawn', n: 'knight', b: 'bishop', r: 'rook', q: 'queen', k: 'king' };
                    const pieceType = typeMap[piece.type];
                    
                    const card = this.cards?.[color]?.[pieceType];
                    if (card && card.image) {
                        return '/storage/' + card.image;
                    }
                    return null;
                },

                getCard(piece) {
                    if (!piece) return null;
                    const color = piece.color === 'w' ? 'white' : 'black';
                    const typeMap = { p: 'pawn', n: 'knight', b: 'bishop', r: 'rook', q: 'queen', k: 'king' };
                    const pieceType = typeMap[piece.type];
                    
                    const card = this.cards?.[color]?.[pieceType];
                    if (!card) return null;
                    
                    const pieceNames = { pawn: 'Pion', knight: 'Cavalier', bishop: 'Fou', rook: 'Tour', queen: 'Dame', king: 'Roi' };
                    
                    return {
                        name: card.name,
                        type: pieceNames[pieceType] || pieceType,
                        quote: card.quote || '',
                        icon: this.getPieceIcon(piece),
                        attack: card.attack_visual || 50,
                        defense: card.defense_visual || 50,
                        speed: card.speed_visual || 50,
                        isEnemy: color !== this.playerColor
                    };
                },

                indexToSquare(row, col) {
                    const files = 'abcdefgh';
                    return files[col] + (8 - row);
                },

                squareToIndex(square) {
                    const files = 'abcdefgh';
                    return {
                        row: 8 - parseInt(square[1]),
                        col: files.indexOf(square[0])
                    };
                },

                isSelected(displayRow, displayCol) {
                    if (!this.selectedSquare) return false;
                    const { row, col } = this.displayToReal(displayRow, displayCol);
                    return this.selectedSquare.row === row && this.selectedSquare.col === col;
                },

                isLegalMove(displayRow, displayCol) {
                    const { row, col } = this.displayToReal(displayRow, displayCol);
                    const square = this.indexToSquare(row, col);
                    return this.legalMoves.some(m => m.to === square);
                },

                isLastMove(displayRow, displayCol) {
                    if (!this.lastMove) return false;
                    const { row, col } = this.displayToReal(displayRow, displayCol);
                    const square = this.indexToSquare(row, col);
                    return this.lastMove.from === square || this.lastMove.to === square;
                },

                isKingInCheck(displayRow, displayCol) {
                    if (!this.chess.in_check()) return false;
                    const { row, col } = this.displayToReal(displayRow, displayCol);
                    const piece = this.board[row]?.[col];
                    return piece && piece.type === 'k' && piece.color === this.chess.turn();
                },

                // Fonctions d'animation
                getSquareNotation(displayRow, displayCol) {
                    const { row, col } = this.displayToReal(displayRow, displayCol);
                    return this.indexToSquare(row, col);
                },

                isSelectedPiece(displayRow, displayCol) {
                    if (!this.selectedSquare) return false;
                    const { row, col } = this.displayToReal(displayRow, displayCol);
                    return this.selectedSquare.row === row && this.selectedSquare.col === col;
                },

                isJustMoved(displayRow, displayCol) {
                    if (!this.lastMovedSquare) return false;
                    const { row, col } = this.displayToReal(displayRow, displayCol);
                    const square = this.indexToSquare(row, col);
                    return this.lastMovedSquare === square;
                },

                isPieceInCheck(displayRow, displayCol) {
                    if (!this.chess.in_check()) return false;
                    const { row, col } = this.displayToReal(displayRow, displayCol);
                    const piece = this.board[row]?.[col];
                    return piece && piece.type === 'k' && piece.color === this.chess.turn();
                },

                isCheckmated(displayRow, displayCol) {
                    if (!this.chess.in_checkmate()) return false;
                    const { row, col } = this.displayToReal(displayRow, displayCol);
                    const piece = this.board[row]?.[col];
                    return piece && piece.type === 'k' && piece.color === this.chess.turn();
                },

                isVictoryPiece(displayRow, displayCol) {
                    if (!this.chess.in_checkmate()) return false;
                    const { row, col } = this.displayToReal(displayRow, displayCol);
                    const piece = this.board[row]?.[col];
                    const winnerColor = this.chess.turn() === 'w' ? 'b' : 'w';
                    return piece && piece.type === 'k' && piece.color === winnerColor;
                },

                // Animation de capture avec particules
                playCaptureAnimation(square) {
                    const squareEl = document.querySelector(`[data-square="${square}"]`);
                    if (!squareEl) return;

                    const rect = squareEl.getBoundingClientRect();
                    const centerX = rect.left + rect.width / 2;
                    const centerY = rect.top + rect.height / 2;

                    // Créer des particules
                    const colors = ['#ef4444', '#f97316', '#eab308', '#ffffff', '#a855f7'];
                    for (let i = 0; i < 12; i++) {
                        const particle = document.createElement('div');
                        particle.className = 'capture-particle';
                        particle.style.left = centerX + 'px';
                        particle.style.top = centerY + 'px';
                        particle.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];

                        // Direction aléatoire
                        const angle = (i / 12) * Math.PI * 2;
                        const distance = 30 + Math.random() * 50;
                        particle.style.setProperty('--tx', Math.cos(angle) * distance + 'px');
                        particle.style.setProperty('--ty', Math.sin(angle) * distance + 'px');

                        document.body.appendChild(particle);

                        // Supprimer après animation
                        setTimeout(() => particle.remove(), 600);
                    }

                    // Jouer un son si disponible
                    this.playSound('capture');
                },

                // Animation de victoire avec confetti
                playVictoryAnimation() {
                    const colors = ['#22c55e', '#3b82f6', '#f59e0b', '#ec4899', '#8b5cf6', '#ffffff'];

                    for (let i = 0; i < 50; i++) {
                        setTimeout(() => {
                            const confetti = document.createElement('div');
                            confetti.className = 'confetti';
                            confetti.style.left = Math.random() * 100 + 'vw';
                            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                            confetti.style.animationDuration = (2 + Math.random() * 2) + 's';
                            confetti.style.animationDelay = Math.random() * 0.5 + 's';

                            if (Math.random() > 0.5) {
                                confetti.style.borderRadius = '0';
                                confetti.style.transform = 'rotate(' + Math.random() * 360 + 'deg)';
                            }

                            document.body.appendChild(confetti);
                            setTimeout(() => confetti.remove(), 5000);
                        }, i * 50);
                    }
                },

                // Initialiser la musique de fond
                initMusic() {
                    if (this.themeSounds?.music) {
                        this.musicPlayer = new Audio(this.themeSounds.music);
                        this.musicPlayer.loop = true;
                        this.musicPlayer.volume = this.musicVolume;
                    }
                },

                // Jouer/Pause la musique
                toggleMusic() {
                    if (!this.musicPlayer) return;

                    if (this.musicPlaying) {
                        this.musicPlayer.pause();
                    } else {
                        this.musicPlayer.play().catch(e => console.log('Autoplay bloqué'));
                    }
                    this.musicPlaying = !this.musicPlaying;
                },

                // Activer/Désactiver les sons
                toggleSound() {
                    this.soundEnabled = !this.soundEnabled;
                    // Stopper tous les sons si on désactive
                    if (!this.soundEnabled) {
                        this.stopAllSounds();
                    }
                },

                // Régler le volume de la musique
                setMusicVolume(volume) {
                    this.musicVolume = Math.max(0, Math.min(1, volume));
                    if (this.musicPlayer) {
                        this.musicPlayer.volume = this.musicVolume;
                    }
                },

                // Régler le volume des effets sonores
                setSoundVolume(volume) {
                    this.soundVolume = Math.max(0, Math.min(1, volume));
                    // Appliquer aux sons actifs
                    Object.values(this.activeSounds).forEach(audio => {
                        audio.volume = this.soundVolume;
                    });
                },

                // Tout couper
                muteAll() {
                    // Stopper la musique
                    if (this.musicPlayer && this.musicPlaying) {
                        this.musicPlayer.pause();
                        this.musicPlaying = false;
                    }
                    // Stopper et désactiver les effets
                    this.soundEnabled = false;
                    this.stopAllSounds();
                },

                // Jouer un son (avec gestion des chevauchements)
                playSound(type) {
                    if (!this.soundEnabled || !this.themeSounds) return;

                    const soundUrl = this.themeSounds[type];
                    if (soundUrl) {
                        // Stopper le son précédent du même type s'il existe
                        if (this.activeSounds[type]) {
                            this.activeSounds[type].pause();
                            this.activeSounds[type].currentTime = 0;
                        }

                        const audio = new Audio(soundUrl);
                        audio.volume = this.soundVolume;
                        this.activeSounds[type] = audio;

                        audio.play().catch(e => console.log('Son bloqué:', e));

                        // Nettoyer la référence quand le son est terminé
                        audio.addEventListener('ended', () => {
                            if (this.activeSounds[type] === audio) {
                                delete this.activeSounds[type];
                            }
                        });
                    }
                },

                // Stopper tous les sons actifs
                stopAllSounds() {
                    Object.values(this.activeSounds).forEach(audio => {
                        audio.pause();
                        audio.currentTime = 0;
                    });
                    this.activeSounds = {};
                },

                handleSquareClick(displayRow, displayCol) {
                    const { row, col } = this.displayToReal(displayRow, displayCol);
                    const piece = this.board[row]?.[col];
                    const clickedSquare = this.indexToSquare(row, col);

                    // Afficher la carte si on clique sur une pièce
                    if (piece) {
                        this.selectedCard = this.getCard(piece);
                    }

                    // Si on clique sur une case de destination valide
                    if (this.selectedSquare && this.legalMoves.some(m => m.to === clickedSquare)) {
                        this.makeMove(this.selectedSquare, { row, col });
                        return;
                    }

                    // Si on clique sur une de nos pièces (et c'est notre tour)
                    if (piece && this.isMyTurn) {
                        const pieceColor = piece.color === 'w' ? 'white' : 'black';
                        if (pieceColor === this.playerColor) {
                            this.selectedSquare = { row, col };
                            const square = this.indexToSquare(row, col);
                            this.legalMoves = this.chess.moves({ square, verbose: true });
                            return;
                        }
                    }

                    // Désélectionner
                    this.selectedSquare = null;
                    this.legalMoves = [];
                },

                async makeMove(from, to) {
                    const fromSquare = this.indexToSquare(from.row, from.col);
                    const toSquare = this.indexToSquare(to.row, to.col);
                    
                    // Vérifier promotion
                    let promotion = null;
                    const piece = this.chess.get(fromSquare);
                    if (piece && piece.type === 'p') {
                        const targetRow = piece.color === 'w' ? 0 : 7;
                        if (to.row === targetRow) {
                            promotion = prompt('Promotion: q (Dame), r (Tour), b (Fou), n (Cavalier)', 'q');
                            if (!['q', 'r', 'b', 'n'].includes(promotion)) promotion = 'q';
                        }
                    }

                    const move = this.chess.move({
                        from: fromSquare,
                        to: toSquare,
                        promotion: promotion
                    });

                    if (move) {
                        // Animation et son de capture
                        if (move.captured) {
                            this.playCaptureAnimation(toSquare);
                            this.playSound('capture');
                            const capturedPiece = (move.color === 'w' ? 'b' : 'w') + move.captured;
                            if (move.color === (this.playerColor === 'white' ? 'w' : 'b')) {
                                this.capturedByMe.push(capturedPiece);
                            } else {
                                this.capturedByOpponent.push(capturedPiece);
                            }
                        } else {
                            // Son de déplacement simple
                            this.playSound('move');
                        }

                        this.lastMove = { from: fromSquare, to: toSquare };
                        this.lastMovedSquare = toSquare;

                        // Reset l'animation après un délai
                        setTimeout(() => {
                            this.lastMovedSquare = null;
                        }, 400);

                        this.updateBoard();
                        this.updateMovesHistory(move);
                        this.updateTurn();
                        this.checkGameEnd();

                        // Sons et animations de fin
                        if (this.chess.in_checkmate()) {
                            const winnerColor = this.chess.turn() === 'w' ? 'black' : 'white';
                            this.playSound('checkmate');
                            if (winnerColor === this.playerColor) {
                                setTimeout(() => {
                                    this.playVictoryAnimation();
                                    this.playSound('victory');
                                }, 500);
                            } else {
                                setTimeout(() => this.playSound('defeat'), 500);
                            }
                        } else if (this.chess.in_check()) {
                            this.playSound('check');
                        }

                        // Envoyer au serveur
                        await this.sendMoveToServer(move);

                        // Si partie contre IA et c'est au tour de l'IA
                        if (this.isAiGame && !this.isMyTurn && this.gameStatus === 'playing') {
                            setTimeout(() => this.makeAiMove(), 800);
                        }
                    }

                    this.selectedSquare = null;
                    this.legalMoves = [];
                },

                async sendMoveToServer(move) {
                    try {
                        await fetch(`/partie/${this.gameUuid}/coup`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                from: move.from,
                                to: move.to,
                                promotion: move.promotion || null,
                                san: move.san,
                                fen: this.chess.fen(),
                                piece: move.piece,
                                captured: move.captured || null,
                                is_check: this.chess.in_check(),
                                is_checkmate: this.chess.in_checkmate(),
                                is_castling: move.flags.includes('k') || move.flags.includes('q'),
                                is_en_passant: move.flags.includes('e')
                            })
                        });
                    } catch (error) {
                        console.error('Erreur envoi coup:', error);
                    }
                },

                makeAiMove() {
                    if (this.gameStatus !== 'playing') return;
                    
                    const moves = this.chess.moves({ verbose: true });
                    if (moves.length > 0) {
                        // IA basique : priorise captures et échecs
                        let bestMoves = moves.filter(m => m.captured || m.san.includes('+'));
                        if (bestMoves.length === 0) bestMoves = moves;
                        
                        const randomMove = bestMoves[Math.floor(Math.random() * bestMoves.length)];
                        const from = this.squareToIndex(randomMove.from);
                        const to = this.squareToIndex(randomMove.to);
                        
                        this.makeMove(from, to);
                    }
                },

                updateMovesHistory(move) {
                    if (move.color === 'w') {
                        this.movesHistory.push([move.san, null]);
                    } else {
                        if (this.movesHistory.length > 0) {
                            this.movesHistory[this.movesHistory.length - 1][1] = move.san;
                        } else {
                            this.movesHistory.push(['...', move.san]);
                        }
                    }
                },

                loadMovesHistory() {
                    const history = this.chess.history();
                    this.movesHistory = [];
                    for (let i = 0; i < history.length; i += 2) {
                        this.movesHistory.push([history[i], history[i + 1] || null]);
                    }
                },

                checkGameEnd() {
                    if (this.chess.in_checkmate()) {
                        this.gameStatus = 'checkmate';
                        this.winner = this.chess.turn() === 'w' ? 'black' : 'white';
                        // L'animation de victoire est déjà gérée dans makeMove
                    } else if (this.chess.in_stalemate()) {
                        this.gameStatus = 'stalemate';
                    } else if (this.chess.in_draw() || this.chess.in_threefold_repetition()) {
                        this.gameStatus = 'draw';
                    }
                },

                handleDragStart(event, displayRow, displayCol) {
                    const { row, col } = this.displayToReal(displayRow, displayCol);
                    const piece = this.board[row]?.[col];
                    
                    if (piece && this.isMyTurn) {
                        const pieceColor = piece.color === 'w' ? 'white' : 'black';
                        if (pieceColor === this.playerColor) {
                            this.selectedSquare = { row, col };
                            const square = this.indexToSquare(row, col);
                            this.legalMoves = this.chess.moves({ square, verbose: true });
                            event.target.classList.add('dragging');
                        }
                    }
                },

                handleDragEnd(event) {
                    event.target.classList.remove('dragging');
                },

                handleDrop(event, displayRow, displayCol) {
                    const { row, col } = this.displayToReal(displayRow, displayCol);
                    const clickedSquare = this.indexToSquare(row, col);
                    
                    if (this.selectedSquare && this.legalMoves.some(m => m.to === clickedSquare)) {
                        this.makeMove(this.selectedSquare, { row, col });
                    } else {
                        this.selectedSquare = null;
                        this.legalMoves = [];
                    }
                },

                offerDraw() {
                    alert('Fonctionnalité à implémenter avec WebSocket');
                },

                // Fonctions de zoom
                zoomIn() {
                    if (this.zoomLevel < 200) {
                        this.zoomLevel += 10;
                    }
                },

                zoomOut() {
                    if (this.zoomLevel > 50) {
                        this.zoomLevel -= 10;
                    }
                },

                resetZoom() {
                    this.zoomLevel = 100;
                },

                toggleFullscreen() {
                    this.isFullscreen = !this.isFullscreen;
                    this.baseSize = this.calculateBaseSize();
                    this.zoomLevel = 100;
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
